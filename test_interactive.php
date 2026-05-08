<?php
/**
 * 📋 Interactive Testing Menu for Mahallu Website
 * 
 * Place in project root and run: php test_interactive.php
 */

function clearScreen() {
    system(PHP_OS_FAMILY === 'Windows' ? 'cls' : 'clear');
}

function printHeader() {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║              🧪 MAHALLU WEBSITE - INTERACTIVE TESTING MENU                    ║\n";
    echo "║                              May 4, 2026                                       ║\n";
    echo "╚════════════════════════════════════════════════════════════════════════════════╝\n\n";
}

function printMenu() {
    echo "┌────────────────────────────────────────────────────────────────────────────────┐\n";
    echo "│ SELECT A TEST TO RUN:                                                          │\n";
    echo "├────────────────────────────────────────────────────────────────────────────────┤\n";
    echo "│                                                                                │\n";
    echo "│  1️⃣  Clear All Caches                                                          │\n";
    echo "│  2️⃣  Check Migration Status                                                    │\n";
    echo "│  3️⃣  Verify Database Tables Exist                                              │\n";
    echo "│  4️⃣  Count Records in Each Table                                               │\n";
    echo "│  5️⃣  Validate Soft Deletes                                                     │\n";
    echo "│  6️⃣  Check Member Report Receipt Numbers                                       │\n";
    echo "│  7️⃣  Validate Balance Calculations                                             │\n";
    echo "│  8️⃣  Run ALL Tests                                                             │\n";
    echo "│  9️⃣  Open Testing Guide                                                        │\n";
    echo "│  0️⃣  Exit                                                                      │\n";
    echo "│                                                                                │\n";
    echo "└────────────────────────────────────────────────────────────────────────────────┘\n";
}

function test1_clearCaches() {
    echo "\n▶️  Clearing application caches...\n\n";
    
    echo "   • Cache clear...";
    shell_exec('php artisan cache:clear 2>&1');
    echo " ✅\n";
    
    echo "   • Config clear...";
    shell_exec('php artisan config:clear 2>&1');
    echo " ✅\n";
    
    echo "   • View clear...";
    shell_exec('php artisan view:clear 2>&1');
    echo " ✅\n";
    
    echo "   • Route clear...";
    shell_exec('php artisan route:clear 2>&1');
    echo " ✅\n";
    
    echo "\n✅ All caches cleared successfully!\n";
}

function test2_migrationStatus() {
    echo "\n▶️  Checking migration status...\n\n";
    
    $output = shell_exec('php artisan migrate:status 2>&1');
    echo $output;
}

function test3_verifyTables() {
    echo "\n▶️  Verifying database tables...\n\n";
    
    $code = '
    $tables = [
        "users", "places", "house_types", "house_creations",
        "relations", "qualifications", "islamic_qualifications",
        "occupations", "job_locations", "members", "member_reports"
    ];
    
    $passed = 0;
    $failed = 0;
    
    foreach ($tables as $table) {
        $exists = Schema::hasTable($table);
        if ($exists) {
            echo "✅ Table: $table\n";
            $passed++;
        } else {
            echo "❌ Table: $table (MISSING)\n";
            $failed++;
        }
    }
    
    echo "\n📊 Summary: $passed passed, $failed failed\n";
    ';
    
    shell_exec("php artisan tinker --execute=\"$code\" 2>&1");
}

function test4_countRecords() {
    echo "\n▶️  Counting records in each table...\n\n";
    
    $code = '
    $tables = ["users", "members", "member_reports", "house_creations", "places"];
    
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "📊 $table: $count records\n";
        } catch (Exception $e) {
            echo "❌ $table: Error reading - " . $e->getMessage() . "\n";
        }
    }
    ';
    
    shell_exec("php artisan tinker --execute=\"$code\" 2>&1");
}

function test5_validateSoftDeletes() {
    echo "\n▶️  Validating soft deletes...\n\n";
    
    $code = '
    $tables = ["members", "member_reports", "house_creations", "places", "house_types"];
    
    $passed = 0;
    $failed = 0;
    
    foreach ($tables as $table) {
        $has_deleted = Schema::hasColumn($table, "deleted_at");
        if ($has_deleted) {
            echo "✅ $table.deleted_at column exists\n";
            $passed++;
        } else {
            echo "❌ $table.deleted_at column MISSING\n";
            $failed++;
        }
    }
    
    echo "\n📊 Summary: $passed passed, $failed failed\n";
    ';
    
    shell_exec("php artisan tinker --execute=\"$code\" 2>&1");
}

function test6_receiptNumbers() {
    echo "\n▶️  Checking member report receipt numbers...\n\n";
    
    $code = '
    try {
        $reports = DB::table("member_reports")
            ->whereNull("deleted_at")
            ->get();
        
        if ($reports->count() == 0) {
            echo "ℹ️  No reports yet. Create a member with subscription to test.\n";
            return;
        }
        
        echo "Found " . $reports->count() . " reports:\n\n";
        
        $valid = 0;
        $invalid = 0;
        
        foreach ($reports->take(10) as $report) {
            $matches = preg_match("/^MR-\d{8}-\d{5}$/", $report->receipt_no);
            
            if ($matches) {
                echo "✅ " . $report->receipt_no . " (Amount: ₹" . number_format($report->debit, 2) . ")\n";
                $valid++;
            } else {
                echo "❌ " . $report->receipt_no . " (INVALID FORMAT)\n";
                $invalid++;
            }
        }
        
        echo "\n📊 Valid: $valid | Invalid: $invalid\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
    ';
    
    shell_exec("php artisan tinker --execute=\"$code\" 2>&1");
}

function test7_balanceCalculations() {
    echo "\n▶️  Validating balance calculations...\n\n";
    
    $code = '
    try {
        $reports = DB::table("member_reports")
            ->whereNull("deleted_at")
            ->get();
        
        if ($reports->count() == 0) {
            echo "ℹ️  No reports yet. Create a member with subscription to test.\n";
            return;
        }
        
        echo "Checking balance formulas (Balance = Debit - Credit):\n\n";
        
        $correct = 0;
        $incorrect = 0;
        
        foreach ($reports->take(10) as $report) {
            $expected = ($report->debit ?? 0) - ($report->credit ?? 0);
            $actual = $report->balance ?? 0;
            $diff = abs($expected - $actual);
            
            if ($diff < 0.01) {
                echo "✅ Receipt " . $report->receipt_no . ": ";
                echo "Debit ₹" . number_format($report->debit ?? 0, 2) . " - ";
                echo "Credit ₹" . number_format($report->credit ?? 0, 2) . " = ";
                echo "Balance ₹" . number_format($actual, 2) . "\n";
                $correct++;
            } else {
                echo "❌ Receipt " . $report->receipt_no . ": ";
                echo "Expected ₹$expected but got ₹$actual\n";
                $incorrect++;
            }
        }
        
        echo "\n📊 Correct: $correct | Incorrect: $incorrect\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
    ';
    
    shell_exec("php artisan tinker --execute=\"$code\" 2>&1");
}

function test8_runAllTests() {
    clearScreen();
    printHeader();
    
    echo "▶️  Running ALL tests...\n\n";
    
    test1_clearCaches();
    echo "\n" . str_repeat("-", 80) . "\n";
    
    test2_migrationStatus();
    echo "\n" . str_repeat("-", 80) . "\n";
    
    test3_verifyTables();
    echo "\n" . str_repeat("-", 80) . "\n";
    
    test4_countRecords();
    echo "\n" . str_repeat("-", 80) . "\n";
    
    test5_validateSoftDeletes();
    echo "\n" . str_repeat("-", 80) . "\n";
    
    test6_receiptNumbers();
    echo "\n" . str_repeat("-", 80) . "\n";
    
    test7_balanceCalculations();
    
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║                        ✅ ALL TESTS COMPLETED                                  ║\n";
    echo "╚════════════════════════════════════════════════════════════════════════════════╝\n";
}

function test9_openGuide() {
    $guide = 'PROJECT_TESTING_GUIDE.md';
    $checklist = 'TESTING_CHECKLIST.md';
    
    echo "\n";
    echo "📚 Available Testing Guides:\n\n";
    echo "  1. " . ($guide) . " - Comprehensive testing guide\n";
    echo "  2. " . ($checklist) . " - Quick checklist for manual testing\n\n";
    
    if (PHP_OS_FAMILY === 'Windows') {
        echo "Commands to view guides:\n";
        echo "  Type: $guide\n";
        echo "  Or open in editor: code $guide\n";
    } else {
        echo "Commands to view guides:\n";
        echo "  cat $guide\n";
        echo "  less $guide\n";
    }
}

// Main program
clearScreen();
printHeader();

while (true) {
    printMenu();
    
    echo "Enter your choice (0-9): ";
    $choice = trim(fgets(STDIN));
    
    clearScreen();
    printHeader();
    
    switch ($choice) {
        case '1':
            test1_clearCaches();
            break;
        case '2':
            test2_migrationStatus();
            break;
        case '3':
            test3_verifyTables();
            break;
        case '4':
            test4_countRecords();
            break;
        case '5':
            test5_validateSoftDeletes();
            break;
        case '6':
            test6_receiptNumbers();
            break;
        case '7':
            test7_balanceCalculations();
            break;
        case '8':
            test8_runAllTests();
            break;
        case '9':
            test9_openGuide();
            break;
        case '0':
            echo "\n👋 Goodbye!\n\n";
            exit(0);
        default:
            echo "❌ Invalid choice. Please select 0-9.\n";
            break;
    }
    
    echo "\nPress Enter to continue...";
    fgets(STDIN);
    
    clearScreen();
    printHeader();
}
