<?php

Route::prefix('campaign-assign')->name('campaign_assign.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'index'])->name('list');

    Route::get('/view-details/{id}', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'show'])->name('show');

    Route::any('/get-assigned-campaigns', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'getAssignedCampaigns'])->name('get_assigned_campaigns');

    Route::get('/create', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'store'])->name('store');

    Route::any('/submit-campaign/{id}', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'submitCampaign'])->name('submit_campaign');

    Route::any('/view-assignment-details/{id}', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'viewAssignmentDetails'])->name('view_assignment_details');

    Route::any('/revoke-campaign/{id}', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'revokeCampaign'])->name('revoke_campaign');
    Route::any('/assign-campaign', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'assignCampaign'])->name('assign_campaign');
    Route::any('/re-assign-campaign/{id}', [App\Http\Controllers\QATeamLeader\CampaignAssignController::class, 'reAssignCampaign'])->name('re_assign_campaign');
});
