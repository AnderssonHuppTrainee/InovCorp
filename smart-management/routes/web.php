<?php

use App\Http\Controllers\Core\ContactController;
use App\Http\Controllers\Core\DigitalArchiveController;
use App\Http\Controllers\Core\EntityController;
use App\Http\Controllers\Core\OrderController;
use App\Http\Controllers\Core\ProposalController;
use App\Http\Controllers\Core\SupplierOrderController;
use App\Http\Controllers\Core\WorkOrderController;
use App\Http\Controllers\Financial\BankAccountController;
use App\Http\Controllers\Financial\CustomerInvoiceController;
use App\Http\Controllers\Financial\SupplierInvoiceController;
use App\Http\Controllers\System\CalendarEventController;
use App\Http\Controllers\System\RoleController;
use App\Http\Controllers\System\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
Route::middleware(['auth', 'verified'])->group(function () {

    // Entities (Clientes/Fornecedores)
    Route::get('/entities', [EntityController::class, 'index'])
        ->name('entities.index');
    Route::get('/entities/create', [EntityController::class, 'create'])
        ->name('entities.create');
    Route::post('/entities', [EntityController::class, 'store'])
        ->name('entities.store');
    Route::get('/entities/{entity}/show', [EntityController::class, 'show'])
        ->name('entities.show');
    Route::get('/entities/{entity}/edit', [EntityController::class, 'edit'])
        ->name('entities.edit');
    Route::put('/entities/{entity}', [EntityController::class, 'update'])
        ->name('entities.update');
    Route::delete('/entities/{entity}', [EntityController::class, 'destroy'])
        ->name('entities.destroy');
    Route::post('/entities/vies-check', [EntityController::class, 'viesCheck'])
        ->name('entities.vies-check');

    // Contacts
    Route::resource('contacts', ContactController::class);

    // Proposals
    Route::resource('proposals', ProposalController::class);
    Route::post('/proposals/{proposal}/convert-to-order', [ProposalController::class, 'convertToOrder'])
        ->name('proposals.convert-to-order');
    Route::get('/proposals/{proposal}/pdf', [ProposalController::class, 'generatePdf'])
        ->name('proposals.pdf');

    // Orders (Encomendas)
    Route::resource('orders', OrderController::class);
    Route::post('/orders/{order}/convert-to-supplier-orders', [OrderController::class, 'convertToSupplierOrders'])
        ->name('orders.convert-to-supplier-orders');
    Route::get('/orders/{order}/pdf', [OrderController::class, 'generatePdf'])
        ->name('orders.pdf');

    // Supplier Orders (Encomendas Fornecedores)
    Route::get('/supplier-orders', [SupplierOrderController::class, 'index'])
        ->name('supplier-orders.index');
    Route::get('/supplier-orders/{supplierOrder}', [SupplierOrderController::class, 'show'])
        ->name('supplier-orders.show');
    Route::delete('/supplier-orders/{supplierOrder}', [SupplierOrderController::class, 'destroy'])
        ->name('supplier-orders.destroy');

    // Calendar
    Route::get('/calendar', [CalendarEventController::class, 'index'])
        ->name('calendar.index');
    Route::get('/calendar/events', [CalendarEventController::class, 'getEvents'])
        ->name('calendar.events');
    Route::post('/calendar', [CalendarEventController::class, 'store'])
        ->name('calendar.store');
    Route::get('/calendar/{calendarEvent}', [CalendarEventController::class, 'show'])
        ->name('calendar.show');
    Route::put('/calendar/{calendarEvent}', [CalendarEventController::class, 'update'])
        ->name('calendar.update');
    Route::delete('/calendar/{calendarEvent}', [CalendarEventController::class, 'destroy'])
        ->name('calendar.destroy');

    // Work Orders
    Route::resource('work-orders', WorkOrderController::class);

    // Supplier Invoices
    Route::resource('supplier-invoices', SupplierInvoiceController::class);
    Route::get('/supplier-invoices/{supplierInvoice}/download-document', [SupplierInvoiceController::class, 'downloadDocument'])
        ->name('supplier-invoices.download-document');
    Route::get('/supplier-invoices/{supplierInvoice}/download-payment-proof', [SupplierInvoiceController::class, 'downloadPaymentProof'])
        ->name('supplier-invoices.download-payment-proof');
    Route::post('/supplier-invoices/{supplierInvoice}/send-payment-proof', [SupplierInvoiceController::class, 'sendPaymentProof'])
        ->name('supplier-invoices.send-payment-proof');

    // Bank Accounts
    Route::resource('bank-accounts', BankAccountController::class);

    // Customer Invoices (Conta Corrente Clientes)
    Route::resource('customer-invoices', CustomerInvoiceController::class);
    Route::post('/customer-invoices/{customerInvoice}/register-payment', [CustomerInvoiceController::class, 'registerPayment'])
        ->name('customer-invoices.register-payment');

    // Digital Archive
    Route::resource('digital-archive', DigitalArchiveController::class);
    Route::get('/digital-archive/{digitalArchive}/download', [DigitalArchiveController::class, 'download'])
        ->name('digital-archive.download');
    Route::get('/digital-archive/{digitalArchive}/view', [DigitalArchiveController::class, 'view'])
        ->name('digital-archive.view');

    // Access Management - Users
    Route::resource('users', UserController::class);

    // Access Management - Roles
    Route::resource('roles', RoleController::class);
    Route::post('/roles/sync-permissions', [RoleController::class, 'syncPermissions'])
        ->name('roles.sync-permissions');

    // Settings - Articles
    Route::resource('articles', \App\Http\Controllers\Settings\ArticleController::class);

    // Settings - Tax Rates
    Route::resource('tax-rates', \App\Http\Controllers\Financial\TaxRateController::class);

    // Settings - Company
    Route::get('/settings/company', [\App\Http\Controllers\Settings\CompanySettingsController::class, 'index'])
        ->name('settings.company.index');
    Route::put('/settings/company', [\App\Http\Controllers\Settings\CompanySettingsController::class, 'update'])
        ->name('settings.company.update');

    // Activity Logs
    Route::get('/logs', function () {
        $logs = \Spatie\Activitylog\Models\Activity::with('causer')->latest()->paginate(50);
        return Inertia::render('settings/logs/Index', ['logs' => $logs]);
    })->name('logs.index');
});