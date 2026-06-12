<?php

namespace App\Http\Controllers;

use App\Models\BioData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BioDataController extends Controller
{
    public function index()
    {
        return view('modern-bio-form');
    }

    public function store(Request $request)
    {
        try {
            // Enhanced validation with custom rules
            $validated = $request->validate([
                'full_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'nin_no' => 'required|string|size:14|unique:bio_data,nin_no|regex:/^[A-Z0-9]+$/',
                'dob' => 'required|date|before:today|after:1900-01-01',
                'marital_status' => 'required|in:Single,Married,Divorced,Widowed',
                'signature' => 'required|string|max:255',
                'declaration_date' => 'required|date|before_or_equal:today',
                'email' => 'nullable|email|max:255',
                'nationality' => 'nullable|string|max:100',
                'occupation' => 'nullable|string|max:255',
                'spouse_name' => 'nullable|string|max:255',
                'spouse_nin' => 'nullable|string|size:14',
                'father_name' => 'nullable|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'next_of_kin' => 'nullable|string|max:255',
                'next_of_kin_nin' => 'nullable|string|size:14',
                'telephone' => 'nullable|array|max:3',
                'telephone.*' => 'nullable|string|regex:/^\+?[0-9]{10,15}$/',
                'children' => 'nullable|array|max:20',
                'children.*.name' => 'required_with:children|string|max:255',
                'children.*.age' => 'required_with:children|integer|min:0|max:50'
            ], [
                'nin_no.unique' => 'This National ID number is already registered.',
                'nin_no.size' => 'National ID must be exactly 14 characters.',
                'full_name.regex' => 'Full name should only contain letters and spaces.',
                'telephone.*.regex' => 'Please enter a valid phone number.',
                'dob.before' => 'Date of birth must be in the past.',
                'dob.after' => 'Please enter a valid date of birth.'
            ]);

            DB::beginTransaction();

            // Process structured data
            $bioData = [
                'full_name' => $validated['full_name'],
                'nin_no' => strtoupper($validated['nin_no']),
                'dob' => $validated['dob'],
                'marital_status' => $validated['marital_status'],
                'signature' => $validated['signature'],
                'declaration_date' => $validated['declaration_date'],
                'email' => $validated['email'] ?? null,
                'nationality' => $validated['nationality'] ?? 'Ugandan',
                'occupation' => $validated['occupation'] ?? null,
                'spouse_name' => $validated['spouse_name'] ?? null,
                'spouse_nin' => $validated['spouse_nin'] ? strtoupper($validated['spouse_nin']) : null,
                'father_name' => $validated['father_name'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'next_of_kin' => $validated['next_of_kin'] ?? null,
                'next_of_kin_nin' => $validated['next_of_kin_nin'] ? strtoupper($validated['next_of_kin_nin']) : null,
            ];

            // Process address data with validation
            $bioData['present_address'] = $this->processAddress($request, 'present');
            $bioData['permanent_address'] = $this->processAddress($request, 'permanent');
            $bioData['birth_place'] = $this->processAddress($request, 'birth');

            // Process telephone numbers
            $bioData['telephone'] = array_filter($validated['telephone'] ?? [], function($phone) {
                return !empty(trim($phone));
            });

            // Process children data
            $bioData['children'] = array_filter($validated['children'] ?? [], function($child) {
                return !empty(trim($child['name'] ?? ''));
            });

            $record = BioData::create($bioData);

            DB::commit();

            Log::info('Bio data created successfully', ['id' => $record->id, 'nin' => $record->nin_no]);

            return response()->json([
                'success' => true, 
                'message' => 'Bio data submitted successfully',
                'data' => ['id' => $record->id]
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bio data creation failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.'
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        try {
            $query = BioData::query();

            // Add search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%")
                      ->orWhere('nin_no', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // Add pagination
            $perPage = $request->get('per_page', 15);
            $bioData = $query->latest()->paginate($perPage);

            return response()->json($bioData);
        } catch (\Exception $e) {
            Log::error('Failed to fetch bio data', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $bioData = BioData::findOrFail($id);
            return response()->json(['success' => true, 'data' => $bioData]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $bioData = BioData::findOrFail($id);
            
            $validated = $request->validate([
                'full_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|nullable|email|max:255',
                'occupation' => 'sometimes|nullable|string|max:255',
                'telephone' => 'sometimes|nullable|array|max:3',
                'telephone.*' => 'nullable|string|regex:/^\+?[0-9]{10,15}$/'
            ]);

            $bioData->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Bio data updated successfully',
                'data' => $bioData
            ]);
        } catch (\Exception $e) {
            Log::error('Bio data update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Update failed'
            ], 500);
        }
    }

    private function processAddress(Request $request, string $type): array
    {
        return [
            'village' => $request->input("{$type}_village"),
            'ward' => $request->input("{$type}_ward"),
            'subcounty' => $request->input("{$type}_subcounty"),
            'county' => $request->input("{$type}_county"),
            'district' => $request->input("{$type}_district")
        ];
    }

    public function getStats()
    {
        try {
            $stats = [
                'total_records' => BioData::count(),
                'recent_submissions' => BioData::where('created_at', '>=', now()->subDays(7))->count(),
                'by_marital_status' => BioData::select('marital_status', DB::raw('count(*) as count'))
                    ->groupBy('marital_status')
                    ->get(),
                'by_nationality' => BioData::select('nationality', DB::raw('count(*) as count'))
                    ->groupBy('nationality')
                    ->get()
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch bio data stats', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
        }
    }
}