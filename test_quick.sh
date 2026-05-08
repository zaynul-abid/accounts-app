#!/bin/bash

# 🧪 Quick Testing Script for Mahallu Website
# Run from project root: bash test_quick.sh

echo ""
echo "╔════════════════════════════════════════════════════════════════════════════════╗"
echo "║                   🧪 MAHALLU WEBSITE - QUICK TEST SUITE                       ║"
echo "║                              May 4, 2026                                       ║"
echo "╚════════════════════════════════════════════════════════════════════════════════╝"
echo ""

PASSED=0
FAILED=0

# Helper function to run tests
test_cmd() {
    local name=$1
    local cmd=$2
    
    echo -n "[TEST] $name ... "
    
    if eval "$cmd" > /dev/null 2>&1; then
        echo "✅ PASS"
        ((PASSED++))
    else
        echo "❌ FAIL"
        ((FAILED++))
    fi
}

echo "┌────────────────────────────────────────────────────────────────────────────────┐"
echo "│ 1. CACHE & CONFIG CLEANUP                                                      │"
echo "└────────────────────────────────────────────────────────────────────────────────┘"
echo ""

echo "Clearing caches..."
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo "✅ Caches cleared"
echo ""

echo "┌────────────────────────────────────────────────────────────────────────────────┐"
echo "│ 2. MIGRATION STATUS TESTS                                                      │"
echo "└────────────────────────────────────────────────────────────────────────────────┘"
echo ""

echo "Migration Status:"
php artisan migrate:status 2>/dev/null | grep -E "(create_users_table|create_members_table|create_member_reports_table)" | head -20

echo ""
echo "┌────────────────────────────────────────────────────────────────────────────────┐"
echo "│ 3. DATABASE TESTS (via tinker)                                                 │"
echo "└────────────────────────────────────────────────────────────────────────────────┘"
echo ""

php artisan tinker --execute='
$tables = [
    "users", "places", "house_types", "house_creations",
    "relations", "qualifications", "islamic_qualifications",
    "occupations", "job_locations", "members", "member_reports"
];

echo "Table Status:\n";
$passed = 0;
$failed = 0;

foreach ($tables as $table) {
    $exists = Schema::hasTable($table);
    $status = $exists ? "✅" : "❌";
    echo "$status $table\n";
    if ($exists) $passed++; else $failed++;
}

echo "\n";
echo "Summary: $passed passed, $failed failed\n";
' 2>/dev/null

echo ""
echo "┌────────────────────────────────────────────────────────────────────────────────┐"
echo "│ 4. SOFT DELETES VERIFICATION                                                   │"
echo "└────────────────────────────────────────────────────────────────────────────────┘"
echo ""

php artisan tinker --execute='
$tables = ["members", "member_reports", "house_creations", "places"];

echo "Soft Delete Columns:\n";
$passed = 0;
$failed = 0;

foreach ($tables as $table) {
    $has_deleted = Schema::hasColumn($table, "deleted_at");
    $status = $has_deleted ? "✅" : "❌";
    echo "$status $table.deleted_at\n";
    if ($has_deleted) $passed++; else $failed++;
}

echo "\nSummary: $passed passed, $failed failed\n";
' 2>/dev/null

echo ""
echo "┌────────────────────────────────────────────────────────────────────────────────┐"
echo "│ 5. DATA COUNTS                                                                 │"
echo "└────────────────────────────────────────────────────────────────────────────────┘"
echo ""

php artisan tinker --execute='
echo "Data Statistics:\n\n";

try {
    $member_count = DB::table("members")->whereNull("deleted_at")->count();
    echo "✅ Members: $member_count\n";
} catch (Exception $e) {
    echo "❌ Cannot query members\n";
}

try {
    $report_count = DB::table("member_reports")->whereNull("deleted_at")->count();
    echo "✅ Member Reports: $report_count\n";
} catch (Exception $e) {
    echo "❌ Cannot query member_reports\n";
}

try {
    $house_count = DB::table("house_creations")->whereNull("deleted_at")->count();
    echo "✅ Houses: $house_count\n";
} catch (Exception $e) {
    echo "❌ Cannot query houses\n";
}
' 2>/dev/null

echo ""
echo "┌────────────────────────────────────────────────────────────────────────────────┐"
echo "│ 6. RECEIPT NUMBER VALIDATION                                                   │"
echo "└────────────────────────────────────────────────────────────────────────────────┘"
echo ""

php artisan tinker --execute='
echo "Receipt Number Validation:\n\n";

try {
    $reports = DB::table("member_reports")
        ->whereNull("deleted_at")
        ->get();
    
    if ($reports->count() == 0) {
        echo "ℹ️  No reports yet (will be created when members are registered)\n";
    } else {
        echo "Found " . $reports->count() . " reports:\n\n";
        
        foreach ($reports->take(5) as $report) {
            $valid = preg_match("/^MR-\d{8}-\d{5}$/", $report->receipt_no);
            $status = $valid ? "✅" : "❌";
            echo "$status Receipt: {$report->receipt_no}\n";
            echo "   Amount: ₹" . number_format($report->debit, 2) . "\n";
            echo "   Status: {$report->status}\n\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error querying reports: " . $e->getMessage() . "\n";
}
' 2>/dev/null

echo ""
echo "┌────────────────────────────────────────────────────────────────────────────────┐"
echo "│ 7. BALANCE CALCULATION VERIFICATION                                            │"
echo "└────────────────────────────────────────────────────────────────────────────────┘"
echo ""

php artisan tinker --execute='
echo "Balance Calculation Check:\n\n";

try {
    $reports = DB::table("member_reports")
        ->whereNull("deleted_at")
        ->get();
    
    if ($reports->count() == 0) {
        echo "ℹ️  No reports yet to verify balance calculations\n";
    } else {
        $valid_count = 0;
        $invalid_count = 0;
        
        foreach ($reports as $report) {
            $expected = ($report->debit ?? 0) - ($report->credit ?? 0);
            $actual = $report->balance ?? 0;
            $diff = abs($expected - $actual);
            
            if ($diff < 0.01) {
                $valid_count++;
            } else {
                $invalid_count++;
                echo "❌ Receipt {$report->receipt_no}: Expected $expected, Got $actual\n";
            }
        }
        
        echo "✅ Valid: $valid_count | ❌ Invalid: $invalid_count\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
' 2>/dev/null

echo ""
echo "╔════════════════════════════════════════════════════════════════════════════════╗"
echo "║                             ✅ TEST COMPLETE                                   ║"
echo "╚════════════════════════════════════════════════════════════════════════════════╝"
echo ""
echo "📋 Next Steps:"
echo "   1. Visit http://localhost:8000/house-creations to create a test house"
echo "   2. Visit http://localhost:8000/member/create to create a test member"
echo "   3. Enable subscription when creating member (auto-generates report)"
echo "   4. Visit http://localhost:8000/member-reports to view all reports"
echo ""
echo "📊 Check Logs for Errors:"
echo "   tail -f storage/logs/laravel.log"
echo ""
