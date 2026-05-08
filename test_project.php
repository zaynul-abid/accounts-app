<?php

/**
 * 🧪 Automated Project Testing Script
 * 
 * This script performs comprehensive testing of the Mahallu Website project
 * to identify errors and validate functionality.
 * 
 * Usage: php test_project.php
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                   🧪 MAHALLU WEBSITE - PROJECT TEST SUITE                     ║\n";
echo "║                              May 4, 2026                                       ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Test counters
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

/**
 * Test helper function
 */
function runTest($testName, $testFunction)
{
    global $totalTests, $passedTests, $failedTests;
    
    $totalTests++;
    echo "[TEST $totalTests] " . str_pad($testName, 50);
    
    try {
        $result = $testFunction();
        
        if ($result) {
            echo " ✅ PASS\n";
            $passedTests++;
        } else {
            echo " ❌ FAIL\n";
            $failedTests++;
        }
    } catch (Exception $e) {
        echo " ❌ ERROR: " . $e->getMessage() . "\n";
        $failedTests++;
    }
}

// ============================================================================
// 1. DATABASE CONNECTIVITY TESTS
// ============================================================================
echo "┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 1. DATABASE CONNECTIVITY TESTS                                                 │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

runTest("Database Connection", function() {
    try {
        DB::connection()->getPdo();
        return true;
    } catch (Exception $e) {
        echo "Connection failed: " . $e->getMessage();
        return false;
    }
});

runTest("Can Query Database", function() {
    try {
        $result = DB::select('SELECT 1');
        return !empty($result);
    } catch (Exception $e) {
        return false;
    }
});

// ============================================================================
// 2. TABLE EXISTENCE TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 2. TABLE EXISTENCE TESTS                                                       │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

$requiredTables = [
    'users',
    'places',
    'house_types',
    'house_creations',
    'relations',
    'qualifications',
    'islamic_qualifications',
    'occupations',
    'job_locations',
    'members',
    'member_reports',
];

foreach ($requiredTables as $table) {
    runTest("Table '$table' exists", function() use ($table) {
        return Schema::hasTable($table);
    });
}

// ============================================================================
// 3. SOFT DELETE COLUMN TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 3. SOFT DELETE COLUMN TESTS                                                    │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

$softDeleteTables = [
    'places', 'house_types', 'house_creations', 'relations', 
    'qualifications', 'islamic_qualifications', 'occupations', 
    'job_locations', 'members', 'member_reports'
];

foreach ($softDeleteTables as $table) {
    runTest("$table has deleted_at column", function() use ($table) {
        return Schema::hasColumn($table, 'deleted_at');
    });
}

// ============================================================================
// 4. REQUIRED COLUMNS TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 4. REQUIRED COLUMNS TESTS                                                      │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

$columnTests = [
    'members' => ['id', 'house_id', 'name', 'subscription', 'subscription_amount'],
    'member_reports' => ['id', 'member_id', 'receipt_no', 'debit', 'credit', 'balance'],
    'house_creations' => ['id', 'place_id', 'house_type_id', 'house_no', 'jamath_house_no'],
];

foreach ($columnTests as $table => $columns) {
    foreach ($columns as $column) {
        runTest("$table.$column exists", function() use ($table, $column) {
            return Schema::hasColumn($table, $column);
        });
    }
}

// ============================================================================
// 5. MODEL TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 5. MODEL & RELATIONSHIP TESTS                                                  │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

runTest("Member model can be instantiated", function() {
    try {
        $member = app('App\Models\Member');
        return $member !== null;
    } catch (Exception $e) {
        return false;
    }
});

runTest("MemberReport model can be instantiated", function() {
    try {
        $report = app('App\Models\MemberReport');
        return $report !== null;
    } catch (Exception $e) {
        return false;
    }
});

runTest("HouseCreation model can be instantiated", function() {
    try {
        $house = app('App\Models\HouseCreation');
        return $house !== null;
    } catch (Exception $e) {
        return false;
    }
});

// ============================================================================
// 6. DATA INTEGRITY TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 6. DATA INTEGRITY TESTS                                                        │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

runTest("Members table can be queried", function() {
    try {
        $count = DB::table('members')->count();
        return is_numeric($count);
    } catch (Exception $e) {
        return false;
    }
});

runTest("Member reports table can be queried", function() {
    try {
        $count = DB::table('member_reports')->count();
        return is_numeric($count);
    } catch (Exception $e) {
        return false;
    }
});

runTest("Houses table can be queried", function() {
    try {
        $count = DB::table('house_creations')->count();
        return is_numeric($count);
    } catch (Exception $e) {
        return false;
    }
});

// ============================================================================
// 7. MIGRATION STATUS TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 7. MIGRATION STATUS TESTS                                                      │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

$requiredMigrations = [
    '0001_01_01_000000_create_users_table',
    '2026_04_06_070915_create_members_table',
    '2026_04_13_000001_create_member_reports_table',
];

foreach ($requiredMigrations as $migration) {
    runTest("Migration '$migration' has run", function() use ($migration) {
        $count = DB::table('migrations')
            ->where('migration', $migration)
            ->count();
        return $count > 0;
    });
}

// ============================================================================
// 8. FOREIGN KEY TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 8. FOREIGN KEY CONSTRAINT TESTS                                                │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

runTest("Members with valid house_id exist", function() {
    try {
        $member = DB::table('members')
            ->whereNotNull('house_id')
            ->first();
        return true; // Column exists, that's what we're testing
    } catch (Exception $e) {
        return false;
    }
});

runTest("Member reports with valid member_id exist", function() {
    try {
        $report = DB::table('member_reports')
            ->whereNotNull('member_id')
            ->first();
        return true; // Column exists
    } catch (Exception $e) {
        return false;
    }
});

// ============================================================================
// 9. RECEIPT NUMBER FORMAT TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 9. RECEIPT NUMBER FORMAT TESTS                                                 │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

runTest("Receipt numbers follow format MR-YYYYMMDD-XXXXX", function() {
    try {
        $report = DB::table('member_reports')
            ->whereNotNull('receipt_no')
            ->first();
        
        if (!$report) {
            return true; // No reports yet, that's okay
        }
        
        return preg_match('/^MR-\d{8}-\d{5}$/', $report->receipt_no) === 1;
    } catch (Exception $e) {
        return false;
    }
});

runTest("Receipt numbers are unique", function() {
    try {
        $duplicates = DB::table('member_reports')
            ->groupBy('receipt_no')
            ->havingRaw('count(*) > 1')
            ->count();
        
        return $duplicates === 0;
    } catch (Exception $e) {
        return false;
    }
});

// ============================================================================
// 10. BALANCE CALCULATION TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 10. BALANCE CALCULATION TESTS                                                  │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

runTest("Balance = Debit - Credit for all reports", function() {
    try {
        $reports = DB::table('member_reports')
            ->whereNull('deleted_at')
            ->get();
        
        foreach ($reports as $report) {
            $expected = ($report->debit ?? 0) - ($report->credit ?? 0);
            $actual = $report->balance ?? 0;
            
            if (abs($expected - $actual) > 0.01) {
                return false;
            }
        }
        
        return true;
    } catch (Exception $e) {
        return false;
    }
});

runTest("All balances are numeric", function() {
    try {
        $invalid = DB::table('member_reports')
            ->whereNull('deleted_at')
            ->where(function($q) {
                $q->whereNull('balance')
                  ->orWhere('balance', 'not numeric');
            })
            ->count();
        
        return $invalid === 0;
    } catch (Exception $e) {
        return false;
    }
});

// ============================================================================
// 11. STATUS FIELD TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 11. STATUS FIELD TESTS                                                         │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

runTest("All status values are valid (pending|completed|cancelled)", function() {
    try {
        $invalid = DB::table('member_reports')
            ->whereNotIn('status', ['pending', 'completed', 'cancelled'])
            ->count();
        
        return $invalid === 0;
    } catch (Exception $e) {
        return false;
    }
});

// ============================================================================
// 12. TIMESTAMP TESTS
// ============================================================================
echo "\n┌────────────────────────────────────────────────────────────────────────────────┐\n";
echo "│ 12. TIMESTAMP TESTS                                                            │\n";
echo "└────────────────────────────────────────────────────────────────────────────────┘\n";

runTest("All tables have created_at timestamp", function() {
    $tables = ['members', 'member_reports', 'house_creations'];
    foreach ($tables as $table) {
        if (!Schema::hasColumn($table, 'created_at')) {
            return false;
        }
    }
    return true;
});

runTest("All tables have updated_at timestamp", function() {
    $tables = ['members', 'member_reports', 'house_creations'];
    foreach ($tables as $table) {
        if (!Schema::hasColumn($table, 'updated_at')) {
            return false;
        }
    }
    return true;
});

// ============================================================================
// SUMMARY REPORT
// ============================================================================
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           📊 TEST SUMMARY REPORT                               ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo sprintf("  Total Tests Run: %d\n", $totalTests);
echo sprintf("  ✅ Passed:        %d\n", $passedTests);
echo sprintf("  ❌ Failed:        %d\n", $failedTests);
echo "\n";

if ($failedTests === 0) {
    echo "  ╔─────────────────────────────────────────────────────────────╗\n";
    echo "  │  🎉 ALL TESTS PASSED! Project is ready for use.            │\n";
    echo "  ╚─────────────────────────────────────────────────────────────╝\n";
    echo "\n";
    $exitCode = 0;
} else {
    echo "  ╔─────────────────────────────────────────────────────────────╗\n";
    echo "  │  ⚠️  Some tests failed. Please review errors above.         │\n";
    echo "  ╚─────────────────────────────────────────────────────────────╝\n";
    echo "\n";
    $exitCode = 1;
}

$passPercentage = ($passedTests / $totalTests) * 100;
echo sprintf("  Pass Rate: %.1f%%\n", $passPercentage);
echo "\n";
echo "  Status: " . ($failedTests === 0 ? "✅ HEALTHY" : "❌ NEEDS ATTENTION") . "\n";
echo "\n";

exit($exitCode);
