<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Models\BioData;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Services\ImageService;
use App\Services\Financial\MemberFinancialSyncService;
use App\Services\Member\MemberDeletionService;
use App\Services\ProfilePictureStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    protected ImageService $imageService;
    protected MemberDeletionService $memberDeletionService;

    public function __construct(ImageService $imageService, MemberDeletionService $memberDeletionService)
    {
        $this->imageService = $imageService;
        $this->memberDeletionService = $memberDeletionService;
    }

    public function index(Request $request)
    {
        $trashFilter = (string) $request->get('trash', 'with');
        $savingsBalanceSubquery = DB::table('savings_accounts')
            ->selectRaw('member_id, COALESCE(SUM(current_balance), 0) as savings_account_balance')
            ->groupBy('member_id');

        $query = Member::query()
            ->with('user')
            ->leftJoinSub($savingsBalanceSubquery, 'member_savings_accounts', 'members.id', '=', 'member_savings_accounts.member_id')
            ->select('members.*')
            ->addSelect(DB::raw('COALESCE(member_savings_accounts.savings_account_balance, 0) as savings_account_balance'));

        if ($trashFilter === 'only') {
            $query->onlyTrashed();
        } elseif ($trashFilter === 'with') {
            $query->withTrashed();
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) LIKE ?', ["%{$request->search}%"])
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('member_number', 'like', "%{$request->search}%")
                  ->orWhere('member_account_number', 'like', "%{$request->search}%")
                  ->orWhere('primary_phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', $request->role));
        }

        if ($request->status) {
            $query->where('membership_status', $request->status);
        }

        if ($request->savings_min || $request->savings_max) {
            if ($request->savings_min) {
                $query->whereRaw('COALESCE(member_savings_accounts.savings_account_balance, 0) >= ?', [(float) $request->savings_min]);
            }
            
            if ($request->savings_max) {
                $query->whereRaw('COALESCE(member_savings_accounts.savings_account_balance, 0) <= ?', [(float) $request->savings_max]);
            }
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->sort) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderByRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) ASC');
                    break;
                case 'name_desc':
                    $query->orderByRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) DESC');
                    break;
                case 'savings_high':
                    $query->orderByRaw('COALESCE(member_savings_accounts.savings_account_balance, 0) desc');
                    break;
                case 'savings_low':
                    $query->orderByRaw('COALESCE(member_savings_accounts.savings_account_balance, 0) asc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $statsBaseQuery = clone $query;
        
        // Calculate total savings directly from completed transactions.
        $totalSavings = (float) DB::table('savings_accounts')->sum('current_balance');
        
        $memberStats = [
            'totalMembers' => (clone $statsBaseQuery)->count(),
            'activeMembers' => (clone $statsBaseQuery)->where('membership_status', 'active')->count(),
            'totalSavings' => $totalSavings,
            'newThisMonth' => (clone $statsBaseQuery)->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        $perPage = (int) $request->get('per_page', 100);
        if ($perPage < 10) {
            $perPage = 10;
        }
        if ($perPage > 500) {
            $perPage = 500;
        }

        $members = $query->paginate($perPage)->appends($request->query());
        
        if ($request->ajax()) {
            return view('admin.members.partials.table', compact('members', 'trashFilter'))->render();
        }
        
        return view('admin.members.index', compact('members', 'trashFilter', 'memberStats'));
    }

    public function create()
    {
        $nextMemberId = generate_member_id();
        
        return view('admin.members.create', compact('nextMemberId'));
    }

    public function store(StoreMemberRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->except('profile_picture', 'roles', 'default_role');
            $selectedRoles = array_values((array) $request->input('roles', []));
            $primaryRole = strtolower((string) $request->input('default_role', 'client'));
            if (!in_array($primaryRole, $selectedRoles, true)) {
                return back()->withErrors([
                    'default_role' => 'Default role must be one of the selected roles.'
                ])->withInput();
            }
            $data['password'] = Hash::make($request->password);
            $data['member_number'] = generate_member_id(); // Legacy field
            $data['member_account_number'] = \App\Services\System\AccountNumberService::generateMemberAccountNumber();
            $data['membership_status'] = 'active';

            if ($request->hasFile('profile_picture')) {
                if ($request->file('profile_picture')->isValid()) {
                    $data['profile_picture'] = ProfilePictureStorageService::storeProfilePicture(
                        $request->file('profile_picture')
                    );
                }
            }
            $openingSavings = (float) max((float) ($data['savings'] ?? 0), (float) ($data['balance'] ?? 0));

            // Create user first
            $user = User::withoutEvents(function () use ($data, $primaryRole) {
                return User::create([
                    'username' => $data['full_name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'role' => $primaryRole,
                    'status' => 'active',
                ]);
            });

            // Create member linked to user
            $member = new Member();
            $member->user_id = $user->id;
            $member->member_number = $data['member_number'];
            $member->member_account_number = $data['member_account_number'];
            $member->full_name = $data['full_name'];
            $member->email = $data['email'];
            $member->primary_phone = $data['contact'] ?? null;
            $member->place_of_birth = $data['location'] ?? null;
            $member->occupation = $data['occupation'] ?? null;
            $member->membership_status = $data['membership_status'];
            Member::queueOpeningSavings($member, $openingSavings);
            $member->join_date = now()->toDateString();
            if (!empty($data['profile_picture'])) {
                $member->profile_picture = $data['profile_picture'];
            }
            $member->save();

            // Assign multiple roles
            if (!empty($selectedRoles)) {
                $member->syncRoles($selectedRoles);
                $user->syncRoles($selectedRoles);
            }

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', 'Member created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create member: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id, MemberFinancialSyncService $financialSyncService)
    {
        $member = Member::with(['loans', 'transactions', 'shares', 'user'])->findOrFail($id);
        $financialSummary = $financialSyncService->getMemberFinancialSummary($member);

        return view('admin.members.show', compact('member', 'financialSummary'));
    }

    public function edit($id)
    {
        $member = Member::findOrFail($id);
        return view('admin.members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $member = Member::findOrFail($id);
            $selectedRoles = array_values((array) $request->input('roles', []));
            $primaryRole = strtolower((string) $request->input('default_role', 'client'));
            if (!in_array($primaryRole, $selectedRoles, true)) {
                return back()->withErrors([
                    'default_role' => 'Default role must be one of the selected roles.'
                ])->withInput();
            }
            $data = $request->only(['full_name', 'email', 'contact', 'location', 'occupation']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('profile_picture')) {
                if ($request->file('profile_picture')->isValid()) {
                    $data['profile_picture'] = ProfilePictureStorageService::storeProfilePicture(
                        $request->file('profile_picture'),
                        $member->profile_picture
                    );
                }
            }

            if (!empty($data['full_name'])) {
                $member->full_name = $data['full_name'];
            }
            if (!empty($data['email'])) {
                $member->email = $data['email'];
            }
            $member->primary_phone = $data['contact'] ?? $member->primary_phone;
            $member->place_of_birth = $data['location'] ?? $member->place_of_birth;
            $member->occupation = $data['occupation'] ?? $member->occupation;
            if (isset($data['profile_picture'])) {
                $member->profile_picture = $data['profile_picture'];
            }
            $member->save();

            // Sync data to user
            if ($member->user) {
                $userData = [
                    'username' => $member->full_name,
                    'email' => $member->email,
                    'role' => $primaryRole,
                ];
                if (isset($data['profile_picture'])) {
                    $userData['profile_picture'] = $data['profile_picture'];
                }
                if (isset($data['password'])) {
                    $userData['password'] = $data['password'];
                }
                $member->user->update($userData);
                if (!empty($selectedRoles)) {
                    $member->user->syncRoles($selectedRoles);
                }
            }

            // Sync roles
            if (!empty($selectedRoles)) {
                $member->syncRoles($selectedRoles);
            }

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', 'Member updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update member: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        // Soft-delete member to allow recovery.
        $member->delete();

        // Deactivate associated user while member is in trash.
        if ($member->user) {
            $member->user->update(['is_active' => false]);
        }

        return redirect()->route('admin.members.index')->with('success', 'Member moved to trash successfully');
    }

    public function restore($id)
    {
        $member = Member::withTrashed()->findOrFail($id);

        if (!$member->trashed()) {
            return redirect()->route('admin.members.index')->with('error', 'Member is not deleted.');
        }

        $member->restore();

        if ($member->user) {
            $member->user->update(['is_active' => true]);
        }

        return redirect()->route('admin.members.index', ['trash' => 'only'])->with('success', 'Member restored successfully');
    }

    public function forceDelete($id)
    {
        $member = Member::withTrashed()->findOrFail($id);

        if (!$member->trashed()) {
            return redirect()->route('admin.members.index')->with('error', 'Only trashed members can be permanently deleted.');
        }

        DB::beginTransaction();
        try {
            $user = $member->user;
            $memberPrimaryKey = $member->id;
            $this->memberDeletionService->purgeDependencies($memberPrimaryKey);
            $member->forceDelete();

            // Keep user/member sync: remove user if it was linked only to this member.
            if ($user) {
                $hasRemainingMemberLink = Member::withTrashed()
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$hasRemainingMemberLink) {
                    $user->delete();
                }
            }

            DB::commit();
            return redirect()->route('admin.members.index', ['trash' => 'only'])->with('success', 'Member permanently deleted.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.members.index', ['trash' => 'only'])->with('error', 'Permanent delete failed: ' . $e->getMessage());
        }
    }

    public function viewBioData($id)
    {
        $member = Member::with('bioData')->findOrFail($id);
        return view('admin.members.bio-data-view', compact('member'));
    }

    public function createBioData()
    {
        return view('admin.members.bio-data');
    }

    public function storeBioData(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'full_name' => 'required|string|max:255',
            'nin_no' => 'required|string|max:14',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
            'dob' => 'required|string',
            'nationality' => 'nullable|string',
            'marital_status' => 'required|string',
            'spouse_name' => 'nullable|string',
            'spouse_nin' => 'nullable|string|max:14',
            'next_of_kin' => 'nullable|string',
            'next_of_kin_nin' => 'nullable|string|max:14',
            'father_name' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'children' => 'nullable|array',
            'occupation' => 'nullable|string',
            'signature' => 'required|string',
            'declaration_date' => 'required|date',
        ]);

        $validated['present_address'] = [
            'region' => $request->present_region,
            'district' => $request->present_district,
            'county' => $request->present_county,
            'subcounty' => $request->present_subcounty,
            'ward' => $request->present_ward,
            'village' => $request->present_village,
        ];

        $validated['permanent_address'] = [
            'region' => $request->permanent_region,
            'district' => $request->permanent_district,
            'county' => $request->permanent_county,
            'subcounty' => $request->permanent_subcounty,
            'ward' => $request->permanent_ward,
            'village' => $request->permanent_village,
        ];

        $validated['birth_place'] = [
            'region' => $request->birth_region,
            'district' => $request->birth_district,
            'county' => $request->birth_county,
            'subcounty' => $request->birth_subcounty,
            'ward' => $request->birth_ward,
            'village' => $request->birth_village,
        ];

        BioData::create($validated);

        return redirect()->route('admin.members.index')->with('success', 'Bio data created successfully');
    }

    /**
     * Upload or update member profile picture
     */
    public function uploadPicture(Request $request, $id): JsonResponse
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
        ]);

        try {
            $member = Member::findOrFail($id);
            
            $picturePath = $this->imageService->uploadMemberPicture(
                $request->file('profile_picture'),
                $member->profile_picture
            );

            $member->update(['profile_picture' => $picturePath]);
            
            // Update associated user
            if ($member->user) {
                $member->user->update(['profile_picture' => $picturePath]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'picture_info' => $this->imageService->getImageInfo($picturePath)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete member profile picture
     */
    public function deletePicture($id): JsonResponse
    {
        try {
            $member = Member::findOrFail($id);
            
            if ($member->profile_picture) {
                $this->imageService->deletePicture($member->profile_picture);
                
                $member->update(['profile_picture' => null]);
                
                // Update associated user
                if ($member->user) {
                    $member->user->update(['profile_picture' => null]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile picture deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get member picture info
     */
    public function getPictureInfo($id): JsonResponse
    {
        try {
            $member = Member::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'picture_info' => $this->imageService->getImageInfo($member->profile_picture)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get picture info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search members for dropdown - comprehensive search
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $membersQuery = Member::query();
        
        if (strlen($query) >= 2) {
            $membersQuery->where(function($q) use ($query) {
                $q->whereRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) LIKE ?', ["%{$query}%"])
                  ->orWhere('member_number', 'like', "%{$query}%")
                  ->orWhere('member_account_number', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('primary_phone', 'like', "%{$query}%")
                  ->orWhere('place_of_birth', 'like', "%{$query}%")
                  ->orWhere('occupation', 'like', "%{$query}%")
                  ->orWhere('membership_status', 'like', "%{$query}%")
                  ->orWhereHas('roles', function ($roleQuery) use ($query) {
                      $roleQuery->where('name', 'like', "%{$query}%");
                  });
            });
        }
        
        $members = $membersQuery->select('id', 'first_name', 'middle_name', 'last_name', 'member_number', 'member_account_number', 'email', 'primary_phone', 'place_of_birth', 'occupation', 'membership_status')
            ->orderByRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, ""))')
            ->limit(50)
            ->get()
            ->each
            ->append(['member_id', 'contact', 'status', 'role']);
        
        return response()->json($members);
    }

    /**
     * Get member details for form population
     */
    public function details($id)
    {
        $member = Member::with('bioData')->findOrFail($id);
        
        // If member has bio data, merge it with member data
        $data = $member->toArray();
        if ($member->bioData) {
            $bioData = $member->bioData->toArray();
            $data = array_merge($data, $bioData);
        }
        
        return response()->json($data);
    }

    /**
     * Bulk upload pictures
     */
    public function bulkUploadPictures(Request $request): JsonResponse
    {
        $request->validate([
            'pictures' => 'required|array|max:10',
            'pictures.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'member_ids' => 'required|array',
            'member_ids.*' => 'required|exists:members,id'
        ]);

        try {
            DB::beginTransaction();
            
            $results = [];
            $pictures = $request->file('pictures');
            $memberIds = $request->input('member_ids');
            
            foreach ($pictures as $index => $picture) {
                if (isset($memberIds[$index])) {
                    $member = Member::find($memberIds[$index]);
                    if ($member) {
                        $picturePath = $this->imageService->uploadMemberPicture(
                            $picture,
                            $member->profile_picture
                        );
                        
                        $member->update(['profile_picture' => $picturePath]);
                        
                        if ($member->user) {
                            $member->user->update(['profile_picture' => $picturePath]);
                        }
                        
                        $results[] = [
                            'member_id' => $member->id,
                            'member_name' => $member->full_name,
                            'success' => true,
                            'picture_info' => $this->imageService->getImageInfo($picturePath)
                        ];
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pictures uploaded successfully',
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload pictures: ' . $e->getMessage()
            ], 500);
        }
    }
}
