<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\LoanStatus;
use App\Models\User;
use App\Services\ProfilePictureStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $members = Member::with(['loans'])
                ->get();

            return response()->json([
                'success' => true,
                'members' => $members,
                'total_members' => $members->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading members: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:members,email|unique:users,email',
                'contact' => 'required|string|max:20',
                'location' => 'required|string|max:255',
                'occupation' => 'required|string|max:255',
                'role' => 'required|in:client,shareholder,cashier,td,ceo,admin',
                'password' => 'nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create member
            $user = User::withoutEvents(function () use ($request) {
                return User::create([
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->input('password', 'password123')),
                    'role' => $request->role,
                    'status' => 'active',
                    'is_active' => true,
                    'phone' => $request->contact,
                    'location' => $request->location,
                ]);
            });

            $member = new Member();
            $member->full_name = $request->full_name;
            $member->email = $request->email;
            $member->contact = $request->contact;
            $member->place_of_birth = $request->location;
            $member->occupation = $request->occupation;
            $member->membership_status = 'active';
            Member::queueOpeningSavings($member, (float) $request->input('savings', 0));
            $memberNumber = $this->generateMemberId();
            $member->member_number = $memberNumber;
            $member->member_account_number = $memberNumber;
            $member->user_id = $user->id;
            $member->join_date = now()->toDateString();
            $member->created_by = auth()->id() ?? $user->id;
            $member->save();
            $member->assignRole($request->role);

            return response()->json([
                'success' => true,
                'message' => 'Member created successfully',
                'member' => $member
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $member = Member::findOrFail($id);

            // Validate request
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:members,email,' . $id . '|unique:users,email,' . ($member->user_id ?? 0),
                'contact' => 'required|string|max:20',
                'location' => 'required|string|max:255',
                'occupation' => 'required|string|max:255',
                'role' => 'required|in:client,shareholder,cashier,td,ceo,admin'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update member
            $member->full_name = $request->full_name;
            $member->email = $request->email;
            $member->contact = $request->contact;
            $member->place_of_birth = $request->location;
            $member->occupation = $request->occupation;
            $member->assignRole($request->role);

            $member->save();

            return response()->json([
                'success' => true,
                'message' => 'Member updated successfully',
                'member' => $member
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload member profile picture
     */
    public function uploadPicture(Request $request, $id)
    {
        try {
            $member = Member::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file'
                ], 422);
            }

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $path = ProfilePictureStorageService::storeProfilePicture($file, $member->profile_picture, 'bss/profile_pictures/members');
                $member->profile_picture = $path;
                $member->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Picture uploaded successfully',
                    'path' => $path
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $member = Member::findOrFail($id);

            // Check if member has active loans or transactions
            $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
            $hasActiveLoans = $approvedStatusId
                ? $member->loans()->where('status_id', $approvedStatusId)->exists()
                : false;
            $hasTransactions = $member->transactions()->exists();

            if ($hasActiveLoans || $hasTransactions) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete member with active loans or transactions'
                ], 400);
            }

            $member->delete();

            return response()->json([
                'success' => true,
                'message' => 'Member deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search member by ID
     */
    public function searchById($memberId)
    {
        try {
            // Use caching for frequently searched members
            $member = Cache::remember("member_search_{$memberId}", 300, function () use ($memberId) {
                $resolvedMemberId = resolve_member_id($memberId);
                return Member::query()
                    ->where('id', $resolvedMemberId ?? -1)
                    ->orWhere('member_account_number', $memberId)
                    ->orWhere('member_number', $memberId)
                    ->select([
                        'id',
                        'member_account_number',
                        'member_number',
                        'full_name',
                        'email',
                        'primary_phone',
                        'date_of_birth',
                        'nationality_id',
                        'occupation',
                        'place_of_birth',
                        'membership_status',
                    ])
                    ->first();
            });

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'id' => $member->id,
                'member_id' => $member->member_id,
                'full_name' => $member->full_name,
                'name' => $member->full_name,
                'email' => $member->email,
                'telephone' => $member->contact,
                'phone' => $member->contact,
                'nin_no' => '',
                'dob' => $member->date_of_birth ?? '',
                'nationality' => 'Ugandan',
                'marital_status' => '',
                'spouse_name' => '',
                'spouse_nin' => '',
                'next_of_kin' => '',
                'next_of_kin_nin' => '',
                'father_name' => '',
                'mother_name' => '',
                'occupation' => $member->occupation ?? '',
                'about_yourself' => $member->notes ?? '',
                'present_region' => '',
                'present_district' => '',
                'present_county' => '',
                'present_subcounty' => '',
                'present_ward' => '',
                'present_village' => ''
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique member ID
     */
    private function generateMemberId()
    {
        return generate_member_id();
    }
}
