<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\OpeningBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OpeningBalanceController extends Controller
{
    public function index()
    {
        try {
            $companyId = auth()->user()->company_id;

            $openingBalances = OpeningBalance::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $canCreate = OpeningBalance::withoutTrashed()
                    ->where('company_id', $companyId)
                    ->count() === 0;

            return view('backend.pages.opening-balances.index', compact('openingBalances', 'canCreate'));
        } catch (\Exception $e) {
            Log::error('Opening Balance Index Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while loading opening balances.');
        }
    }

    public function store(Request $request)
    {
        try {
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
                'description' => 'nullable|string|max:1000',
                'status' => 'required|boolean',
            ]);

            $openingBalance = OpeningBalance::create(array_merge($validated, [
                'created_by' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Opening Balance created successfully',
                'openingBalance' => [
                    'id' => $openingBalance->id,
                    'opening_balance' => $openingBalance->opening_balance,
                    'description' => $openingBalance->description,
                    'status' => $openingBalance->status,
                    'company_id' => $openingBalance->company_id,
                    'created_by' => $openingBalance->createdBy ? $openingBalance->createdBy->name : 'N/A'
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Opening Balance Store Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id ?? 'N/A',
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the opening balance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, OpeningBalance $openingBalance)
    {
        try {
            // Ensure the opening balance belongs to the user's company
            if ($openingBalance->company_id !== auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this opening balance'
                ], 403);
            }

            $validated = $request->validate([
                'opening_balance' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'status' => 'required|boolean',
            ]);

            // Log request data for debugging
            Log::info('Opening Balance Update Request', [
                'opening_balance_id' => $openingBalance->id,
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]);

            $openingBalance->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Opening Balance updated successfully',
                'openingBalance' => [
                    'id' => $openingBalance->id,
                    'opening_balance' => $openingBalance->opening_balance,
                    'description' => $openingBalance->description,
                    'status' => $openingBalance->status,
                    'company_id' => $openingBalance->company_id,
                    'created_by' => $openingBalance->createdBy ? $openingBalance->createdBy->name : 'N/A'
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Opening Balance Update Error: ' . $e->getMessage(), [
                'opening_balance_id' => $openingBalance->id,
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id ?? 'N/A',
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the opening balance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(OpeningBalance $openingBalance)
    {
        try {
            // Ensure the opening balance belongs to the user's company
            if ($openingBalance->company_id !== auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this opening balance'
                ], 403);
            }

            if ($openingBalance->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete active opening balance'
                ], 422);
            }

            $openingBalance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Opening Balance deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Opening Balance Delete Error: ' . $e->getMessage(), [
                'opening_balance_id' => $openingBalance->id,
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the opening balance: ' . $e->getMessage()
            ], 500);
        }
    }
}
