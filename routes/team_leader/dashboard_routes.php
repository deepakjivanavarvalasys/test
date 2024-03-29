<?php

Route::prefix('dashboard')->name('dashboard.')->group(function()
{

    Route::any('/get-counts', [App\Http\Controllers\TeamLeader\DashboardController::class, 'getCounts'])->name('get_counts');
    Route::any('/get-radial-chart-data', [App\Http\Controllers\TeamLeader\DashboardController::class, 'getRadialChartData'])->name('get_radial_chart_data');

});
