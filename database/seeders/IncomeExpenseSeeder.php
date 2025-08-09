<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\IncomeType;
use App\Models\ExpenseType;
use App\Models\Income;
use App\Models\Expense;
use App\Models\User;
use App\Models\Transaction;

class IncomeExpenseSeeder extends Seeder
{
    public function run(): void
    {
        // Remove previous seeded data (disable foreign key checks for MySQL)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('transactions')->truncate();
        DB::table('incomes')->truncate();
        DB::table('expenses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Seed Income Types
        $incomeTypes = [
            ['name' => 'Sales', 'type' => 'Direct Income', 'description' => 'Income from sales', 'status' => true, 'company_id' => 1],
            ['name' => 'Interest Received', 'type' => 'Indirect Income', 'description' => 'Bank interest', 'status' => true, 'company_id' => 1],
            ['name' => 'Commission', 'type' => 'Indirect Income', 'description' => 'Commission income', 'status' => true, 'company_id' => 1],
        ];
        foreach ($incomeTypes as $type) {
            IncomeType::firstOrCreate(['name' => $type['name'], 'company_id' => 1], $type);
        }

        // Seed Expense Types
        $expenseTypes = [
            ['name' => 'Purchase', 'type' => 'Direct Expense', 'description' => 'Goods purchased', 'status' => true, 'company_id' => 1],
            ['name' => 'Salary', 'type' => 'Indirect Expense', 'description' => 'Employee salary', 'status' => true, 'company_id' => 1],
            ['name' => 'Rent', 'type' => 'Indirect Expense', 'description' => 'Office rent', 'status' => true, 'company_id' => 1],
        ];
        foreach ($expenseTypes as $type) {
            ExpenseType::firstOrCreate(['name' => $type['name'], 'company_id' => 1], $type);
        }

        // Get a user for created_by
        $user = User::first();
        if (!$user) return;

        // Get a bank account if exists
        $bankAccountId = DB::table('bank_accounts')->value('id');

        // Seed 100 Incomes
        $incomeTypesList = IncomeType::where('company_id', 1)->pluck('id')->toArray();
        $receiptModes = ['cash', 'bank', 'credit'];
        for ($i = 1; $i <= 100; $i++) {
            $incomeTypeId = $incomeTypesList[array_rand($incomeTypesList)];
            $mode = $receiptModes[array_rand($receiptModes)];
            $amount = rand(100, 5000) + rand(0, 99)/100;
            $daysAgo = rand(0, 60);
            $date = Carbon::now()->subDays($daysAgo);
            $income = Income::create([
                'income_type_id' => $incomeTypeId,
                'date_time' => $date,
                'receipt_mode' => $mode,
                'receipt_amount' => $amount,
                'narration' => 'Auto-generated income entry #'.$i,
                'created_by' => $user->id,
                'bank_account_id' => $mode === 'bank' ? $bankAccountId : null,
                'company_id' => 1,
                'voucher_number' => 'INC-'.str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
            // Create transaction for income
            Transaction::create([
                'company_id' => 1,
                'date' => $date,
                'transaction_type' => 'income',
                'debit' => $amount,
                'credit' => 0,
                'payment_mode' => $mode,
                'bank_id' => $mode === 'bank' ? $bankAccountId : null,
                'income_id' => $income->id,
                'created_by' => $user->id,
            ]);
        }

        // Seed 100 Expenses
        $expenseTypesList = ExpenseType::where('company_id', 1)->pluck('id')->toArray();
        $paymentModes = ['cash', 'bank', 'credit'];
        for ($i = 1; $i <= 100; $i++) {
            $expenseTypeId = $expenseTypesList[array_rand($expenseTypesList)];
            $mode = $paymentModes[array_rand($paymentModes)];
            $amount = rand(100, 6000) + rand(0, 99)/100;
            $daysAgo = rand(0, 60);
            $date = Carbon::now()->subDays($daysAgo);
            $expense = Expense::create([
                'expense_type_id' => $expenseTypeId,
                'date_time' => $date,
                'payment_mode' => $mode,
                'payment_amount' => $amount,
                'narration' => 'Auto-generated expense entry #'.$i,
                'created_by' => $user->id,
                'bank_account_id' => $mode === 'bank' ? $bankAccountId : null,
                'company_id' => 1,
                'voucher_number' => 'EXP-'.str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
            // Create transaction for expense
            Transaction::create([
                'company_id' => 1,
                'date' => $date,
                'transaction_type' => 'expense',
                'debit' => 0,
                'credit' => $amount,
                'payment_mode' => $mode,
                'bank_id' => $mode === 'bank' ? $bankAccountId : null,
                'expense_id' => $expense->id,
                'created_by' => $user->id,
            ]);
        }
    }
}
