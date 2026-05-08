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
        Schema::table('member_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('member_reports', 'sl_no')) {
                $table->string('sl_no')->nullable()->after('id');
            }
            if (!Schema::hasColumn('member_reports', 'include_previous_due')) {
                $table->boolean('include_previous_due')->default(false)->after('credit');
            }
            if (!Schema::hasColumn('member_reports', 'due_amount')) {
                $table->decimal('due_amount', 10, 2)->nullable()->after('include_previous_due');
            }
            if (!Schema::hasColumn('member_reports', 'received_from')) {
                $table->string('received_from')->nullable()->after('due_amount');
            }
            if (!Schema::hasColumn('member_reports', 'receipt_mode')) {
                $table->string('receipt_mode')->nullable()->after('received_from');
            }
            if (!Schema::hasColumn('member_reports', 'receipt_account_id')) {
                $table->unsignedBigInteger('receipt_account_id')->nullable()->after('receipt_mode');
            }
        });

        Schema::table('member_reports', function (Blueprint $table) {
            if (Schema::hasColumn('member_reports', 'receipt_account_id')) {
                $table->foreign('receipt_account_id')
                    ->references('id')
                    ->on('receipt_accounts')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_reports', function (Blueprint $table) {
            if (Schema::hasColumn('member_reports', 'receipt_account_id')) {
                try {
                    $table->dropForeign(['receipt_account_id']);
                } catch (\Throwable $th) {
                    // Keep rollback resilient for unknown existing schema states.
                }
            }

            $columns = [
                'sl_no',
                'include_previous_due',
                'due_amount',
                'received_from',
                'receipt_mode',
                'receipt_account_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('member_reports', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

