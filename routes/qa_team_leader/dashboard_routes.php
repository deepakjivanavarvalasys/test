<?php

Route::prefix('dashboard')->name('dashboard.')->group(function()
{

    Route::any('/get-counts', [App\Http\Controllers\QATeamLeader\DashboardController::class, 'getCountsQATL'])->name('get_counts');

});
