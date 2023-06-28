<?php

Route::prefix('dashboard')->name('dashboard.')->group(function()
{
    Route::any('/get-data', [App\Http\Controllers\Agent\DashboardController::class, 'getData'])->name('get_data');

    Route::any('/get-guage-chart-data', [App\Http\Controllers\Agent\DashboardController::class, 'getGuageChartData'])->name('get_guage_chart_data');

    Route::any('/get-counts-by-work-type-bar-chart-data', [App\Http\Controllers\Agent\DashboardController::class, 'getCountsByWorkTypeBarChartData'])->name('get_counts_by_work_type_bar_chart_data');

    Route::any('/get-leads-generated-counts-bar-chart-data', [App\Http\Controllers\Agent\DashboardController::class, 'getLeadsGeneratedCountsBarChartData'])->name('get_leads_generated_counts_bar_chart_data');

    Route::any('/get-top-productivity-data', [App\Http\Controllers\Agent\DashboardController::class, 'getTopProductivityData'])->name('get_top_productivity_data');
});
