<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Expense\ExpenseController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Sale\PaymentController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Unit\UnitController;
use Illuminate\Support\Facades\Route;

Route::controller(ProjectController::class)->group(function () {
    Route::get('/', 'index')->name('projects.index');
    Route::get('/project/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/project/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{project}', 'update')->name('projects.update');
});

Route::prefix('{project:slug}')->middleware('project.session')->group(function () {
    // Selection
    Route::get('/select', [ProjectController::class, 'select'])->name('projects.select');
    Route::get('/dashboard', [ProjectController::class, 'dashboard'])->name('projects.dashboard');
    Route::prefix('Members')->group(function () {
        Route::controller(MemberController::class)->group(function () {
            Route::get('/', 'index')->name('members.index');
            Route::post('/', 'store')->name('members.store');
            Route::post('/add-from-list', 'addFromList')->name('members.addFromList');
            Route::delete('delete/{member}', 'destroy')->name('members.destroy');
            Route::put('/update/{member}', 'update')->name('members.update');
        });
    });
    Route::prefix('Expenses')->group(function () {
        Route::controller(ExpenseController::class)->group(function () {
            Route::get('/', 'index')->name('expenses.index');
            Route::post('/', 'store')->name('expenses.store');
            Route::delete('delete/{expense}', 'destroy')->name('expenses.destroy');
            Route::put('/update/{expense}', 'update')->name('expenses.update');
        });
    });
    Route::prefix('Units')->group(function () {
        Route::controller(UnitController::class)->group(function () {
            Route::get('/', 'index')->name('units.index');
            Route::post('/', 'store')->name('units.store');
            Route::delete('delete/{unit}', 'destroy')->name('units.destroy');
            Route::put('/update/{unit}', 'update')->name('units.update');
        });
    });
    Route::resource('sales', SaleController::class);
        // ── Payments (nested under a sale) ─────────────────────────────────
    Route::prefix('sales/{sale}/payments')->name('payments.')->group(function () {
        Route::get('create',          [PaymentController::class, 'create'])->name('create');
        Route::post('/',              [PaymentController::class, 'store'])->name('store');
        Route::get('{payment}/edit',  [PaymentController::class, 'edit'])->name('edit');
        Route::put('{payment}',       [PaymentController::class, 'update'])->name('update');
        Route::delete('{payment}',    [PaymentController::class, 'destroy'])->name('destroy');
    });
 
    // ── Sale Report ────────────────────────────────────────────────────
    Route::get('sales/{sale}/report', [SaleController::class, 'report'])->name('sales.report');
    Route::resource('clients', ClientController::class)->except(['create', 'edit']);
    Route::prefix('Reports')->group(function () {
        Route::controller(ReportController::class)->group(function () {
            Route::get('/', 'index')->name('reports.index');
            Route::get('/members', 'members')->name('reports.members');
            Route::get('/clients', 'clients')->name('reports.clients');
            Route::get('/units', 'units')->name('reports.units');
            Route::get('/sales', 'sales')->name('reports.sales');
            Route::get('/expenses', 'expenses')->name('reports.expenses');
            Route::get('/overall', 'overall')->name('reports.overall');
            Route::get('/download/{type}', 'downloadPdf')->name('reports.download');

            // Single reports
            Route::get('client/{client}', 'singleClient')->name('reports.client');
            Route::get('client/{client}/download', 'downloadClientPdf')->name('reports.client.download');

            Route::get('sale/{sale}', 'singleSale')->name('reports.sale');
            Route::get('sale/{sale}/download', 'downloadSalePdf')->name('reports.sale.download');

            Route::get('member/{member}', 'singleMember')->name('reports.member');
            Route::get('member/{member}/download', 'downloadMemberPdf')->name('reports.member.download');
        });
    });
});

Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

// Predefined Categories
Route::post('/settings/categories', [SettingsController::class, 'storeCategory'])->name('settings.categories.store');
Route::put('/settings/categories/{category}', [SettingsController::class, 'updateCategory'])->name('settings.categories.update');
Route::delete('/settings/categories/{category}', [SettingsController::class, 'destroyCategory'])->name('settings.categories.destroy');

// Predefined Units
Route::post('/settings/units', [SettingsController::class, 'storeUnit'])->name('settings.units.store');
Route::put('/settings/units/{unit}', [SettingsController::class, 'updateUnit'])->name('settings.units.update');
Route::delete('/settings/units/{unit}', [SettingsController::class, 'destroyUnit'])->name('settings.units.destroy');
