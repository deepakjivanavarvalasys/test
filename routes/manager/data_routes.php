<?php

Route::prefix('data')->name('data.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Manager\DataController::class, 'index'])->name('list');

    Route::any('/get-data', [App\Http\Controllers\Manager\DataController::class, 'getData'])->name('get_data');

    Route::any('/import-data', [App\Http\Controllers\Manager\DataController::class, 'import'])->name('import_data');
});
