<?php

Route::prefix('campaign-assign')->name('campaign_assign.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\VendorManager\CampaignAssignController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\VendorManager\CampaignAssignController::class, 'show'])->name('show');
    Route::any('/get-assigned-campaigns', [App\Http\Controllers\VendorManager\CampaignAssignController::class, 'getAssignedCampaigns'])->name('get_assigned_campaigns');
    Route::post('/store', [App\Http\Controllers\VendorManager\CampaignAssignController::class, 'store'])->name('store');

    Route::any('/submit-campaign/{id}', [App\Http\Controllers\VendorManager\CampaignAssignController::class, 'submitCampaign'])->name('submit_campaign');
});
