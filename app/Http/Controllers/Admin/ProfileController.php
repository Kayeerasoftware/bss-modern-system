<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\System\AuditLog;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Loan;
use App\Models\LoanStatus;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('member'); // Eager load member to prevent N+1
        
        // Combine audit log queries into one
        $auditStats = DB::table('audit_logs')
            ->where('user_id', $user->id)
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today')
            ->first();
        
        $todayActions = $auditStats->today ?? 0;
        $totalActions = $auditStats->total ?? 0;
        
        // Fetch activities separately (only if needed)
        $activities = AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Combine all count queries into one
        $stats = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM members) as total_members,
                (SELECT COUNT(*) FROM transactions) as total_transactions,
                (SELECT COUNT(*) FROM loans) as total_loans,
                (SELECT COUNT(*) FROM loans WHERE status_id = (SELECT id FROM loan_statuses WHERE name = 'approved' LIMIT 1)) as active_loans
        ")[0];
        
        $totalMembers = $stats->total_members;
        $totalTransactions = $stats->total_transactions;
        $totalLoans = $stats->total_loans;
        $activeLoans = $stats->active_loans;
        
        return view('admin.sections.profile', compact('user', 'activities', 'todayActions', 'totalActions', 'totalMembers', 'totalTransactions', 'totalLoans', 'activeLoans'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
        ]);
        
        // Update through the model so username uniqueness logic stays consistent
        $user->username = $validated['name'];
        $user->email = $validated['email'];
        $user->save();
        
        // Update member if exists
        if ($user->member) {
            DB::table('members')->where('id', $user->member->id)->update([
                'email' => $validated['email'],
                'primary_phone' => $validated['phone'],
                'place_of_birth' => $validated['location'],
                'notes' => $validated['bio'],
                'updated_at' => now(),
            ]);
        }
        
        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }
    
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 422);
        }
        
        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->new_password),
            'updated_at' => now(),
        ]);
        
        return response()->json(['success' => true, 'message' => 'Password updated successfully']);
    }
    
    public function uploadProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $user = Auth::user();
            
            if (!$request->hasFile('profile_picture')) {
                return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
            }
            
            $file = $request->file('profile_picture');
            
            if (!$file->isValid()) {
                return response()->json(['success' => false, 'message' => 'Invalid file'], 400);
            }
            
            // Delete old picture if exists
            $oldPath = DB::table('users')->where('id', $user->id)->value('profile_picture');
            if ($oldPath && Storage::disk('uploads')->exists($oldPath)) {
                Storage::disk('uploads')->delete($oldPath);
            }
            
            // Store new picture
            $path = $file->store('profile_pictures', 'uploads');
            
            if (!$path) {
                return response()->json(['success' => false, 'message' => 'Failed to store file'], 500);
            }
            
            // Update user profile picture directly in database
            DB::table('users')->where('id', $user->id)->update([
                'profile_picture' => $path,
                'updated_at' => now(),
            ]);
            
            // Update member if exists
            if ($user->member) {
                DB::table('members')->where('id', $user->member->id)->update([
                    'profile_picture' => $path,
                    'updated_at' => now(),
                ]);
            }
            
            // Return the full URL
            $fullUrl = asset('uploads/' . $path);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'profile_picture_url' => $fullUrl,
                'path' => $path
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile picture upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }
    
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $preferences = [
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'dark_mode' => $request->boolean('dark_mode'),
        ];
        
        // Update member preferences if exists
        if ($user->member) {
            DB::table('members')->where('id', $user->member->id)->update([
                'communication_preferences' => json_encode($preferences),
                'updated_at' => now(),
            ]);
        }
        
        return response()->json(['success' => true, 'message' => 'Preferences updated successfully']);
    }
}
