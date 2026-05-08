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
        $this->updateLookupTable('relations');
        $this->updateLookupTable('qualifications');
        $this->updateLookupTable('islamic_qualifications');
        $this->updateLookupTable('occupations');
        $this->updateLookupTable('job_locations');

        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'house_id')) {
                $table->unsignedBigInteger('house_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('members', 'sl_number')) {
                $table->string('sl_number')->nullable();
            }
            if (!Schema::hasColumn('members', 'date')) {
                $table->date('date')->nullable();
            }
            if (!Schema::hasColumn('members', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('members', 'father_name')) {
                $table->string('father_name')->nullable();
            }
            if (!Schema::hasColumn('members', 'mother_name')) {
                $table->string('mother_name')->nullable();
            }
            if (!Schema::hasColumn('members', 'marital_status')) {
                $table->string('marital_status')->nullable();
            }
            if (!Schema::hasColumn('members', 'spouse_name')) {
                $table->string('spouse_name')->nullable();
            }
            if (!Schema::hasColumn('members', 'relation_id')) {
                $table->unsignedBigInteger('relation_id')->nullable();
            }
            if (!Schema::hasColumn('members', 'dob')) {
                $table->date('dob')->nullable();
            }
            if (!Schema::hasColumn('members', 'age')) {
                $table->integer('age')->nullable();
            }
            if (!Schema::hasColumn('members', 'gender')) {
                $table->string('gender')->nullable();
            }
            if (!Schema::hasColumn('members', 'blood_group')) {
                $table->string('blood_group')->nullable();
            }
            if (!Schema::hasColumn('members', 'disabled')) {
                $table->boolean('disabled')->default(false);
            }
            if (!Schema::hasColumn('members', 'mobile_number')) {
                $table->string('mobile_number')->nullable();
            }
            if (!Schema::hasColumn('members', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable();
            }
            if (!Schema::hasColumn('members', 'islamic_qualification_id')) {
                $table->unsignedBigInteger('islamic_qualification_id')->nullable();
            }
            if (!Schema::hasColumn('members', 'qualification_id')) {
                $table->unsignedBigInteger('qualification_id')->nullable();
            }
            if (!Schema::hasColumn('members', 'occupation_id')) {
                $table->unsignedBigInteger('occupation_id')->nullable();
            }
            if (!Schema::hasColumn('members', 'job_location_id')) {
                $table->unsignedBigInteger('job_location_id')->nullable();
            }
            if (!Schema::hasColumn('members', 'subscription')) {
                $table->boolean('subscription')->default(false);
            }
            if (!Schema::hasColumn('members', 'default_subscription')) {
                $table->boolean('default_subscription')->default(false);
            }
            if (!Schema::hasColumn('members', 'subscription_amount')) {
                $table->decimal('subscription_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('members', 'subscription_type')) {
                $table->string('subscription_type')->nullable();
            }
            if (!Schema::hasColumn('members', 'narration')) {
                $table->text('narration')->nullable();
            }
            if (!Schema::hasColumn('members', 'op_amount')) {
                $table->decimal('op_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('members', 'active')) {
                $table->boolean('active')->default(true);
            }
            if (!Schema::hasColumn('members', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left minimal: this migration is a schema repair migration.
    }

    private function updateLookupTable(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn($tableName, 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn($tableName, 'active')) {
                $table->boolean('active')->default(true);
            }
            if (!Schema::hasColumn($tableName, 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }
};
