<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\OpeningBalance;
use Illuminate\Http\Request;

class OpeningBalanceController extends Controller
{
    public function index()
    {
        // Get the current user's company ID
        $companyId = auth()->user()->company_id;

        // Get opening balances only for the current company
        $openingBalances = OpeningBalance::where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Check if creation is allowed (no active opening balance for this company)
        $canCreate = OpeningBalance::withoutTrashed()
                ->where('company_id', $companyId)
                ->count() === 0;

        return view('backend.pages.opening-balances.index', compact('openingBalances', 'canCreate'));
    }

    public function store(Request $request)
    {
        // Check if any non-deleted opening balance exists for this company
        if (OpeningBalance::withoutTrashed()
            ->where('company_id', auth()->user()->company_id)
            ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create new opening balance. Only one active opening balance per company is allowed.'
            ], 422);
        }

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        OpeningBalance::create(array_merge($validated, [
            'created_by' => auth()->id(),
            'company_id' => auth()->user()->company_id
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
