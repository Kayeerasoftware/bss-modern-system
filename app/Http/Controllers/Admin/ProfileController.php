<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\System\AuditLog;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Loan;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch real activity data from audit_logs (fallback to empty if none)
        $activities = AuditLog::where('user', $user->name)
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();
        
        // If no audit logs, use empty collection
        if ($activities->isEmpty()) {
            $activities = collect();
        }
        
        // Count today's actions
        $todayActions = AuditLog::where('user', $user->name)
            ->whereDate('timestamp', today())
            ->count();
        
        // Count total actions
        $totalActions = AuditLog::where('user', $user->name)
            ->count();
        
        // Fetch real stats
        $totalMembers = Member::count();
        $totalTransactions = Transaction::count();
        $totalLoans = Loan::count();
        $activeLoans = Loan::where('status', 'approved')->count();
        
        return view('admin.sections.profile', compact('user', 'activities', 'todayActions', 'totalActions', 'totalMembers', 'totalTransactions', 'totalLoans', 'activeLoans'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
        ]);
        
        $user->update($request->only(['name', 'email', 'phone', 'location', 'bio']));
        
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
        
        $user->update(['password' => Hash::make($request->new_password)]);
        
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
            
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            $path = $file->store('profile_pictures', 'public');
            
            if (!$path) {
                return response()->json(['success' => false, 'message' => 'Failed to store file'], 500);
            }
            
            $user->update(['profile_picture' => $path]);
            
            if ($user->member) {
                $user->member->update(['profile_picture' => $path]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'profile_picture_url' => $user->fresh()->profile_picture_url
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
        
        $user->update(['preferences' => json_encode($preferences)]);
        
        return response()->json(['success' => true, 'message' => 'Preferences updated successfully']);
    }
}
