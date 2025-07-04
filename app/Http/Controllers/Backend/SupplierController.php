<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'desc')->paginate(10);
        return view('backend.pages.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'address' => 'nullable|string',
            'opening_balance' => 'required|numeric',
            'status' => 'required|boolean',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully',
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'contact_number' => $supplier->contact_number,
                'address' => $supplier->address,
                'opening_balance' => $supplier->opening_balance,
                'status' => $supplier->status
            ]
        ]);
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

            $supplier->update($validated);

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
