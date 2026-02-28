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
        
        $perPage = (int) $request->get('per_page', 20);
        if (!in_array($perPage, [10, 15, 20, 50], true)) {
            $perPage = 20;
        }

        $loans = $query
            ->paginate($perPage, ['*'], 'page')
            ->appends($request->except('page'));

        $stats = [
            'total' => Loan::where('member_id', $member->member_id)->count(),
            'active' => Loan::where('member_id', $member->member_id)->where('status', 'approved')->count(),
            'pending' => Loan::where('member_id', $member->member_id)->where('status', 'pending')->count(),
            'completed' => Loan::where('member_id', $member->member_id)
                ->where('status', 'approved')
                ->get()
                ->filter(static fn (Loan $loan) => $loan->remaining_balance <= 0)
                ->count(),
        ];

        $applicationsQuery = LoanApplication::with('member')
            ->where('member_id', $member->member_id);

        if ($request->filled('app_search')) {
            $search = $request->app_search;
            $applicationsQuery->where(function ($q) use ($search): void {
                $q->where('application_id', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        if ($request->filled('app_status')) {
            $applicationsQuery->where('status', $request->app_status);
        }

        if ($request->filled('app_date_from')) {
            $applicationsQuery->whereDate('created_at', '>=', $request->app_date_from);
        }

        if ($request->filled('app_date_to')) {
            $applicationsQuery->whereDate('created_at', '<=', $request->app_date_to);
        }

        switch ($request->app_sort) {
            case 'amount_high':
                $applicationsQuery->orderBy('amount', 'desc');
                break;
            case 'amount_low':
                $applicationsQuery->orderBy('amount', 'asc');
                break;
            case 'oldest':
                $applicationsQuery->oldest();
                break;
            default:
                $applicationsQuery->latest();
                break;
        }

        $applicationsPerPage = (int) $request->get('applications_per_page', 10);
        if (!in_array($applicationsPerPage, [5, 10, 15, 20, 50], true)) {
            $applicationsPerPage = 10;
        }

        $applications = $applicationsQuery
            ->paginate($applicationsPerPage, ['*'], 'applications_page')
            ->appends($request->except('applications_page'));

        $applicationStats = [
            'total' => LoanApplication::where('member_id', $member->member_id)->count(),
            'pending' => LoanApplication::where('member_id', $member->member_id)->where('status', 'pending')->count(),
            'approved' => LoanApplication::where('member_id', $member->member_id)->where('status', 'approved')->count(),
            'rejected' => LoanApplication::where('member_id', $member->member_id)->where('status', 'rejected')->count(),
        ];

        return view('shareholder.loans', compact('loans', 'stats', 'applications', 'applicationStats'));
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
        $query = array_filter($request->query(), static fn ($value) => $value !== null && $value !== '');

        if (isset($query['search']) && !isset($query['app_search'])) {
            $query['app_search'] = $query['search'];
            unset($query['search']);
        }

        if (isset($query['status']) && !isset($query['app_status'])) {
            $query['app_status'] = $query['status'];
            unset($query['status']);
        }

        if (isset($query['date_from']) && !isset($query['app_date_from'])) {
            $query['app_date_from'] = $query['date_from'];
            unset($query['date_from']);
        }

        if (isset($query['date_to']) && !isset($query['app_date_to'])) {
            $query['app_date_to'] = $query['date_to'];
            unset($query['date_to']);
        }

        if (isset($query['sort']) && !isset($query['app_sort'])) {
            $query['app_sort'] = $query['sort'];
            unset($query['sort']);
        }

        if (isset($query['per_page']) && !isset($query['applications_per_page'])) {
            $query['applications_per_page'] = $query['per_page'];
            unset($query['per_page']);
        }

        $query['tab'] = 'applications';

        return redirect()->to(route('shareholder.loans', $query) . '#loan-applications');
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
        
        return redirect()->to(route('shareholder.loans', ['tab' => 'applications']) . '#loan-applications')
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
