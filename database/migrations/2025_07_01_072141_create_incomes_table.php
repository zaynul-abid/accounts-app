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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_type_id')->constrained('income_types');
            $table->dateTime('date_time');
            $table->enum('receipt_mode', ['cash', 'bank','credit']);
            $table->decimal('receipt_amount', 10, 2);
            $table->text('narration')->nullable();
            $table->foreignId('created_by')->constrained('users');

            // Soft deletes
            $table->softDeletes();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
