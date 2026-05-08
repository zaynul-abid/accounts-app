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
        Schema::create('member_reports', function (Blueprint $table) {
            $table->id();

            // Foreign Keys & References
            $table->unsignedBigInteger('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');

            // Core Fields
            $table->string('receipt_no')->unique();                    // Unique receipt number
            $table->date('date')->default(now());                      // Transaction date
            $table->string('name')->nullable();                        // Member name (denormalized for reporting)
            $table->string('transaction_type');                        // e.g., "Year Subscription", "Monthly Subscription", "Payment", "Refund"
            $table->string('posting_year')->nullable();                // e.g., "2017-2018"
            $table->text('description')->nullable();                   // Details about transaction

            // Financial Fields
            $table->decimal('debit', 10, 2)->nullable();               // Amount owed/charged
            $table->decimal('credit', 10, 2)->nullable();              // Amount paid/received
            $table->decimal('balance', 10, 2)->default(0);             // Running balance

            // Additional Fields
            $table->string('payment_method')->nullable();              // Cash, Check, Online, Bank Transfer, etc.
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');  // Transaction status
            $table->text('remarks')->nullable();                       // Additional notes/comments

            // Audit Trail
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_reports');
    }
};
