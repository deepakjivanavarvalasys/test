<?php

Route::prefix('campaign-management')->name('campaign_management.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'show'])->name('show');

    Route::any('/get-campaigns', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'getCampaigns'])->name('get_campaigns');

    Route::get('/create', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'store'])->name('store');

    //Incremental
    Route::get('/incremental/create/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'createIncremental'])->name('create_incremental');

    Route::any('/edit/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'update'])->name('update');

    Route::any('/edit-pacing-details/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'editPacingDetails'])->name('edit_pacing_details');
    Route::any('/edit-sub-allocations/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'editSubAllocations'])->name('edit_pacing_details');

    Route::any('/update-sub-allocations/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'updateSubAllocations'])->name('update_sub_allocations');

    Route::any('/destroy/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'destroy'])->name('destroy');

    Route::any('/validate-v-mail-campaign-id', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'validateVMailCampaignId'])->name('validate.v_mail_campaign_id');
    Route::any('/validate-campaign-name', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'validateCampaignName'])->name('validate.campaign_name');

    Route::any('/attach-specification/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'attachSpecification'])->name('attach_specification');
    Route::any('/remove-specification/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'removeSpecification'])->name('remove_specification');

    Route::any('/attach-campaign-file/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'attachCampaignFile'])->name('attach_campaign_file');

    Route::any('/import', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'import'])->name('import');

    Route::any('/get-campaign-history/{id}', [App\Http\Controllers\EmailMarketingExecutive\CampaignManagementController::class, 'getCampaignHistory'])->name('get_campaign_history');

});
