<?php

Route::prefix('promotion-campaign')->name('promotion_campaign.')->group(function()
{

    Route::get('/list', [App\Http\Controllers\EmailMarketingExecutive\PromotionCampaignController::class, 'index'])->name('list');

    Route::get('/view-details/{id}', [App\Http\Controllers\EmailMarketingExecutive\PromotionCampaignController::class, 'show'])->name('show');

    Route::any('/get-campaigns', [App\Http\Controllers\EmailMarketingExecutive\PromotionCampaignController::class, 'getCampaigns'])->name('get_campaigns');

    Route::any('/upload-ebb-file/{id}', [App\Http\Controllers\EmailMarketingExecutive\PromotionCampaignController::class, 'uploadEBBFile'])->name('upload_ebb_file');
    Route::any('/submit-campaign/{id}', [App\Http\Controllers\EmailMarketingExecutive\PromotionCampaignController::class, 'submitCampaign'])->name('submit_campaign');

});
