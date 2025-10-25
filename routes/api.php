<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Src\Presentation\Controllers\HealthController;
use Src\Presentation\Controllers\InvoiceController;

Route::get('/health', [HealthController::class, 'check']);

Route::prefix('invoices')->group(function () {
    Route::post('/', [InvoiceController::class, 'store']);
    Route::get('/{id}', [InvoiceController::class, 'show']);
    Route::post('/{id}/send', [InvoiceController::class, 'send']);
    Route::post('/{id}/status', [InvoiceController::class, 'updateStatus']);
});
