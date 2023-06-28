<?php

Route::prefix('campaign-assign')->name('campaign_assign.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'show'])->name('show');

    Route::any('/get-assigned-campaigns', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'getAssignedCampaigns'])->name('get_assigned_campaigns');

    Route::get('/create', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'store'])->name('store');

    Route::any('/view-assignment-details/{id}', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'viewAssignmentDetails'])->name('view_assignment_details');

    Route::any('/get-data', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'getData'])->name('get_data');
    Route::any('/assign-data', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'assignData'])->name('assign_data');
    Route::any('/upload-npf-file', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'uploadNPF'])->name('upload_npf');

    Route::any('/send-for-quality-check/{caratl_id}', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'sendForQualityCheck'])->name('send_for_quality_check');

    Route::any('/revoke-campaign/{id}', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'revokeCampaign'])->name('revoke_campaign');
    Route::any('/assign-campaign', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'assignCampaign'])->name('assign_campaign');
    Route::any('/re-assign-campaign/{id}', [App\Http\Controllers\TeamLeader\CampaignAssignController::class, 'reAssignCampaign'])->name('re_assign_campaign');
});
