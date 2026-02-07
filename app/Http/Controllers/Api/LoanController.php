<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $loans = Loan::with('member')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'loans' => $loans,
                'total_loans' => $loans->count(),
                'pending_loans' => $loans->where('status', 'pending')->count(),
                'approved_loans' => $loans->where('status', 'approved')->count(),
                'rejected_loans' => $loans->where('status', 'rejected')->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading loans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'member_id' => 'required|exists:members,member_id',
                'amount' => 'required|numeric|min:1000',
                'purpose' => 'required|string|max:500',
                'status' => 'nullable|in:pending,approved,rejected'
            ]);

            $loan = Loan::create([
                'member_id' => $request->member_id,
                'amount' => $request->amount,
                'purpose' => $request->purpose,
                'status' => $request->status ?? 'pending',
                'repayment_months' => $request->repayment_months ?? 12,
                'interest' => $request->interest ?? 10
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Loan request created successfully',
                'loan' => $loan->load('member')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a loan application
     */
    public function approve($id)
    {
        try {
            $loan = Loan::findOrFail($id);

            // Check if already approved
            if ($loan->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan is already approved'
                ], 400);
            }

            // Update loan status
            $loan->status = 'approved';
            $loan->approved_by = Auth::id();
            $loan->approved_at = now();
            $loan->save();

            // Calculate monthly payment
            $monthlyRate = $loan->interest_rate / 12 / 100;
            $monthlyPayment = $loan->amount * $monthlyRate / (1 - pow(1 + $monthlyRate, -$loan->repayment_months));
            $loan->monthly_payment = round($monthlyPayment, 2);
            $loan->save();

            return response()->json([
                'success' => true,
                'message' => 'Loan approved successfully',
                'loan' => $loan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a loan application
     */
    public function reject($id)
    {
        try {
            $loan = Loan::findOrFail($id);

            // Check if already processed
            if ($loan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan has already been processed'
                ], 400);
            }

            // Update loan status
            $loan->status = 'rejected';
            $loan->approved_by = Auth::id();
            $loan->approved_at = now();
            $loan->save();

            return response()->json([
                'success' => true,
                'message' => 'Loan rejected successfully',
                'loan' => $loan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $loan = Loan::findOrFail($id);

            // Validate request
            $request->validate([
                'amount' => 'required|numeric|min:1000',
                'purpose' => 'required|string|max:500',
                'repayment_months' => 'required|integer|min:1|max:60',
                'status' => 'required|in:pending,approved,rejected'
            ]);

            // Update loan
            $loan->amount = $request->amount;
            $loan->purpose = $request->purpose;
            $loan->repayment_months = $request->repayment_months;
            $loan->status = $request->status;

            // Recalculate monthly payment if approved
            if ($loan->status === 'approved') {
                $monthlyRate = $loan->interest_rate / 12 / 100;
                $monthlyPayment = $loan->amount * $monthlyRate / (1 - pow(1 + $monthlyRate, -$loan->repayment_months));
                $loan->monthly_payment = round($monthlyPayment, 2);
            }

            $loan->save();

            return response()->json([
                'success' => true,
                'message' => 'Loan updated successfully',
                'loan' => $loan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $loan = Loan::findOrFail($id);

            // Check if loan can be deleted (only pending loans)
            if ($loan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending loans can be deleted'
                ], 400);
            }

            $loan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Loan deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting loan: ' . $e->getMessage()
            ], 500);
        }
    }
}
