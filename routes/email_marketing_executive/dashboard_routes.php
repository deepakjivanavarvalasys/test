<?php

Route::prefix('dashboard')->name('dashboard.')->group(function()
{

    Route::any('/get-counts', [App\Http\Controllers\EmailMarketingExecutive\DashboardController::class, 'getCountsEME'])->name('get_counts');

});
