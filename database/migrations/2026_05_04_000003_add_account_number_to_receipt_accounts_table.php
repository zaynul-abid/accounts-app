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
        Schema::table('receipt_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('receipt_accounts', 'account_number')) {
                $table->string('account_number')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('receipt_accounts', 'account_number')) {
                $table->dropColumn('account_number');
            }
        });
    }
};
