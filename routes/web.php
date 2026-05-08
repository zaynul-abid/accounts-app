<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\HouseCreationController;
use App\Http\Controllers\Frontend\HouseTypeController;
use App\Http\Controllers\Frontend\MemberCreationController;
use App\Http\Controllers\Frontend\MemberReportController;
use App\Http\Controllers\Frontend\PlaceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LookupMasterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::put('/{user}/password', [AdminUserController::class, 'updatePassword'])->name('password');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });

    Route::resource('house-creations', HouseCreationController::class);

    // Member Creation Routes
    Route::get('/member/create', [MemberCreationController::class, 'index'])->name('members.index');
    Route::get('/member/list', [MemberCreationController::class, 'memberList'])->name('members.list');
    Route::get('/member/search-houses', [MemberCreationController::class, 'searchHouses'])->name('members.searchHouses');
    Route::get('/member/house/{house}/details', [MemberCreationController::class, 'getHouseDetails'])->name('members.getHouseDetails');
    Route::get('/member/house/{house}/members', [MemberCreationController::class, 'getHouseMembers'])->name('members.getHouseMembers');
    Route::post('/member/store', [MemberCreationController::class, 'store'])->name('members.store');
    Route::post('/member/house/{house}/owner/{member}', [MemberCreationController::class, 'changeOwner'])->name('members.changeOwner');
    Route::put('/member/{member}/update', [MemberCreationController::class, 'update'])->name('members.update');
    Route::delete('/member/{member}/destroy', [MemberCreationController::class, 'destroy'])->name('members.destroy');

    Route::post('/member/relation/store', [MemberCreationController::class, 'storeRelation'])->name('relations.store');
    Route::post('/member/islamic-qualification/store', [MemberCreationController::class, 'storeIslamicQualification'])->name('islamic-qualifications.store');
    Route::post('/member/qualification/store', [MemberCreationController::class, 'storeQualification'])->name('qualifications.store');
    Route::post('/member/occupation/store', [MemberCreationController::class, 'storeOccupation'])->name('occupations.store');
    Route::post('/member/job-location/store', [MemberCreationController::class, 'storeJobLocation'])->name('job-locations.store');

    // Aliases for AJAX calls from member creation form
    Route::post('/member/relation/create', [MemberCreationController::class, 'storeRelation'])->name('members.createRelation');
    Route::post('/member/qualification/create', [MemberCreationController::class, 'storeQualification'])->name('members.createQualification');
    Route::post('/member/islamic-qualification/create', [MemberCreationController::class, 'storeIslamicQualification'])->name('members.createIslamicQualification');
    Route::post('/member/occupation/create', [MemberCreationController::class, 'storeOccupation'])->name('members.createOccupation');
    Route::post('/member/job-location/create', [MemberCreationController::class, 'storeJobLocation'])->name('members.createJobLocation');

    // Member Report Routes
    Route::prefix('member-reports')->name('member-reports.')->group(function () {
        Route::get('/', [MemberReportController::class, 'index'])->name('index');
        Route::get('/create', [MemberReportController::class, 'create'])->name('create');
        Route::get('/yearly-payment', [MemberReportController::class, 'createYearlyPayment'])->name('yearly-payment.create');
        Route::get('/yearly-payment/search-members', [MemberReportController::class, 'searchYearlyPaymentMembers'])->name('yearly-payment.search-members');
        Route::post('/store', [MemberReportController::class, 'store'])->name('store');
        Route::post('/yearly-payment', [MemberReportController::class, 'storeYearlyPayment'])->name('yearly-payment.store');
        Route::post('/receipt-accounts', [MemberReportController::class, 'storeReceiptAccount'])->name('receipt-accounts.store');
        Route::get('/search', [MemberReportController::class, 'search'])->name('search');
        Route::get('/{memberReport}', [MemberReportController::class, 'show'])->name('show');
        Route::get('/{memberReport}/edit', [MemberReportController::class, 'edit'])->name('edit');
        Route::put('/{memberReport}', [MemberReportController::class, 'update'])->name('update');
        Route::delete('/{memberReport}', [MemberReportController::class, 'destroy'])->name('destroy');
        Route::get('/member/{member}/summary', [MemberReportController::class, 'getSummary'])->name('summary');
        Route::get('/member/{member}', [MemberReportController::class, 'showByMember'])->name('show-member');
    });

    // House creation lookups
    Route::resource('places', PlaceController::class);
    Route::resource('house-types', HouseTypeController::class);
    Route::post('/places/ajax', [PlaceController::class, 'store'])->name('places.store.ajax');
    Route::post('/house-types/ajax', [HouseTypeController::class, 'store'])->name('house-types.store.ajax');

    Route::prefix('admin/lookups')->name('admin.lookups.')->group(function () {
        Route::get('/{type}', [LookupMasterController::class, 'index'])->name('index');
        Route::post('/{type}', [LookupMasterController::class, 'store'])->name('store');
        Route::put('/{type}/{id}', [LookupMasterController::class, 'update'])->name('update');
        Route::delete('/{type}/{id}', [LookupMasterController::class, 'destroy'])->name('destroy');
    });
});
