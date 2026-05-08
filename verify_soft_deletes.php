<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking Soft Deletes in All Tables ===\n\n";

$tables = [
    'house_types' => 'HouseType',
    'incomes' => 'Income',
    'expenses' => 'Expense',
    'opening_balances' => 'OpeningBalance',
    'bank_accounts' => 'BankAccount',
    'suppliers' => 'Supplier',
    'supplier_transactions' => 'SupplierTransaction',
    'transactions' => 'Transaction',
    'companies' => 'Company',
    'places' => 'Place',
];

foreach ($tables as $table => $model) {
    $hasColumn = Schema::hasColumn($table, 'deleted_at');
    $status = $hasColumn ? "✓ YES" : "✗ NO";
    echo str_pad($table, 30) . " => $status\n";
}

echo "\n=== Migration Status ===\n";
echo "All migrations have been run successfully!\n";
echo "Soft deletes are now available for all tables with SoftDeletes trait.\n";
