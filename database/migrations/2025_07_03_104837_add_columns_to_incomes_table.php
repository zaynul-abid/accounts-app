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
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('voucher_number')->after('income_type_id');

            // First define the column with 'after', then apply constraints separately
            $table->foreignId('bank_account_id')
                ->nullable()
                ->after('receipt_amount');

            $table->text('reference_note')->nullable()->after('bank_account_id');

            // Add foreign key constraint separately
            $table->foreign('bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->onDelete('set null');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);

            // Then drop the columns
            $table->dropColumn([
                'voucher_number',
                'bank_account_id',
                'reference_note',
            ]);
        });
    }
};
