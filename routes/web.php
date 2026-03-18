<?php

use App\Http\Controllers\ContractExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/contracts/{clientContract}/export/pdf', [ContractExportController::class, 'pdf'])
        ->name('contracts.export.pdf');
    Route::get('/contracts/{clientContract}/export/docx', [ContractExportController::class, 'docx'])
        ->name('contracts.export.docx');
});
