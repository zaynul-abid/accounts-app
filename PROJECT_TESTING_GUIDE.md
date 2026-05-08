# 🧪 Project Testing Guide - Mahallu Website

**Date:** May 4, 2026  
**Status:** Complete Testing Framework

---

## 📋 Table of Contents

1. [Quick Test Commands](#quick-test-commands)
2. [Manual Testing Checklist](#manual-testing-checklist)
3. [Database Testing](#database-testing)
4. [Feature Testing](#feature-testing)
5. [Common Errors & Solutions](#common-errors--solutions)

---

## ⚡ Quick Test Commands

### 1. **Health Check**
```bash
# Check if Laravel is running correctly
php artisan tinker
>>> DB::connection()->getPdo()
>>> exit
```

### 2. **Database Connection Test**
```bash
# Test database connectivity
php artisan migrate:status
```

### 3. **Clear Cache & Rebuild**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild
php artisan config:cache
php artisan route:cache
```

### 4. **Check All Tables Exist**
```bash
php artisan tinker
>>> Schema::hasTable('members')
>>> Schema::hasTable('member_reports')
>>> Schema::hasTable('house_creations')
>>> exit
```

---

## 📝 Manual Testing Checklist

### A. **Authentication & Authorization**
- [ ] User can login with valid credentials
- [ ] User cannot login with invalid credentials
- [ ] User can logout successfully
- [ ] Unauthenticated users are redirected to login

### B. **House Creation Module**
```
Location: /house-creations or /house/create

Test Cases:
- [ ] Create new house with valid data
- [ ] Validate required fields (sl_number, place, house_no, mobile)
- [ ] Edit existing house
- [ ] Delete house (soft delete)
- [ ] View house list with pagination
- [ ] Search houses by name/number
- [ ] Add new place inline
- [ ] Add new house type inline
```

### C. **Member Creation Module**
```
Location: /member/create

Test Cases:
- [ ] Search and select house
- [ ] Auto-populate house details
- [ ] Create member with required fields
- [ ] Validate marital status & spouse (show/hide spouse field)
- [ ] Calculate age from DOB automatically
- [ ] Enable/disable subscription
- [ ] Create member WITH subscription (auto-generates report)
- [ ] Create member WITHOUT subscription (no report)
- [ ] View house members list (sidebar)
- [ ] Add relation/qualification inline
- [ ] Edit member details
- [ ] Soft delete member
```

### D. **Member Report Module** ⭐ CRITICAL
```
Location: /member-reports

Test Cases:
1. AUTO-GENERATED REPORTS:
   - [ ] Member created with subscription → Report auto-created
   - [ ] Receipt number auto-generated (format: MR-YYYYMMDD-XXXXX)
   - [ ] Debit field = subscription amount
   - [ ] Description field = member narration
   - [ ] Transaction type = "Yearly Subscription" or "Monthly Subscription"
   - [ ] Posting year formatted correctly (2026 or 2026-27)
   - [ ] Status = "completed"
   - [ ] Balance = debit - credit

2. MANUAL REPORTS:
   - [ ] Create new report manually
   - [ ] Receipt number auto-generated
   - [ ] Select member from dropdown
   - [ ] Edit report (update debit/credit/status)
   - [ ] Delete report (soft delete)
   - [ ] View report details

3. MEMBER REPORTS VIEW:
   - [ ] View all member reports (/member-reports)
   - [ ] Filter by member
   - [ ] View member-specific reports (/member-reports/member/{id})
   - [ ] Summary cards show correct totals
   - [ ] Pagination works
   - [ ] Status badges display correctly

4. CALCULATIONS:
   - [ ] Balance = Debit - Credit (auto-calculated)
   - [ ] Total Debit = Sum of all debits
   - [ ] Total Credit = Sum of all credits
   - [ ] Running balance accurate
```

### E. **Lookup Masters**
```
Location: /admin/lookups or /lookups/{type}

Test Cases:
- [ ] View all lookup types (Relations, Occupations, etc.)
- [ ] Create new lookup item
- [ ] Edit lookup item
- [ ] Delete lookup item
- [ ] Active/Inactive toggle
- [ ] Pagination works
```

### F. **Dashboard**
```
Location: /admin/dashboard or /dashboard

Test Cases:
- [ ] Dashboard loads without errors
- [ ] All widgets display correctly
- [ ] Navigation links work
- [ ] Sidebar menu is functional
```

---

## 🗄️ Database Testing

### Check All Tables Exist
```bash
php artisan tinker
```

```php
// Check each table
$tables = [
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

foreach ($tables as $table) {
    $exists = Schema::hasTable($table);
    echo "$table: " . ($exists ? "✓ EXISTS" : "✗ MISSING") . "\n";
}
```

### Check Soft Deletes
```php
// Verify soft_deletes columns exist
$softDeleteTables = [
    'places', 'house_types', 'house_creations',
    'relations', 'qualifications', 'islamic_qualifications',
    'occupations', 'job_locations', 'members', 'member_reports'
];

foreach ($softDeleteTables as $table) {
    $hasDeleted = Schema::hasColumn($table, 'deleted_at');
    echo "$table.deleted_at: " . ($hasDeleted ? "✓ EXISTS" : "✗ MISSING") . "\n";
}
```

### Check Foreign Keys
```php
// Verify foreign key relationships
$tests = [
    'members' => ['house_id', 'relation_id', 'islamic_qualification_id'],
    'member_reports' => ['member_id'],
    'house_creations' => ['place_id', 'house_type_id'],
];

foreach ($tests as $table => $columns) {
    echo "\n$table columns:\n";
    foreach ($columns as $col) {
        $exists = Schema::hasColumn($table, $col);
        echo "  - $col: " . ($exists ? "✓" : "✗") . "\n";
    }
}
```

---

## ✨ Feature Testing

### 1. **House Creation → Member Creation → Report Flow**
```
1. Create House:
   └─ Navigate: /house-creations
   └─ Fill all fields
   └─ Save → Check house list

2. Create Member (with subscription):
   └─ Navigate: /member/create
   └─ Search and select the house
   └─ Fill member details
   └─ Enable subscription
   └─ Enter amount & type
   └─ Save
   └─ Verify: Member created in database

3. Check Auto-Generated Report:
   └─ Navigate: /member-reports
   └─ Find latest report
   └─ Verify:
      ├─ Receipt number exists
      ├─ Amount matches subscription amount
      ├─ Type formatted correctly
      ├─ Debit = subscription amount
      └─ Status = "completed"
```

### 2. **Receipt Number Generation**
```
Test in: Member creation form → Member Report auto-generation

Expected Format: MR-YYYYMMDD-XXXXX
Examples:
- MR-20260504-00001 (May 4, 2026, first report)
- MR-20260504-00002 (May 4, 2026, second report)
- MR-20260505-00001 (May 5, 2026, first report)

Verify:
- [ ] Receipt no is unique
- [ ] Format matches pattern
- [ ] Auto-generated (not manual input)
- [ ] Increments correctly per day
```

### 3. **Balance Calculation**
```
Test in: Create/Edit Member Report

Examples:
1. Debit: 1000, Credit: 0 → Balance: 1000
2. Debit: 1000, Credit: 500 → Balance: 500
3. Debit: 1000, Credit: 1000 → Balance: 0
4. Debit: 0, Credit: 500 → Balance: -500

Verify:
- [ ] Formula: Debit - Credit
- [ ] Auto-calculated on input change
- [ ] Displays correctly in reports
```

---

## 🐛 Common Errors & Solutions

### Error 1: Table 'member_reports' doesn't exist
```
Error: SQLSTATE[42S02]: Base table or view not found: 1146 
Table 'mahallu-website.member_reports' doesn't exist

Solution:
1. Run migration:
   php artisan migrate

2. Verify migration ran:
   php artisan migrate:status
   # Should show: 2026_04_13_000001_create_member_reports_table ... [3] Ran
```

### Error 2: Column 'X' doesn't exist in table 'Y'
```
Solution:
1. Check if migration was fully executed
2. Run: php artisan migrate:refresh --seed (⚠️ Warning: Deletes data!)
3. Or create fresh migration for missing column
```

### Error 3: Foreign Key Constraint Failed
```
Solution:
1. When creating member, ensure house_id exists
2. When creating report, ensure member_id exists
3. Check cascade delete rules in migrations
```

### Error 4: "Call to undefined method" in Controller
```
Solution:
1. Run: php artisan config:clear
2. Run: php artisan cache:clear
3. Restart PHP server
```

### Error 5: Soft Delete Issues
```
Problem: Deleted records still showing up
Solution:
- Make sure Model uses SoftDeletes trait:
  use Illuminate\Database\Eloquent\SoftDeletes;
  class MyModel extends Model {
      use SoftDeletes;
  }
```

---

## 🎯 Testing Workflow (Easy 5-Step Method)

### Step 1: Quick Health Check (2 minutes)
```bash
php artisan migrate:status
php artisan config:clear && php artisan cache:clear
```

### Step 2: Database Verification (2 minutes)
```bash
php artisan tinker
>>> Schema::hasTable('member_reports')
>>> exit
```

### Step 3: Create Test Data (10 minutes)
1. Go to `/house-creations` → Create a test house
2. Go to `/member/create` → Create test member WITH subscription
3. Go to `/member-reports` → Verify report was created

### Step 4: Manual Testing (15 minutes)
Follow the checklist above for critical features:
- [ ] House creation
- [ ] Member creation
- [ ] Auto-report generation
- [ ] Balance calculations

### Step 5: Error Check (5 minutes)
- [ ] Check browser console for JS errors (F12)
- [ ] Check Laravel logs: `storage/logs/laravel.log`
- [ ] Check if all routes load: `/member-reports`, `/member/create`

---

## 📊 Test Coverage Areas

| Area | Priority | Time | Status |
|------|----------|------|--------|
| House Creation | High | 5 min | ✅ Ready |
| Member Creation | High | 10 min | ✅ Ready |
| Member Reports (Auto) | Critical | 10 min | ✅ Ready |
| Member Reports (Manual) | High | 5 min | ✅ Ready |
| Balance Calculations | Critical | 5 min | ✅ Ready |
| Receipt Generation | Critical | 3 min | ✅ Ready |
| Lookup Masters | Medium | 5 min | ✅ Ready |
| Soft Deletes | High | 5 min | ✅ Ready |
| Pagination | Medium | 3 min | ✅ Ready |
| Dashboard | Low | 3 min | ✅ Ready |

**Total Testing Time: ~60 minutes**

---

## 🚀 Testing Best Practices

1. **Always Clear Cache Before Testing**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

2. **Check Logs for Errors**
   ```bash
   # Real-time log monitoring
   tail -f storage/logs/laravel.log
   ```

3. **Test Both Success & Failure Cases**
   - Valid input → Should succeed
   - Invalid input → Should show error
   - Missing required field → Should validate

4. **Test Data Relationships**
   - Create parent record first (House → Member → Report)
   - Verify foreign keys link correctly
   - Test soft deletes cascade

5. **Browser Testing (F12 Console)**
   - Check for JavaScript errors
   - Test responsive design (mobile/tablet)
   - Test form validation messages

---

## ✅ Sign-Off Checklist

Before marking as "Ready for Production":

- [ ] All tables exist in database
- [ ] All foreign keys configured
- [ ] Soft deletes working
- [ ] House creation working
- [ ] Member creation working
- [ ] Member reports auto-generating
- [ ] Receipt numbers unique & formatted
- [ ] Balance calculations correct
- [ ] Pagination functional
- [ ] No console errors
- [ ] No Laravel errors in logs
- [ ] All routes accessible
- [ ] Admin sidebar navigation working

---

## 📞 Support & Debugging

**Check Logs First:**
```bash
tail -f storage/logs/laravel.log
```

**Database Issues:**
```bash
php artisan migrate:status
php artisan migrate --step
```

**Cache Issues:**
```bash
php artisan cache:clear
php artisan config:clear
```

**View Issues:**
```bash
php artisan view:clear
```

---

**Last Updated:** May 4, 2026  
**Status:** ✅ READY FOR TESTING
