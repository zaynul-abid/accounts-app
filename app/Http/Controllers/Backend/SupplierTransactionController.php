<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SupplierTransactionController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('frontend.pages.suppliers.index', compact('suppliers'));
    }

    public function report()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('frontend.pages.suppliers.report', compact('suppliers'));
    }

    public function transactions(Supplier $supplier, Request $request)
    {
        $today = Carbon::today('Asia/Kolkata')->toDateString();
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $showAll = $request->input('show_all', false);

        Log::info('Fetching transactions for supplier ID: ' . $supplier->id, [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'show_all' => $showAll,
            'page' => $request->input('page', 1)
        ]);

        $query = SupplierTransaction::where('supplier_id', $supplier->id)
            ->with(['expense', 'expense.expenseType'])
            ->orderBy('date', 'desc');

        // Apply date filters only if show_all is false and dates are provided
        if (!$showAll) {
            if ($fromDate && $fromDate !== '') {
                $query->whereDate('date', '>=', Carbon::parse($fromDate, 'Asia/Kolkata')->startOfDay());
                Log::info('Applying from_date filter: ' . $fromDate);
            }
            if ($toDate && $toDate !== '') {
                $query->whereDate('date', '<=', Carbon::parse($toDate, 'Asia/Kolkata')->endOfDay());
                Log::info('Applying to_date filter: ' . $toDate);
            }
        }

        $transactions = $query->paginate(10);

        // Calculate total debit and credit
        $totalsQuery = SupplierTransaction::where('supplier_id', $supplier->id);

        if (!$showAll) {
            if ($fromDate && $fromDate !== '') {
                $totalsQuery->whereDate('date', '>=', Carbon::parse($fromDate, 'Asia/Kolkata')->startOfDay());
            }
            if ($toDate && $toDate !== '') {
                $totalsQuery->whereDate('date', '<=', Carbon::parse($toDate, 'Asia/Kolkata')->endOfDay());
            }
        }

        $totals = $totalsQuery->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        Log::info('Transactions fetched:', [
            'count' => $transactions->count(),
            'total_debit' => $totals->total_debit,
            'total_credit' => $totals->total_credit
        ]);

        return response()->json([
            'success' => true,
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'contact_number' => $supplier->contact_number,
                'address' => $supplier->address,
                'opening_balance' => $supplier->opening_balance,
                'status' => $supplier->status
            ],
            'transactions' => $transactions->items(),
            'totals' => [
                'total_debit' => $totals->total_debit,
                'total_credit' => $totals->total_credit,
                'balance' => $totals->total_debit - $totals->total_credit
            ],
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total()
            ]
        ]);
    }

    public function storePayment(Request $request, Supplier $supplier)
    {
        try {
            $validated = $request->validate([
                'payment_amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'note' => 'nullable|string',
            ]);

            $user = auth()->user();
            if (!$user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not associated with any company',
                ], 403);
            }

            if ($supplier->company_id !== $user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Supplier does not belong to your company',
                ], 403);
            }

            $transaction = SupplierTransaction::create([
                'company_id' => $user->company_id,
                'supplier_id' => $supplier->id,
                'date' => Carbon::parse($validated['date'], 'Asia/Kolkata'),
                'bill_number' => null,
                'transaction_mode' => 'payment',
                'expense_id' => null,
                'transaction_type' => 'payment',
                'debit' =>  $validated['payment_amount'],
                'credit' => 0,
                'notes' => $validated['note'] ?? 'Manual payment',
            ]);

            Log::info('Payment recorded for supplier ID: ' . $supplier->id, [
                'transaction_id' => $transaction->id,
                'amount' => $validated['payment_amount'],
                'date' => $validated['date']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'transaction' => $transaction
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to record payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
