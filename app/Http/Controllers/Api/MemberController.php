<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Services\ProfilePictureStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
                'role' => 'required|in:client,shareholder,cashier,td,ceo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create member
            $member = new Member();
            $member->full_name = $request->full_name;
            $member->email = $request->email;
            $member->contact = $request->contact;
            $member->location = $request->location;
            $member->occupation = $request->occupation;
            $member->role = $request->role;
            $member->status = 'active';
            $member->member_id = $this->generateMemberId();
            $member->save();

            // Create user account if role requires it
            if (in_array($request->role, ['cashier', 'td', 'ceo'])) {
                $user = new User();
                $user->name = $request->full_name;
                $user->email = $request->email;
                $user->password = Hash::make('default123'); // Default password
                $user->role = $request->role;
                $user->status = 'active';
                $user->save();
            }

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
                'email' => 'required|email|unique:members,email,' . $id . '|unique:users,email,' . $id,
                'contact' => 'required|string|max:20',
                'location' => 'required|string|max:255',
                'occupation' => 'required|string|max:255',
                'role' => 'required|in:client,shareholder,cashier,td,ceo'
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
            $member->location = $request->location;
            $member->occupation = $request->occupation;
            $member->role = $request->role;

            // Update user account if role requires it
            if (in_array($request->role, ['cashier', 'td', 'ceo'])) {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    $user->name = $request->full_name;
                    $user->role = $request->role;
                    $user->save();
                }
            }

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
            $hasActiveLoans = $member->loans()->where('status', 'approved')->exists();
            $hasTransactions = $member->transactions()->exists();

            if ($hasActiveLoans || $hasTransactions) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete member with active loans or transactions'
                ], 400);
            }

            // Delete associated user account
            $user = User::where('email', $member->email)->first();
            if ($user) {
                $user->delete();
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
                return Member::where('member_id', $memberId)
                    ->orWhere('id', $memberId)
                    ->select([
                        'id', 'member_id', 'full_name', 'email', 'contact', 'nin_no', 'dob',
                        'nationality', 'marital_status', 'spouse_name', 'spouse_nin',
                        'next_of_kin', 'next_of_kin_nin', 'father_name', 'mother_name',
                        'occupation', 'about_yourself', 'present_region', 'present_district',
                        'present_county', 'present_subcounty', 'present_ward', 'present_village'
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
                'nin_no' => $member->nin_no ?? '',
                'dob' => $member->dob ?? '',
                'nationality' => $member->nationality ?? 'Ugandan',
                'marital_status' => $member->marital_status ?? '',
                'spouse_name' => $member->spouse_name ?? '',
                'spouse_nin' => $member->spouse_nin ?? '',
                'next_of_kin' => $member->next_of_kin ?? '',
                'next_of_kin_nin' => $member->next_of_kin_nin ?? '',
                'father_name' => $member->father_name ?? '',
                'mother_name' => $member->mother_name ?? '',
                'occupation' => $member->occupation ?? '',
                'about_yourself' => $member->about_yourself ?? '',
                'present_region' => $member->present_region ?? '',
                'present_district' => $member->present_district ?? '',
                'present_county' => $member->present_county ?? '',
                'present_subcounty' => $member->present_subcounty ?? '',
                'present_ward' => $member->present_ward ?? '',
                'present_village' => $member->present_village ?? ''
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
        $prefix = 'MEM';
        $year = date('y');
        $count = Member::count() + 1;
        $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);
        return $prefix . $year . $sequence;
    }
}
