<?php

Route::prefix('vendor')->name('vendor.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\VendorManager\VendorController::class, 'index'])->name('list');

    Route::any('/store', [App\Http\Controllers\VendorManager\VendorController::class, 'store'])->name('store');
    Route::any('/get-vendors', [App\Http\Controllers\VendorManager\VendorController::class, 'getVendors'])->name('get_vendors');

    Route::any('/edit/{id}', [App\Http\Controllers\VendorManager\VendorController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\VendorManager\VendorController::class, 'update'])->name('update');

    Route::any('/destroy/{id}', [App\Http\Controllers\VendorManager\VendorController::class, 'destroy'])->name('destroy');

});
