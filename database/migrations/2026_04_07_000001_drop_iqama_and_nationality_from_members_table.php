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
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'iqama_no')) {
                $table->dropColumn('iqama_no');
            }

            if (Schema::hasColumn('members', 'nationality')) {
                $table->dropColumn('nationality');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'iqama_no')) {
                $table->string('iqama_no')->nullable();
            }

            if (!Schema::hasColumn('members', 'nationality')) {
                $table->string('nationality')->nullable();
            }
        });
    }
};
