<?php

Route::prefix('holiday')->name('holiday.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Admin\HolidayController::class, 'index'])->name('list');
    Route::any('/get-holidays', [App\Http\Controllers\Admin\HolidayController::class, 'getHolidays'])->name('get_holidays');

    Route::any('/get-holiday-list', [App\Http\Controllers\Manager\HolidayController::class, 'getHolidayList'])->name('get_holiday_list');
});
