<?php

Route::prefix('campaign-settings')->name('campaign_settings.')->group(function()
{
    Route::prefix('campaign-filter')->name('campaign_filter.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\Admin\CampaignFilterController::class, 'index'])->name('list');
        Route::any('/get-campaign-filters', [App\Http\Controllers\Admin\CampaignFilterController::class, 'getCampaignFilters'])->name('get_campaign_filters');
        Route::any('/store', [App\Http\Controllers\Admin\CampaignFilterController::class, 'store'])->name('store');
        Route::any('/edit/{id}', [App\Http\Controllers\Admin\CampaignFilterController::class, 'edit'])->name('edit');
        Route::any('/update/{id}', [App\Http\Controllers\Admin\CampaignFilterController::class, 'update'])->name('update');
        Route::any('/destroy/{id}', [App\Http\Controllers\Admin\CampaignFilterController::class, 'destroy'])->name('destroy');
    });
});
