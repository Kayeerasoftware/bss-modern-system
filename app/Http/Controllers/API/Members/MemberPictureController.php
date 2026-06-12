<?php

namespace App\Http\Controllers\Api\Members;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MemberPictureController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Upload member profile picture
     */
    public function upload(Request $request, $memberId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $member = Member::findOrFail($memberId);
            
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
                'message' => 'Profile picture uploaded successfully',
                'data' => [
                    'member_id' => $member->id,
                    'picture_path' => $picturePath,
                    'picture_info' => $this->imageService->getImageInfo($picturePath)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update member profile picture
     */
    public function update(Request $request, $memberId): JsonResponse
    {
        return $this->upload($request, $memberId);
    }

    /**
     * Delete member profile picture
     */
    public function delete($memberId): JsonResponse
    {
        try {
            $member = Member::findOrFail($memberId);
            
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
     * Get member picture information
     */
    public function show($memberId): JsonResponse
    {
        try {
            $member = Member::findOrFail($memberId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'member_id' => $member->id,
                    'member_name' => $member->full_name,
                    'picture_info' => $this->imageService->getImageInfo($member->profile_picture)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get picture info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk upload pictures for multiple members
     */
    public function bulkUpload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'uploads' => 'required|array|max:20',
            'uploads.*.member_id' => 'required|exists:members,id',
            'uploads.*.picture' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $results = [];
            $uploads = $request->input('uploads');
            
            foreach ($uploads as $index => $upload) {
                $member = Member::find($upload['member_id']);
                if ($member && $request->hasFile("uploads.{$index}.picture")) {
                    try {
                        $picturePath = $this->imageService->uploadMemberPicture(
                            $request->file("uploads.{$index}.picture"),
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
                    } catch (\Exception $e) {
                        $results[] = [
                            'member_id' => $member->id,
                            'member_name' => $member->full_name,
                            'success' => false,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Bulk upload completed',
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process bulk upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get picture statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalMembers = Member::count();
            $membersWithPictures = Member::whereNotNull('profile_picture')->count();
            $membersWithoutPictures = $totalMembers - $membersWithPictures;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_members' => $totalMembers,
                    'members_with_pictures' => $membersWithPictures,
                    'members_without_pictures' => $membersWithoutPictures,
                    'completion_percentage' => $totalMembers > 0 ? round(($membersWithPictures / $totalMembers) * 100, 2) : 0
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
