<?php

Route::prefix('cron_trigger')->name('cron_trigger.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Admin\CronTriggerController::class, 'index'])->name('list');

    /*
     * Cron Jobs
     */
    //Daily Report Logs
    Route::get('/daily-report-log', [App\Http\Controllers\Admin\CronTriggerController::class, 'DailyReportLog'])->name('daily_report_log');

});
