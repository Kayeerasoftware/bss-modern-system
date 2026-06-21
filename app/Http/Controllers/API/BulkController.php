<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\System\AccountNumberService;

class BulkController extends Controller
{
    private function generateMemberId(): string
    {
        return AccountNumberService::generateMemberAccountNumber();
    }

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
                
                $role = strtolower(trim((string) ($data['role'] ?? 'client')));
                if (!in_array($role, ['admin', 'client', 'cashier', 'td', 'ceo', 'shareholder'], true)) {
                    $role = 'client';
                }
                
                $user = User::withoutEvents(function () use ($data, $role) {
                    return User::create([
                        'name' => $data['full_name'],
                        'email' => $data['email'],
                        'password' => Hash::make('password123'),
                        'role' => $role,
                        'status' => 'active',
                        'is_active' => true,
                        'phone' => $data['contact'] ?? null,
                        'location' => $data['location'] ?? null,
                    ]);
                });

                $memberNumber = $this->generateMemberId();
                $member = new Member();
                $member->member_number = $memberNumber;
                $member->member_account_number = $memberNumber;
                $member->full_name = $data['full_name'];
                $member->email = $data['email'];
                $member->primary_phone = $data['contact'];
                $member->place_of_birth = $data['location'] ?? '';
                $member->occupation = $data['occupation'] ?? '';
                $member->password = $user->password;
                $member->user_id = $user->id;
                $member->membership_status = 'active';
                $member->join_date = now()->toDateString();
                Member::queueOpeningSavings($member, (float) ($data['savings'] ?? 0));
                $member->save();
                $member->assignRole($role);
                
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
