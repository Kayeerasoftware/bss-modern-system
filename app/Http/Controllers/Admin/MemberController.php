<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Models\BioData;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with('user');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('member_id', 'like', "%{$request->search}%")
                  ->orWhere('contact', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->savings_min) {
            $query->where('savings', '>=', $request->savings_min);
        }

        if ($request->savings_max) {
            $query->where('savings', '<=', $request->savings_max);
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
                    $query->orderBy('full_name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('full_name', 'desc');
                    break;
                case 'savings_high':
                    $query->orderBy('savings', 'desc');
                    break;
                case 'savings_low':
                    $query->orderBy('savings', 'asc');
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

        $members = $query->paginate(15);
        
        if ($request->ajax()) {
            return view('admin.members.partials.table', compact('members'))->render();
        }
        
        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        // Calculate next member ID
        $lastMember = Member::withTrashed()
            ->where('member_id', 'like', 'BSS-C15-%')
            ->orderBy('member_id', 'desc')
            ->first();
        
        if ($lastMember && preg_match('/BSS-C15-(\d+)/', $lastMember->member_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        $nextMemberId = 'BSS-C15-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return view('admin.members.create', compact('nextMemberId'));
    }

    public function store(StoreMemberRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->except('profile_picture');

            // Generate member ID in format: BSS-C15-000x
            $lastMember = Member::withTrashed()
                ->where('member_id', 'like', 'BSS-C15-%')
                ->orderBy('member_id', 'desc')
                ->first();
            
            if ($lastMember && preg_match('/BSS-C15-(\d+)/', $lastMember->member_id, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            
            $data['member_id'] = 'BSS-C15-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $data['password'] = Hash::make($request->password);
            $data['savings'] = $request->savings ?? 0;
            $data['balance'] = $request->balance ?? 0;
            $data['savings_balance'] = $request->savings ?? 0;
            $data['loan'] = 0;
            $data['status'] = 'active';
            $data['role'] = $request->roles[0] ?? 'client';

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                if ($file->isValid()) {
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/members'), $filename);
                    $data['profile_picture'] = 'uploads/members/' . $filename;
                }
            }

            // Create user first
            $user = User::create([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'],
                'phone' => $data['contact'] ?? null,
                'location' => $data['location'] ?? null,
                'profile_picture' => $data['profile_picture'] ?? null,
                'is_active' => true,
            ]);

            // Create member linked to user
            $data['user_id'] = $user->id;
            $member = Member::create($data);

            // Assign multiple roles
            if ($request->has('roles')) {
                $member->syncRoles($request->roles);
            }

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', 'Member created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create member: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $member = Member::with(['loans', 'transactions', 'shares', 'user'])->findOrFail($id);
        return view('admin.members.show', compact('member'));
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
            $data = $request->only(['full_name', 'email', 'contact', 'location', 'occupation']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                if ($file->isValid()) {
                    if ($member->profile_picture) {
                        @unlink(public_path($member->profile_picture));
                    }
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/members'), $filename);
                    $data['profile_picture'] = 'uploads/members/' . $filename;
                }
            }

            $member->update($data);

            // Sync data to user
            if ($member->user) {
                $userData = [
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'phone' => $member->contact,
                    'location' => $member->location,
                ];
                if (isset($data['profile_picture'])) {
                    $userData['profile_picture'] = $data['profile_picture'];
                }
                if (isset($data['password'])) {
                    $userData['password'] = $data['password'];
                }
                $member->user->update($userData);
            }

            // Sync roles
            if ($request->has('roles')) {
                $member->syncRoles($request->roles);
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
        
        if ($member->profile_picture) {
            @unlink(public_path($member->profile_picture));
        }

        // Delete associated user
        if ($member->user) {
            $member->user->delete();
        } else {
            $member->delete();
        }

        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully');
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
                $q->where('full_name', 'like', "%{$query}%")
                  ->orWhere('member_id', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('contact', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%")
                  ->orWhere('occupation', 'like', "%{$query}%")
                  ->orWhere('status', 'like', "%{$query}%")
                  ->orWhere('role', 'like', "%{$query}%");
            });
        }
        
        $members = $membersQuery->select('id', 'full_name', 'member_id', 'email', 'contact', 'location', 'occupation', 'status', 'role')
            ->orderBy('full_name')
            ->limit(50)
            ->get();
        
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
