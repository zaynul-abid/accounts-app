<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;

class SupplierTransactionController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('frontend.pages.suppliers.index', compact('suppliers')); // Corrected to use details.blade.php
    }

    public function transactions(Supplier $supplier, Request $request)
    {
        $query = SupplierTransaction::where('supplier_id', $supplier->id)
            ->with(['expense', 'expense.expenseType'])
            ->orderBy('date', 'desc');

        // Apply date filters if provided
        if ($request->has('from_date') && $request->input('from_date')) {
            $query->whereDate('date', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date') && $request->input('to_date')) {
            $query->whereDate('date', '<=', $request->input('to_date'));
        }

        $transactions = $query->paginate(10);

        // Calculate total debit and credit for filtered transactions
        $totalsQuery = SupplierTransaction::where('supplier_id', $supplier->id);
        if ($request->has('from_date') && $request->input('from_date')) {
            $totalsQuery->whereDate('date', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date') && $request->input('to_date')) {
            $totalsQuery->whereDate('date', '<=', $request->input('to_date'));
        }
        $totals = $totalsQuery->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

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
            // Validate the request data
            $validated = $request->validate([
                'payment_amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'note' => 'nullable|string',
            ]);

            // Get the authenticated user and company ID
            $user = auth()->user();
            if (!$user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not associated with any company',
                ], 403);
            }

            // Verify the supplier belongs to the user's company
            if ($supplier->company_id !== $user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Supplier does not belong to your company',
                ], 403);
            }

            // Create supplier transaction
            $transaction = SupplierTransaction::create([
                'company_id' => $user->company_id,
                'supplier_id' => $supplier->id,
                'date' => $validated['date'],
                'bill_number' => null,
                'transaction_mode' => 'payment',
                'expense_id' => null,
                'transaction_type' => 'payment',
                'debit' => 0,
                'credit' => $validated['payment_amount'],
                'notes' => $validated['note'] ?? 'Manual payment',
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'transaction' => [
                    'id' => $transaction->id,
                    'company_id' => $transaction->company_id,
                    'supplier_id' => $transaction->supplier_id,
                    'date' => $transaction->date,
                    'bill_number' => $transaction->bill_number,
                    'transaction_mode' => $transaction->transaction_mode,
                    'expense_id' => $transaction->expense_id,
                    'transaction_type' => $transaction->transaction_type,
                    'debit' => $transaction->debit,
                    'credit' => $transaction->credit,
                    'notes' => $transaction->notes,
                ]
            ], 201);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to record payment: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
