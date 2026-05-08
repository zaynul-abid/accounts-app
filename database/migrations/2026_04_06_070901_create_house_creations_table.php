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
        Schema::create('house_creations', function (Blueprint $table) {
            $table->id();
            $table->string('sl_number');
            $table->date('registration_date');

            // Foreign Keys
            $table->foreignId('place_id')->constrained('places');
            $table->foreignId('house_type_id')->constrained('house_types');
            $table->unsignedBigInteger('member_id')->nullable();

            $table->string('jamath_house_no');
            $table->string('house_name');
            $table->string('house_owner');
            $table->integer('floors');
            $table->string('ward_no');
            $table->string('house_no');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile');
            $table->decimal('reg_fee', 10, 2);
            $table->boolean('house_sub')->default(true);
            $table->decimal('default_amount', 10, 2)->nullable();
            $table->decimal('due_amount', 10, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('house_creations');
    }
};
