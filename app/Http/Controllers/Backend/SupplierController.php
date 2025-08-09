<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('backend.pages.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:255',
                'address' => 'nullable|string',
                'opening_balance' => 'required|numeric',
                'status' => 'required|boolean',
            ]);

            // Get the authenticated user and company ID
            $user = auth()->user();
            if (!$user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not associated with any company',
                ], 403);
            }

            // Start a database transaction
            return DB::transaction(function () use ($request, $validated, $user) {
                // Merge company_id with validated data
                $validated['company_id'] = $user->company_id;

                // Create supplier
                $supplier = Supplier::create($validated);

                // Create supplier transaction for opening balance
                SupplierTransaction::create([
                    'company_id' => $user->company_id,
                    'supplier_id' => $supplier->id,
                    'date' => now(),
                    'bill_number' => null,
                    'transaction_mode' => null,
                    'expense_id' => null,
                    'transaction_type' => 'opening balance',
                    'debit' => 0,
                    'credit' => $validated['opening_balance'],
                    'notes' => 'Opening balance for new supplier',
                ]);

                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Supplier created successfully',
                    'supplier' => [
                        'id' => $supplier->id,
                        'name' => $supplier->name,
                        'contact_number' => $supplier->contact_number,
                        'address' => $supplier->address,
                        'opening_balance' => $supplier->opening_balance,
                        'status' => $supplier->status,
                        'company_id' => $supplier->company_id,
                    ]
                ], 201);
            });
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to create supplier: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to create supplier: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Supplier $supplier)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:255',
                'address' => 'nullable|string',
                'opening_balance' => 'required|numeric',
                'status' => 'required|boolean',
            ]);

            // Update supplier
            $supplier->update($validated);

            // Update or create opening balance transaction
            $transaction = SupplierTransaction::where('supplier_id', $supplier->id)
                ->where('transaction_type', 'opening balance')
                ->first();

            if ($transaction) {
                $transaction->update([
                    'debit' => $validated['opening_balance'],
                    'credit' => 0,
                    'date' => now(),
                    'notes' => 'Updated opening balance'
                ]);
            } else {
                SupplierTransaction::create([
                    'supplier_id' => $supplier->id,
                    'date' => now(),
                    'bill_number' => null,
                    'transaction_mode' => null,
                    'expense_id' => null,
                    'transaction_type' => 'opening balance',
                    'debit' => 0,
                    'credit' => $validated['opening_balance'],
                    'notes' => 'Opening balance for updated supplier'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully',
                'supplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'contact_number' => $supplier->contact_number,
                    'address' => $supplier->address,
                    'opening_balance' => $supplier->opening_balance,
                    'status' => $supplier->status
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Supplier Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->status) {
            return response()->json([
                'message' => 'Cannot delete active supplier'
            ], 422);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully']);
    }
}
