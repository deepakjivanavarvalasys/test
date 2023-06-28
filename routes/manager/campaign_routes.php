<?php

Route::prefix('campaign')->name('campaign.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Manager\CampaignController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'show'])->name('show');

    Route::any('/get-campaigns', [App\Http\Controllers\Manager\CampaignController::class, 'getCampaigns'])->name('get_campaigns');

    Route::get('/create', [App\Http\Controllers\Manager\CampaignController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\Manager\CampaignController::class, 'store'])->name('store');

    //Incremental
    Route::get('/incremental/create/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'createIncremental'])->name('create_incremental');

    Route::any('/edit/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'update'])->name('update');

    Route::any('/edit-pacing-details/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'editPacingDetails'])->name('edit_pacing_details');
    Route::any('/edit-sub-allocations/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'editSubAllocations'])->name('edit_pacing_details');

    Route::any('/update-sub-allocations/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'updateSubAllocations'])->name('update_sub_allocations');

    Route::any('/destroy/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'destroy'])->name('destroy');

    Route::any('/validate-v-mail-campaign-id', [App\Http\Controllers\Manager\CampaignController::class, 'validateVMailCampaignId'])->name('validate.v_mail_campaign_id');
    Route::any('/validate-campaign-name', [App\Http\Controllers\Manager\CampaignController::class, 'validateCampaignName'])->name('validate.campaign_name');

    Route::any('/attach-specification/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'attachSpecification'])->name('attach_specification');
    Route::any('/remove-specification/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'removeSpecification'])->name('remove_specification');

    Route::any('/attach-campaign-file/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'attachCampaignFile'])->name('attach_campaign_file');

    Route::any('/import', [App\Http\Controllers\Manager\CampaignController::class, 'import'])->name('import');

    Route::any('/get-campaign-history/{id}', [App\Http\Controllers\Manager\CampaignController::class, 'getCampaignHistory'])->name('get_campaign_history');

    //Remote Validation
    Route::any('/check-campaign-name-already-exists/{id?}', [App\Http\Controllers\Manager\CampaignController::class, 'checkCampaignNameAlreadyExists'])->name('check_campaign_name_already_exists');
});
