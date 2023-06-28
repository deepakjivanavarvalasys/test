<?php

Route::prefix('dashboard')->name('dashboard.')->group(function()
{

    Route::any('/get-counts', [App\Http\Controllers\QualityAnalyst\DashboardController::class, 'getCountsQA'])->name('get_counts');

});
