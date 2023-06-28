<?php

Route::prefix('holiday')->name('holiday.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Admin\HolidayController::class, 'index'])->name('list');
    Route::any('/get-holidays', [App\Http\Controllers\Admin\HolidayController::class, 'getHolidays'])->name('get_holidays');

    Route::any('/store', [App\Http\Controllers\Admin\HolidayController::class, 'store'])->name('store');
    Route::any('/edit/{id}', [App\Http\Controllers\Admin\HolidayController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\Admin\HolidayController::class, 'update'])->name('update');

    Route::any('/destroy/{id}', [App\Http\Controllers\Admin\HolidayController::class, 'destroy'])->name('destroy');
    
});
