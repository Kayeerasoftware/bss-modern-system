<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoansController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            return redirect()->route('shareholder.dashboard')->with('error', 'Member profile not found.');
        }
        
        $query = Loan::with('member')->where('member_id', $member->member_id);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        $query->filterStatus($request->status);
        
        // Amount range
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }
        
        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sorting
        switch ($request->sort) {
            case 'amount_high':
                $query->orderBy('amount', 'desc');
                break;
            case 'amount_low':
                $query->orderBy('amount', 'asc');
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
        
        $perPage = $request->get('per_page', 20);
        $loans = $query->paginate($perPage)->appends($request->query());

        $stats = [
            'total' => Loan::where('member_id', $member->member_id)->count(),
            'active' => Loan::where('member_id', $member->member_id)->where('status', 'approved')->count(),
            'pending' => Loan::where('member_id', $member->member_id)->where('status', 'pending')->count(),
            'completed' => Loan::where('member_id', $member->member_id)
                ->where('status', 'approved')
                ->whereRaw('(COALESCE(amount, 0) + COALESCE(interest, 0) + COALESCE(processing_fee, 0)) <= COALESCE(paid_amount, 0)')
                ->count(),
        ];

        return view('shareholder.loans', compact('loans', 'stats'));
    }
    
    public function show($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        $loan = Loan::with('member')
            ->where('member_id', $member->member_id)
            ->findOrFail($id);
            
        return view('shareholder.loans.show', compact('loan'));
    }
    
    public function applications(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        
        $query = LoanApplication::with('member')
            ->where('member_id', $member->member_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_id', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $applications = $query->latest()->paginate(20)->appends($request->query());
            
        return view('shareholder.loans.applications', compact('applications'));
    }
    
    public function apply()
    {
        return view('shareholder.loans.apply');
    }
    
    public function storeApplication(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);
        
        LoanApplication::create([
            'member_id' => $member->member_id,
            'amount' => $request->amount,
            'purpose' => $request->purpose,
            'repayment_months' => $request->duration,
            'status' => 'pending',
        ]);
        
        return redirect()->route('shareholder.loans.applications')
            ->with('success', 'Loan application submitted successfully.');
    }
    
    public function showApplication($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        $application = LoanApplication::with('member')
            ->where('member_id', $member->member_id)
            ->findOrFail($id);
            
        return view('shareholder.loans.application-details', compact('application'));
    }
    
    public function makePayment(Request $request, $id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        $loan = Loan::where('member_id', $member->member_id)->findOrFail($id);
        
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $loan->remaining_balance,
        ]);

        DB::transaction(function () use ($loan, $member, $request): void {
            Transaction::create([
                'member_id' => $member->member_id,
                'type' => 'loan_payment',
                'amount' => $request->amount,
                'description' => 'Loan payment for loan #' . $loan->loan_id,
                'status' => 'completed',
                'metadata' => ['loan_id' => $loan->id],
                'transaction_date' => now(),
            ]);

            $loan->update([
                'paid_amount' => $loan->paid_amount + (float) $request->amount,
                'status' => 'approved',
            ]);
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Payment made successfully',
        ]);
    }
}
