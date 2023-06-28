<?php

Route::prefix('campaign')->name('campaign.')->group(function()
{

    Route::get('/list', [App\Http\Controllers\VendorManager\RA\CampaignController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\VendorManager\RA\CampaignController::class, 'show'])->name('show');
    Route::any('/get-campaigns', [App\Http\Controllers\VendorManager\RA\CampaignController::class, 'getCampaigns'])->name('get_campaigns');


    Route::any('/start-campaign/{id}', [App\Http\Controllers\VendorManager\RA\CampaignController::class, 'startCampaign'])->name('start_campaign');
    Route::any('/restart-campaign/{id}', [App\Http\Controllers\VendorManager\RA\CampaignController::class, 'restartCampaign'])->name('restart_campaign');
    Route::any('/submit-campaign/{id}', [App\Http\Controllers\VendorManager\RA\CampaignController::class, 'submitCampaign'])->name('submit_campaign');

});
