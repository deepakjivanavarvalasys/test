<?php

Route::prefix('campaign-settings')->name('campaign_settings.')->group(function()
{
    Route::prefix('campaign-status')->name('campaign_status.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\Admin\CampaignStatusController::class, 'index'])->name('list');
        Route::any('/get-campaign-statuses', [App\Http\Controllers\Admin\CampaignStatusController::class, 'getCampaignStatuses'])->name('get_campaign_statuses');
        Route::any('/store', [App\Http\Controllers\Admin\CampaignStatusController::class, 'store'])->name('store');
        Route::any('/edit/{id}', [App\Http\Controllers\Admin\CampaignStatusController::class, 'edit'])->name('edit');
        Route::any('/update/{id}', [App\Http\Controllers\Admin\CampaignStatusController::class, 'update'])->name('update');
        Route::any('/destroy/{id}', [App\Http\Controllers\Admin\CampaignStatusController::class, 'destroy'])->name('destroy');
    });
});
