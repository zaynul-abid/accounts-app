<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Add company_id to bank_accounts
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
        });

        // Add company_id to expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
        });

        // Add company_id to expense_types
        Schema::table('expense_types', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
        });

        // Add company_id to incomes
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
        });

        // Add company_id to income_types
        Schema::table('income_types', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
        });

        // Add company_id to opening_balances
        Schema::table('opening_balances', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
        });

        // Add company_id to suppliers
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
        });

        // Add company_id to supplier_transactions
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('expense_types', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('income_types', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('opening_balances', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
