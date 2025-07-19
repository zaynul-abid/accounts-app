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
        // Define the new allowed values
        $newModes = ['cash', 'bank', 'credit', 'touch&go', 'boost', 'duitinow'];

        // Update income table
        Schema::table('incomes', function (Blueprint $table) use ($newModes) {
            // Drop the existing column and recreate it as an ENUM
            $table->dropColumn('receipt_mode');
            $table->enum('receipt_mode', $newModes)->nullable()->after('date_time'); // Adjust 'some_column' to the column before receipt_mode
        });

        // Update expense table
        Schema::table('expenses', function (Blueprint $table) use ($newModes) {
            $table->dropColumn('payment_mode');
            $table->enum('payment_mode', $newModes)->nullable()->after('date_time'); // Adjust 'some_column' to the column before payment_mode
        });
    }

    public function down()
    {
        // Revert to previous state (adjust based on your previous column type)
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('receipt_mode');
            $table->string('receipt_mode')->nullable()->after('date_time'); // Adjust to previous type, e.g., string
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('payment_mode');
            $table->string('payment_mode')->nullable()->after('date_time'); // Adjust to previous type, e.g., string
        });
    }
};
