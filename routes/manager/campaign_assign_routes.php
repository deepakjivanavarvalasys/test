<?php

Route::prefix('campaign-assign')->name('campaign_assign.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Manager\CampaignAssignController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'show'])->name('show');

    Route::any('/get-assigned-campaigns', [App\Http\Controllers\Manager\CampaignAssignController::class, 'getAssignedCampaigns'])->name('get_assigned_campaigns');

    Route::get('/create', [App\Http\Controllers\Manager\CampaignAssignController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\Manager\CampaignAssignController::class, 'store'])->name('store');

    Route::any('/edit-pacing-details/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'editPacingDetails'])->name('edit_pacing_details');
    Route::any('/update/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'update'])->name('update');
    Route::any('/destroy/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'destroy'])->name('destroy');

    Route::any('/view-assignment-details/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'viewAssignmentDetails'])->name('view_assignment_details');
    Route::any('/view-assigned-agents/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'viewAssignedAgents'])->name('view_assigned_agents');
    Route::any('/view-assigned-vendors/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'viewAssignmentVendors'])->name('view_assigned_vendors');

    Route::any('/get-campaign-details/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'show'])->name('get_campaign_details');
    Route::any('/update-delivery-details', [App\Http\Controllers\Manager\CampaignAssignController::class, 'updateDeliveryDetails'])->name('update_delivery_details');

    Route::any('/revoke-campaign/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'revokeCampaign'])->name('revoke_campaign');
    Route::any('/assign-campaign', [App\Http\Controllers\Manager\CampaignAssignController::class, 'assignCampaign'])->name('assign_campaign');
    Route::any('/re-assign-campaign/{id}', [App\Http\Controllers\Manager\CampaignAssignController::class, 'reAssignCampaign'])->name('re_assign_campaign');
});
