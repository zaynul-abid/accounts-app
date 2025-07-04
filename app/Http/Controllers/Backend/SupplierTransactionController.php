<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierTransaction;


class SupplierTransactionController extends Controller
{

    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'desc')->get();
        return view('frontend.pages.suppliers.index', compact('suppliers'));
    }

    public function transactions(Supplier $supplier)
    {
        $transactions = SupplierTransaction::where('supplier_id', $supplier->id)
            ->with(['expense', 'expense.expenseType'])
            ->orderBy('date', 'desc')
            ->paginate(10);

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
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total()
            ]
        ]);
    }

}
