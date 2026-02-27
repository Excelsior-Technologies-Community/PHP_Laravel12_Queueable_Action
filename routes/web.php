<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

// Web routes
Route::get('/', function () {
    return view('orders');
});

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/create', [OrderController::class, 'createTestOrder'])->name('orders.create');
    Route::post('/{order}/process-sync', [OrderController::class, 'processSync'])->name('orders.process-sync');
    Route::post('/{order}/process-queued', [OrderController::class, 'processQueued'])->name('orders.process-queued');
    Route::post('/process-bulk', [OrderController::class, 'processBulkQueued'])->name('orders.process-bulk');
});