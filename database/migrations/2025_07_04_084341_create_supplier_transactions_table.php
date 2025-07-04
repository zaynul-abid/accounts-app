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
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('bill_number')->nullable();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('transaction_mode')->nullable();
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('set null');
            $table->string('transaction_type')->nullable(); // renamed from 'transaction' for clarity
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add check constraint to ensure either debit or credit is set (optional)
            // $table->check('debit > 0 OR credit > 0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['expense_id']);
        });

        Schema::dropIfExists('supplier_transactions');
    }
};
