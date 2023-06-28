<?php

Route::prefix('campaign-settings')->name('campaign_settings.')->group(function()
{
    Route::prefix('campaign-type')->name('campaign_type.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\Admin\CampaignTypeController::class, 'index'])->name('list');
        Route::any('/get-campaign-types', [App\Http\Controllers\Admin\CampaignTypeController::class, 'getCampaignTypes'])->name('get_campaign_types');
        Route::any('/store', [App\Http\Controllers\Admin\CampaignTypeController::class, 'store'])->name('store');
        Route::any('/edit/{id}', [App\Http\Controllers\Admin\CampaignTypeController::class, 'edit'])->name('edit');
        Route::any('/update/{id}', [App\Http\Controllers\Admin\CampaignTypeController::class, 'update'])->name('update');
        Route::any('/destroy/{id}', [App\Http\Controllers\Admin\CampaignTypeController::class, 'destroy'])->name('destroy');
    });
});
