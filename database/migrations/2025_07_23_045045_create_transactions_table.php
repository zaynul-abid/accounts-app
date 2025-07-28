    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         *
         *
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->date('date');
                $table->enum('transaction_mode', ['income', 'expense']);
                $table->foreignId('income_id')->nullable()->constrained('incomes')->nullOnDelete();
                $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
                $table->string('transaction_type');
                $table->string('payment_mode'); // e.g., cash, bank, upi
                $table->foreignId('bank_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
                $table->decimal('debit', 15, 2);
                $table->decimal('credit', 15, 2); // Amount with precision
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();

            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('transactions', function (Blueprint $table) {
                // Drop foreign key constraints
                $table->dropForeign(['company_id']);
                $table->dropForeign(['income_id']);
                $table->dropForeign(['expense_id']);
                $table->dropForeign(['bank_id']);
                $table->dropForeign(['created_by']);
            });

            Schema::dropIfExists('transactions');
        }

    };
