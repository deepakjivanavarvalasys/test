<?php

Route::prefix('dashboard')->name('dashboard.')->group(function()
{

    Route::any('/get-counts', [App\Http\Controllers\VendorManager\DashboardController::class, 'getCountsVM'])->name('get_counts');

});
