<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberReport;
use App\Models\ReceiptAccount;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MemberReportController extends Controller
{
    /**
     * Display member reports listing
     */
    public function index()
    {
        $reports = MemberReport::with('member')
            ->latest()
            ->paginate(20);

        return view('frontend.pages.member-report.index', compact('reports'));
    }

    /**
     * Display member reports for specific member
     */
    public function showByMember(Member $member)
    {
        $reports = $member->reports()
            ->latest()
            ->paginate(20);

        $memberInfo = $member;
        $totalDebit = $member->reports()->sum('debit');
        $totalCredit = $member->reports()->sum('credit');
        $balance = ($totalDebit ?? 0) - ($totalCredit ?? 0);

        return view('frontend.pages.member-report.member-reports', compact(
            'reports',
            'memberInfo',
            'totalDebit',
            'totalCredit',
            'balance'
        ));
    }

    /**
     * Show member report creation form
     */
    public function create()
    {
        $members = Member::where('active', 1)
            ->with('house')
            ->latest()
            ->get();

        $transactionTypes = [
            'Year Subscription',
            'Monthly Subscription',
            'Payment',
            'Refund',
            'Fine',
            'Donation',
            'Adjustment',
            'Other'
        ];

        $paymentMethods = [
            'Cash',
            'Check',
            'Online Transfer',
            'Bank Transfer',
            'Credit Card',
            'Debit Card',
            'Other'
        ];

        return view('frontend.pages.member-report.create', compact(
            'members',
            'transactionTypes',
            'paymentMethods'
        ));
    }

    /**
     * Show yearly subscription payment form
     */
    public function createYearlyPayment(Request $request)
    {
        $members = Member::where('active', 1)
            ->where('subscription', 1)
            ->with('house.place')
            ->orderBy('name')
            ->get();

        $paymentMethods = [
            'Cash',
            'Check',
            'Online Transfer',
            'Bank Transfer',
            'Credit Card',
            'Debit Card',
            'Other'
        ];

        $receiptModes = [
            'Cash',
            'Cheque',
            'Bank Transfer',
            'UPI',
            'Online',
            'Other',
        ];

        $receiptAccounts = ReceiptAccount::where('active', 1)
            ->orderBy('name')
            ->get();

        $selectedMember = null;
        if ($request->filled('member_id')) {
            $selectedMember = $members->firstWhere('id', (int) $request->member_id);
        }

        return view('frontend.pages.member-report.yearly-payment', compact(
            'members',
            'paymentMethods',
            'receiptModes',
            'receiptAccounts',
            'selectedMember'
        ));
    }

    /**
     * Search yearly subscription members for the payment form.
     */
    public function searchYearlyPaymentMembers(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $members = Member::where('active', 1)
            ->where('subscription', 1)
            ->with('house.place')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('adhar_number', 'LIKE', "%{$search}%")
                        ->orWhere('father_name', 'LIKE', "%{$search}%")
                        ->orWhere('mother_name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile_number', 'LIKE', "%{$search}%")
                        ->orWhere('whatsapp_number', 'LIKE', "%{$search}%")
                        ->orWhereHas('house', function ($houseQuery) use ($search) {
                            $houseQuery->where('house_name', 'LIKE', "%{$search}%")
                                ->orWhere('house_no', 'LIKE', "%{$search}%")
                                ->orWhere('jamath_house_no', 'LIKE', "%{$search}%")
                                ->orWhere('house_owner', 'LIKE', "%{$search}%")
                                ->orWhere('ward_no', 'LIKE', "%{$search}%")
                                ->orWhere('address', 'LIKE', "%{$search}%")
                                ->orWhere('phone', 'LIKE', "%{$search}%")
                                ->orWhere('mobile', 'LIKE', "%{$search}%")
                                ->orWhereHas('place', function ($placeQuery) use ($search) {
                                    $placeQuery->where('name', 'LIKE', "%{$search}%");
                                });
                        });
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'subscription_type' => $member->subscription_type,
                    'subscription_amount' => $member->subscription_amount ?? 0,
                    'house_name' => $member->house?->house_name,
                    'house_no' => $member->house?->house_no,
                    'jamath_house_no' => $member->house?->jamath_house_no,
                    'house_owner' => $member->house?->house_owner,
                    'place' => $member->house?->place?->name,
                ];
            });

        return response()->json($members);
    }

    /**
     * Store yearly subscription payment as credit transaction
     */
    public function storeYearlyPayment(Request $request)
    {
        $validated = $request->validate([
            'receipt_no' => 'required|string|max:100',
            'member_id' => 'required|exists:members,id',
            'date' => 'required|date',
            'posting_year' => 'required|string|max:20',
            'amount' => 'required|numeric|min:0.01',
            'include_previous_due' => 'nullable|boolean',
            'due_amount' => 'nullable|numeric|min:0',
            'received_from' => 'required|string|max:255',
            'narration' => 'nullable|string',
            'receipt_mode' => 'required|string|max:100',
            'receipt_account_id' => 'required|exists:receipt_accounts,id',
            'payment_method' => 'nullable|string|max:100',
        ]);

        $member = Member::with('house.place')->findOrFail($validated['member_id']);
        if (! $member->subscription) {
            return back()
                ->withInput()
                ->withErrors(['member_id' => 'Selected member does not have subscription enabled.']);
        }

        $currentDue = (float) $member->reports()->sum('debit') - (float) $member->reports()->sum('credit');
        $credit = (float) $validated['amount'];
        $includePreviousDue = (bool) ($validated['include_previous_due'] ?? false);
        $dueAmount = $includePreviousDue
            ? ($validated['due_amount'] ?? $currentDue)
            : null;

        MemberReport::create([
            'member_id' => $member->id,
            'sl_no' => MemberReport::generateSlNo(),
            'receipt_no' => $validated['receipt_no'],
            'date' => $validated['date'],
            'name' => $member->name,
            'transaction_type' => 'Yearly Subscription Payment',
            'posting_year' => $validated['posting_year'],
            'description' => $validated['narration'] ?? null,
            'debit' => 0,
            'credit' => $credit,
            'balance' => 0 - $credit,
            'include_previous_due' => $includePreviousDue,
            'due_amount' => $dueAmount,
            'received_from' => $validated['received_from'],
            'receipt_mode' => $validated['receipt_mode'],
            'receipt_account_id' => $validated['receipt_account_id'],
            'payment_method' => $validated['payment_method'] ?? $validated['receipt_mode'],
            'status' => 'completed',
            'remarks' => null,
        ]);

        return redirect()
            ->route('member-reports.show-member', $member->id)
            ->with('success', 'Yearly subscription payment recorded successfully!');
    }

    /**
     * Create receipt account from yearly payment page
     */
    public function storeReceiptAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:receipt_accounts,name',
            'account_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $account = ReceiptAccount::create([
            'name' => $validated['name'],
            'account_number' => $validated['account_number'] ?? null,
            'description' => $validated['description'] ?? null,
            'active' => 1,
        ]);

        return response()->json([
            'success' => true,
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'account_number' => $account->account_number,
            ],
        ]);
    }

    /**
     * Store member report
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'receipt_no' => 'nullable|unique:member_reports,receipt_no',
            'date' => 'required|date',
            'transaction_type' => 'required|string',
            'posting_year' => 'nullable|string',
            'description' => 'nullable|string',
            'debit' => 'nullable|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
            'remarks' => 'nullable|string',
        ]);

        // Auto-generate receipt number if not provided
        if (empty($validated['receipt_no'])) {
            $validated['receipt_no'] = MemberReport::generateReceiptNo();
        }

        // Get member name for denormalization
        $member = Member::find($validated['member_id']);
        $validated['name'] = $member->name;

        // Calculate balance
        $debit = $validated['debit'] ?? 0;
        $credit = $validated['credit'] ?? 0;
        $validated['balance'] = $debit - $credit;

        $report = MemberReport::create($validated);

        return redirect()
            ->route('member-reports.show-member', $member->id)
            ->with('success', 'Member report created successfully!');
    }

    /**
     * Show single report
     */
    public function show(MemberReport $memberReport)
    {
        $memberReport->load('member');
        return view('frontend.pages.member-report.show', compact('memberReport'));
    }

    /**
     * Show edit form
     */
    public function edit(MemberReport $memberReport)
    {
        $members = Member::where('active', 1)
            ->with('house')
            ->latest()
            ->get();

        $transactionTypes = [
            'Year Subscription',
            'Monthly Subscription',
            'Payment',
            'Refund',
            'Fine',
            'Donation',
            'Adjustment',
            'Other'
        ];

        $paymentMethods = [
            'Cash',
            'Check',
            'Online Transfer',
            'Bank Transfer',
            'Credit Card',
            'Debit Card',
            'Other'
        ];

        return view('frontend.pages.member-report.edit', compact(
            'memberReport',
            'members',
            'transactionTypes',
            'paymentMethods'
        ));
    }

    /**
     * Update member report
     */
    public function update(Request $request, MemberReport $memberReport)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'date' => 'required|date',
            'transaction_type' => 'required|string',
            'posting_year' => 'nullable|string',
            'description' => 'nullable|string',
            'debit' => 'nullable|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
            'remarks' => 'nullable|string',
        ]);

        // Update member name if member changed
        $member = Member::find($validated['member_id']);
        $validated['name'] = $member->name;

        // Calculate balance
        $debit = $validated['debit'] ?? 0;
        $credit = $validated['credit'] ?? 0;
        $validated['balance'] = $debit - $credit;

        $memberReport->update($validated);

        return redirect()
            ->route('member-reports.show-member', $member->id)
            ->with('success', 'Member report updated successfully!');
    }

    /**
     * Delete member report (soft delete)
     */
    public function destroy(MemberReport $memberReport)
    {
        $memberId = $memberReport->member_id;
        $memberReport->delete();

        return redirect()
            ->route('member-reports.show-member', $memberId)
            ->with('success', 'Member report deleted successfully!');
    }

    /**
     * Get member report summary
     */
    public function getSummary($memberId)
    {
        $member = Member::findOrFail($memberId);
        $reports = $member->reports()->get();

        $summary = [
            'total_debit' => $reports->sum('debit'),
            'total_credit' => $reports->sum('credit'),
            'balance' => ($reports->sum('debit') ?? 0) - ($reports->sum('credit') ?? 0),
            'total_transactions' => $reports->count(),
            'pending' => $reports->where('status', 'pending')->count(),
            'completed' => $reports->where('status', 'completed')->count(),
            'cancelled' => $reports->where('status', 'cancelled')->count(),
        ];

        return response()->json($summary);
    }

    /**
     * Search reports by various criteria
     */
    public function search(Request $request)
    {
        $query = MemberReport::with('member');

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->from_date)->startOfDay(),
                Carbon::parse($request->to_date)->endOfDay()
            ]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('receipt_no', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $reports = $query->latest()->paginate(20);

        return view('frontend.pages.member-report.index', compact('reports'));
    }
}
