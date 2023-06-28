<?php

Route::prefix('campaign')->name('campaign.')->group(function()
{

    Route::get('/list', [App\Http\Controllers\QualityAnalyst\CampaignController::class, 'index'])->name('list');

    Route::get('/view-details/{id}', [App\Http\Controllers\QualityAnalyst\CampaignController::class, 'show'])->name('show');

    Route::any('/get-campaigns', [App\Http\Controllers\QualityAnalyst\CampaignController::class, 'getCampaigns'])->name('get_campaigns');


    Route::any('/download-file/{id}', [App\Http\Controllers\QualityAnalyst\CampaignController::class, 'downloadFile'])->name('download_file');
    Route::any('/download-npf/{id}', [App\Http\Controllers\QualityAnalyst\CampaignController::class, 'downloadNPF'])->name('download_npf');

    Route::any('/upload-npf-file/{id}', [App\Http\Controllers\QualityAnalyst\CampaignController::class, 'uploadNPF'])->name('upload_npf');
    Route::any('/submit-campaign/{id}', [App\Http\Controllers\QualityAnalyst\CampaignController::class, 'submitCampaign'])->name('submit_campaign');

});
