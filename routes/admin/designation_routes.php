<?php

Route::prefix('user-settings')->name('user_settings.')->group(function()
{
    Route::prefix('designation')->name('designation.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\Admin\DesignationController::class, 'index'])->name('list');
        Route::any('/get-designations', [App\Http\Controllers\Admin\DesignationController::class, 'getDesignations'])->name('get_designations');
        Route::any('/store', [App\Http\Controllers\Admin\DesignationController::class, 'store'])->name('store');
        Route::any('/edit/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'edit'])->name('edit');
        Route::any('/update/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'update'])->name('update');
        Route::any('/destroy/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'destroy'])->name('destroy');
    });
});
