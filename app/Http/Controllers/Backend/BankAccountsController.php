<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountsController extends Controller


{
    public function index()
    {
        $bankAccounts = BankAccount::where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('backend.pages.banks.index', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:bank_accounts,account_number',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:50',
            'account_type' => 'required|in:savings,current',
            'is_active' => 'required|boolean',
        ]);

        // Get company ID - assuming user belongs to a company
        $companyId = auth()->user()->company_id; // or auth()->user()->company->id if using relationship

        // Merge company_id with validated data
        $data = array_merge($validated, ['company_id' => $companyId]);

        $bankAccount = BankAccount::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Bank Account created successfully',
            'bankAccount' => [
                'id' => $bankAccount->id,
                'account_name' => $bankAccount->account_name,
                'account_number' => $bankAccount->account_number,
                'bank_name' => $bankAccount->bank_name,
                'branch_name' => $bankAccount->branch_name,
                'ifsc_code' => $bankAccount->ifsc_code,
                'account_type' => $bankAccount->account_type,
                'is_active' => $bankAccount->is_active,
                'company_id' => $bankAccount->company_id // include company_id in response if needed
            ]
        ]);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        try {
            $validated = $request->validate([
                'account_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:255|unique:bank_accounts,account_number,' . $bankAccount->id,
                'bank_name' => 'required|string|max:255',
                'branch_name' => 'nullable|string|max:255',
                'ifsc_code' => 'nullable|string|max:50',
                'account_type' => 'required|in:savings,current',
                'is_active' => 'required|boolean',
            ]);

            $bankAccount->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Bank Account updated successfully',
                'bankAccount' => [
                    'id' => $bankAccount->id,
                    'account_name' => $bankAccount->account_name,
                    'account_number' => $bankAccount->account_number,
                    'bank_name' => $bankAccount->bank_name,
                    'branch_name' => $bankAccount->branch_name,
                    'ifsc_code' => $bankAccount->ifsc_code,
                    'account_type' => $bankAccount->account_type,
                    'is_active' => $bankAccount->is_active
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Bank Account Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bank account: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(BankAccount $bankAccount)
    {
        if ($bankAccount->is_active) {
            return response()->json([
                'message' => 'Cannot delete active bank account'
            ], 422);
        }

        $bankAccount->delete();

        return response()->json(['message' => 'Bank Account deleted successfully']);
    }
}


