<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\OpeningBalance;
use Illuminate\Http\Request;

class OpeningBalanceController extends Controller
{
    public function index()
    {
        $openingBalances = OpeningBalance::orderBy('created_at', 'desc')->paginate(10);
        $canCreate = OpeningBalance::withoutTrashed()->count() === 0;
        return view('backend.pages.opening-balances.index', compact('openingBalances', 'canCreate'));
    }

    public function store(Request $request)
    {
        // Check if any non-deleted opening balance exists
        if (OpeningBalance::withoutTrashed()->count() > 0) {
            return response()->json([
                'message' => 'Cannot create new opening balance. Only one active opening balance is allowed.'
            ], 422);
        }

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

      OpeningBalance::create(array_merge($validated, [
            'created_by' => auth()->id()
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Opening Balance created successfully',
        ]);
    }

    public function update(Request $request, OpeningBalance $openingBalance)
    {
        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $openingBalance->update($validated);

        return response()->json(['message' => 'Opening Balance updated successfully']);
    }

    public function destroy(OpeningBalance $openingBalance)
    {
        if ($openingBalance->status) {
            return response()->json([
                'message' => 'Cannot delete active opening balance'
            ], 422);
        }

        $openingBalance->delete();

        return response()->json(['message' => 'Opening Balance deleted successfully']);
    }
}
