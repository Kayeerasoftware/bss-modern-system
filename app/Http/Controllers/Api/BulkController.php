<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class BulkController extends Controller
{
    public function importMembers(Request $request)
    {
        try {
            $file = $request->file('file');
            
            if (!$file) {
                return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
            }

            if ($file->getClientOriginalExtension() !== 'csv') {
                return response()->json(['success' => false, 'message' => 'Invalid file format. Please upload a CSV file'], 400);
            }

            $csv = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($csv);
            
            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($csv as $index => $row) {
                if (count($row) < 7) {
                    $errors[] = "Row " . ($index + 2) . ": Insufficient columns";
                    continue;
                }
                
                $data = array_combine($header, $row);
                
                // Skip if email exists
                if (Member::where('email', $data['email'])->exists()) {
                    $skipped++;
                    continue;
                }
                
                // Auto-generate member_id
                $memberCount = Member::count();
                $memberId = 'BSS' . str_pad($memberCount + 1, 3, '0', STR_PAD_LEFT);
                
                Member::create([
                    'member_id' => $memberId,
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'contact' => $data['contact'],
                    'location' => $data['location'] ?? '',
                    'occupation' => $data['occupation'] ?? '',
                    'role' => $data['role'],
                    'savings' => $data['savings'] ?? 0,
                    'loan' => 0,
                    'balance' => $data['savings'] ?? 0,
                    'savings_balance' => 0,
                    'password' => Hash::make('password123')
                ]);
                
                $imported++;
            }

            return response()->json([
                'success' => true,
                'imported' => $imported,
                'skipped' => $skipped,
                'message' => "Imported $imported members" . ($skipped > 0 ? ", skipped $skipped duplicates" : '')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function exportMembers()
    {
        $members = Member::all();
        
        $csv = "member_id,full_name,email,contact,location,occupation,role,savings\n";
        
        foreach ($members as $member) {
            $csv .= implode(',', [
                $member->member_id,
                $member->full_name,
                $member->email,
                $member->contact,
                $member->location ?? '',
                $member->occupation ?? '',
                $member->role,
                $member->savings
            ]) . "\n";
        }
        
        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="members_export_' . date('Y-m-d') . '.csv"');
    }
}
