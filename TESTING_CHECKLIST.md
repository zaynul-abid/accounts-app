# 🧪 Easy Testing Checklist - Mahallu Website

**Last Updated:** May 4, 2026

---

## 🚀 Quick Start (5-10 Minutes)

### Option 1: Automated Tests (Recommended)

**Windows Users:**
```batch
cd d:\laragon\www\mahallu-website
test_quick.bat
```

**Linux/Mac Users:**
```bash
cd /path/to/mahallu-website
bash test_quick.sh
```

**What it does:**
- ✅ Clears all caches
- ✅ Checks migration status
- ✅ Verifies all tables exist
- ✅ Counts records in each table
- ✅ Validates data integrity

### Option 2: Manual Testing (15-20 Minutes)

Follow the checklist below to test each feature manually.

---

## 📝 Complete Manual Testing Checklist

### Phase 1: Initial Setup (2 minutes)
```
□ Server is running (http://localhost:8000 loads)
□ Can login to admin dashboard
□ Sidebar navigation visible
□ No console errors (F12)
```

### Phase 2: House Creation (5 minutes)

**Steps:**
1. Go to `/house-creations` (or click "House Creation" in sidebar)
2. Click "Save Property" or "New House"

**Test Cases:**
```
□ Form loads without errors
□ Can fill all required fields:
  └ □ Sl Number
  └ □ Registration Date
  └ □ Place (dropdown works)
  └ □ House Owner
  └ □ House Name
  └ □ Jamath House No
  └ □ House Type
  └ □ Ward No
  └ □ House No
  └ □ Mobile No

□ Can click "Add Place" button and create new place
□ Can click "Add House Type" button and create new type
□ Form submission successful
□ House appears in table below
□ Can see Edit button for house
□ Can see Delete button for house
□ Message "Property created successfully" appears
```

**Result:** ✅ or ❌

---

### Phase 3: Member Creation (10 minutes)

**Steps:**
1. Go to `/member/create` (or click "Member Creation" in sidebar)
2. Click "Save Member"

**Test Cases:**
```
HOUSE SELECTION:
□ House search box works (type 2+ characters)
□ House suggestions dropdown appears
□ Can select house from dropdown
□ House details auto-populate:
  └ □ Place
  └ □ House No
  └ □ Jamath No
  └ □ House Name
  └ □ House Owner
  └ □ Phone/Mobile

MEMBER DETAILS:
□ Can fill member name (required)
□ Marital status dropdown works
□ Spouse name field appears when "Married" selected
□ Spouse name field hides when "Single" selected
□ DOB field works
□ Age auto-calculates from DOB
□ Can toggle "Enter age manually" checkbox
□ Gender dropdown works
□ Blood group dropdown works
□ All qualification dropdowns work
□ Can create new relation/qualification inline

SUBSCRIPTION SECTION (CRITICAL!):
□ Subscription checkbox toggles section visibility
□ When checked, shows:
  └ □ Amount field
  └ □ Type radio buttons (Monthly/Yearly)
  └ □ Default checkbox
□ When unchecked, hides these fields

FORM SUBMISSION:
□ Form validates required fields
□ Shows error if house not selected
□ Shows error if member name missing
□ Form submits successfully with subscription enabled
□ Form submits successfully with subscription disabled
□ Success message appears
□ Member appears in sidebar list
```

**Result:** ✅ or ❌

---

### Phase 4: Member Report Auto-Generation (CRITICAL! - 15 minutes)

**Setup:**
1. Create a new member WITH subscription enabled
2. Amount: 1500.00
3. Type: Yearly
4. Narration: "Test Annual Fee"

**Test Cases:**

```
AUTOMATIC REPORT CREATION:
□ After member creation, go to /member-reports
□ A new report appears in the list
□ Receipt number format: MR-YYYYMMDD-XXXXX
□ Example: MR-20260504-00001

REPORT DETAILS:
□ Receipt No: Auto-generated ✅
□ Date: Today's date
□ Member Name: Matches created member
□ Transaction Type: "Yearly Subscription" (for Yearly type)
                  OR "Monthly Subscription" (for Monthly type)
□ Posting Year: "2026" (for Yearly)
                OR "2026-27" (for Monthly)
□ Debit Amount: 1500.00 (matches subscription amount)
□ Credit Amount: 0.00
□ Balance: 1500.00 (debit - credit)
□ Status: "completed"
□ Description: "Test Annual Fee" (from narration)

MEMBER-SPECIFIC REPORTS:
□ Go to /member-reports/member/{member-id}
□ Page title shows member name
□ Summary cards show:
  └ □ Total Debit: 1500.00
  └ □ Total Credit: 0.00
  └ □ Balance: 1500.00
  └ □ Total Transactions: 1
□ Report appears in table
```

**Result:** ✅ or ❌

---

### Phase 5: Manual Report Creation (10 minutes)

**Steps:**
1. Go to `/member-reports/create`
2. Select a member
3. Fill in transaction details

**Test Cases:**

```
FORM FIELDS:
□ Member dropdown works
□ Receipt number auto-generates on load
□ Can change date
□ Transaction type dropdown works
□ Can enter posting year
□ Can select status (pending/completed/cancelled)
□ Can enter description

FINANCIAL CALCULATION:
□ Balance auto-calculates: Balance = Debit - Credit
□ Debit: 500 → Balance shows 500
□ Debit: 500, Credit: 200 → Balance shows 300
□ Credit only: Debit: 0, Credit: 500 → Balance shows -500

FORM SUBMISSION:
□ Form validates required fields
□ Form submits successfully
□ Success message appears
□ Report appears in reports list
```

**Result:** ✅ or ❌

---

### Phase 6: Report Editing & Deletion (8 minutes)

**Test Cases:**

```
EDIT REPORT:
□ Click edit button on any report
□ Form loads with existing data
□ Can modify debit amount
□ Can modify credit amount
□ Balance updates automatically
□ Can change status
□ Form submits successfully
□ Changes appear in list

DELETE REPORT:
□ Click delete button on any report
□ Confirmation dialog appears
□ Clicking "Yes" deletes report
□ Report disappears from list
□ Report can still be recovered (soft delete)
□ No error messages
```

**Result:** ✅ or ❌

---

### Phase 7: Lookup Masters (5 minutes)

**Steps:**
1. Go to `/admin/lookups/` or click "Lookup Masters" → Relations

**Test Cases:**

```
□ Can view relations list
□ Can click to other lookup types (Occupations, etc.)
□ Can create new lookup item
□ Can edit existing item
□ Can delete item
□ Status toggle (Active/Inactive) works
□ Pagination works if many items
```

**Result:** ✅ or ❌

---

### Phase 8: Dashboard (3 minutes)

**Test Cases:**

```
□ Dashboard loads without errors
□ All widgets display
□ Navigation links work
□ No missing images/icons
□ Responsive on mobile (F12 → Mobile view)
```

**Result:** ✅ or ❌

---

## 🔍 Error Checking Checklist

### Browser Console Errors (F12)
```
□ Press F12 in browser
□ Go to Console tab
□ No red error messages
□ No warnings about missing files
```

### Laravel Logs
```
Command: tail -f storage/logs/laravel.log

□ No error messages
□ No SQL syntax errors
□ No "undefined" errors
□ No class not found errors
```

### Database Issues
```
Error: "Table 'X' doesn't exist"
Solution: Run php artisan migrate

Error: "Column 'X' doesn't exist"
Solution: Check if migration fully ran: php artisan migrate:status

Error: "Foreign key constraint failed"
Solution: Ensure parent record exists before creating child
```

---

## 📊 Data Validation Checklist

### Receipt Numbers
```
Format: MR-YYYYMMDD-XXXXX

Examples of CORRECT format:
✅ MR-20260504-00001
✅ MR-20260504-00002
✅ MR-20260505-00001

Examples of INCORRECT format:
❌ MR-May-04-001 (wrong date format)
❌ MR-2026050400001 (missing dash)
❌ MR-2026-05-04-001 (wrong format)

Check in database:
□ All receipt numbers follow format
□ All receipt numbers are unique (no duplicates)
□ Counter increments daily
```

### Balance Calculations
```
Test Cases:
□ Debit: 1000, Credit: 0 → Balance: 1000 ✅
□ Debit: 1000, Credit: 500 → Balance: 500 ✅
□ Debit: 1000, Credit: 1000 → Balance: 0 ✅
□ Debit: 0, Credit: 500 → Balance: -500 ✅

Check: Balance = Debit - Credit (always)
```

### Data Integrity
```
□ No NULL values in required fields
□ All foreign keys link to valid parent records
□ No orphaned records (member_reports without member)
□ Soft deletes working (deleted_at filled on delete)
□ No negative amounts in debit/credit (unless intentional)
```

---

## 🎯 Test Scenarios

### Scenario 1: Create Complete Flow
```
1. Create House → Success ✅
2. Create Member (with subscription) → Success ✅
3. Verify Report Auto-Created → Success ✅
4. View Member Reports → Success ✅
5. Check Receipt Number Format → Correct ✅
6. Verify Balance Calculation → Correct ✅
```

### Scenario 2: Member Without Subscription
```
1. Create Member WITHOUT subscription
2. Check /member-reports
3. No report should be created ✅
```

### Scenario 3: Multiple Members, One House
```
1. Create House
2. Create Member 1 (with subscription)
3. Create Member 2 (with subscription)
4. Check /member-reports
5. Both reports should exist ✅
6. Each has unique receipt number ✅
7. Member details link correct ✅
```

---

## ✅ Final Sign-Off

When ALL items are checked:

```
Date Tested: ________________
Tested By: __________________
Result: □ PASS  □ FAIL

Issues Found:
_________________________________
_________________________________
_________________________________

Notes:
_________________________________
_________________________________
```

---

## 🆘 Common Issues & Quick Fixes

| Issue | Quick Fix |
|-------|-----------|
| Table doesn't exist | `php artisan migrate` |
| Changes don't appear | `php artisan cache:clear` |
| Blank page | Check `storage/logs/laravel.log` |
| Member creation fails | Ensure house is selected |
| Report not auto-creating | Ensure subscription=1 in form |
| Balance wrong | Check: Balance = Debit - Credit |
| Receipt number invalid | Format should be: MR-YYYYMMDD-XXXXX |
| Form validation error | Check required fields marked with * |
| Soft delete not working | Ensure model uses SoftDeletes trait |

---

## 📞 Get Help

1. **Check Logs First**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Clear Everything**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

3. **Check Database Connection**
   ```bash
   php artisan migrate:status
   ```

4. **Review Testing Guide**
   - Open: `PROJECT_TESTING_GUIDE.md`
   - More detailed tests and solutions

---

**Status:** ✅ Ready for Testing  
**Last Updated:** May 4, 2026  
**Total Testing Time:** ~60 minutes

