<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add soft deletes to house_types table
        if (Schema::hasTable('house_types') && !Schema::hasColumn('house_types', 'deleted_at')) {
            Schema::table('house_types', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to income_types table
        if (Schema::hasTable('income_types') && !Schema::hasColumn('income_types', 'deleted_at')) {
            Schema::table('income_types', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to expense_types table
        if (Schema::hasTable('expense_types') && !Schema::hasColumn('expense_types', 'deleted_at')) {
            Schema::table('expense_types', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to incomes table
        if (Schema::hasTable('incomes') && !Schema::hasColumn('incomes', 'deleted_at')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to expenses table
        if (Schema::hasTable('expenses') && !Schema::hasColumn('expenses', 'deleted_at')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to opening_balances table
        if (Schema::hasTable('opening_balances') && !Schema::hasColumn('opening_balances', 'deleted_at')) {
            Schema::table('opening_balances', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to bank_accounts table
        if (Schema::hasTable('bank_accounts') && !Schema::hasColumn('bank_accounts', 'deleted_at')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to suppliers table
        if (Schema::hasTable('suppliers') && !Schema::hasColumn('suppliers', 'deleted_at')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to supplier_transactions table
        if (Schema::hasTable('supplier_transactions') && !Schema::hasColumn('supplier_transactions', 'deleted_at')) {
            Schema::table('supplier_transactions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to transactions table
        if (Schema::hasTable('transactions') && !Schema::hasColumn('transactions', 'deleted_at')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to companies table
        if (Schema::hasTable('companies') && !Schema::hasColumn('companies', 'deleted_at')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop soft deletes from all tables
        $tables = [
            'house_types',
            'income_types',
            'expense_types',
            'incomes',
            'expenses',
            'opening_balances',
            'bank_accounts',
            'suppliers',
            'supplier_transactions',
            'transactions',
            'companies',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
