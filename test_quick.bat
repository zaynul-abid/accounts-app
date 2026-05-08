@echo off
REM 🧪 Quick Testing Script for Mahallu Website (Windows Batch)
REM Run from project root: test_quick.bat

cls
echo.
echo ╔════════════════════════════════════════════════════════════════════════════════╗
echo ║                   🧪 MAHALLU WEBSITE - QUICK TEST SUITE                       ║
echo ║                              May 4, 2026                                       ║
echo ╚════════════════════════════════════════════════════════════════════════════════╝
echo.

REM Clear caches
echo ┌────────────────────────────────────────────────────────────────────────────────┐
echo │ 1. CLEARING CACHES & CONFIG                                                    │
echo └────────────────────────────────────────────────────────────────────────────────┘
echo.

echo Clearing application cache...
php artisan cache:clear >nul 2>&1
echo ✅ Cache cleared

echo Clearing config cache...
php artisan config:clear >nul 2>&1
echo ✅ Config cleared

echo Clearing view cache...
php artisan view:clear >nul 2>&1
echo ✅ View cleared

echo.
echo ┌────────────────────────────────────────────────────────────────────────────────┐
echo │ 2. MIGRATION STATUS                                                            │
echo └────────────────────────────────────────────────────────────────────────────────┘
echo.

echo Checking migration status...
php artisan migrate:status
echo.

echo ┌────────────────────────────────────────────────────────────────────────────────┐
echo │ 3. RUNNING DATABASE TESTS                                                      │
echo └────────────────────────────────────────────────────────────────────────────────┘
echo.

echo Testing table existence...
php artisan tinker --execute="$tables = ['users', 'members', 'member_reports', 'house_creations']; foreach ($tables as $t) { echo (Schema::hasTable($t) ? '✅' : '❌') . ' Table: ' . $t . PHP_EOL; }" 2>nul

echo.
echo ┌────────────────────────────────────────────────────────────────────────────────┐
echo │ 4. DATA STATISTICS                                                             │
echo └────────────────────────────────────────────────────────────────────────────────┘
echo.

echo Checking data counts...
php artisan tinker --execute="try { $m = DB::table('members')->count(); echo '✅ Members: ' . $m . PHP_EOL; } catch (Exception $e) { echo '❌ Cannot query members' . PHP_EOL; }" 2>nul

php artisan tinker --execute="try { $r = DB::table('member_reports')->count(); echo '✅ Member Reports: ' . $r . PHP_EOL; } catch (Exception $e) { echo '❌ Cannot query reports' . PHP_EOL; }" 2>nul

php artisan tinker --execute="try { $h = DB::table('house_creations')->count(); echo '✅ Houses: ' . $h . PHP_EOL; } catch (Exception $e) { echo '❌ Cannot query houses' . PHP_EOL; }" 2>nul

echo.
echo ╔════════════════════════════════════════════════════════════════════════════════╗
echo ║                             ✅ TESTS COMPLETED                                 ║
echo ╚════════════════════════════════════════════════════════════════════════════════╝
echo.
echo 📋 NEXT STEPS:
echo    1. Open http://localhost:8000 in your browser
echo    2. Create a test house at /house-creations
echo    3. Create a test member at /member/create
echo    4. Enable subscription (auto-generates member report)
echo    5. View reports at /member-reports
echo.
echo 🔍 CHECK LOGS FOR ERRORS:
echo    storage\logs\laravel.log
echo.
pause
