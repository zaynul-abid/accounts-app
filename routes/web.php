<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Backend\ExpenseTypeController;
use App\Http\Controllers\Backend\IncomeTypeController;
use App\Http\Controllers\Backend\SupplierTransactionController;
use App\Http\Controllers\Frontend\IncomeExpenseSummaryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index']);
Route::post('/redirect', [HomeController::class, 'redirectToLogin'])->name('welcome.redirect');
// routes/web.php
Route::post('/ajax-authenticate', [AuthenticatedSessionController::class, 'ajaxAuthenticate']);


Route::get('/get-companies', [AuthenticatedSessionController::class, 'getCompanies'])->name('get.companies');
Route::post('/update-company', [AuthenticatedSessionController::class, 'updateCompany'])->name('update.company');




Route::middleware(['auth', 'usertype:admin'])->group(function () {
    Route::get('/admin/dashboard',[DashboardController::class,'adminIndex'])->name('admin.dashboard');
    Route::resource('users', UserController::class)->except(['show', 'edit']);
    Route::resource('companies', \App\Http\Controllers\Backend\CompaniesController::class);
});


Route::middleware(['auth', 'usertype:employee'])->group(function () {
    Route::get('/employee/dashboard',[DashboardController::class,'employeeIndex'])->name('employee.dashboard');
    Route::resource('incomes', \App\Http\Controllers\Frontend\IncomeController::class);
    Route::resource('expenses', \App\Http\Controllers\Frontend\ExpenseController::class);

    Route::resource('income-types', IncomeTypeController::class)->except(['show', 'edit']);
    Route::resource('expense-types', ExpenseTypeController::class)->except(['show', 'edit']);
    Route::resource('opening-balances', \App\Http\Controllers\Backend\OpeningBalanceController::class)->except(['show', 'edit']);
    Route::resource('bank-accounts', \App\Http\Controllers\Backend\BankAccountsController::class)->except(['show', 'edit']);
    Route::resource('suppliers', \App\Http\Controllers\Backend\SupplierController::class)->except(['show', 'edit']);

    Route::get('/income-expense/summary',[IncomeExpenseSummaryController::class,'Index'])->name('income-expense-summary');
    Route::get('/income-expense/summary/data', [IncomeExpenseSummaryController::class, 'getData'])->name('income-expense-summary.data');

    Route::get('/suppliers/details', [SupplierTransactionController::class, 'index'])->name('suppliers.details');

    Route::get('/suppliers/reports', [SupplierTransactionController::class, 'report'])->name('suppliers.reports');

    Route::get('/suppliers/{supplier}/transactions', [SupplierTransactionController::class, 'transactions'])->name('suppliers.transactions');
    Route::post('/suppliers/{supplier}/transactions', [SupplierTransactionController::class, 'storePayment'])->name('suppliers.transactions.store');


});























Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

//Route::middleware(['auth', 'usertype:superadmin'])->group(function () {
//    Route::get('/superadmin/dashboard',[DashboardController::class,'superadminIndex'])->name('superadmin.dashboard');
//
//});
