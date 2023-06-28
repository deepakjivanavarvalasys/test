<?php

Route::prefix('lead')->name('lead.')->group(function()
{

    Route::get('/{id}/list', [App\Http\Controllers\TeamLeader\LeadController::class, 'index'])->name('list');
    Route::get('edit/{id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'edit'])->name('edit');
    Route::post('update/{id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'update'])->name('update');
    Route::any('reject/{id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'reject'])->name('reject');

    Route::any('/get-agent-leads', [App\Http\Controllers\TeamLeader\LeadController::class, 'getAgentLeads'])->name('get_agent_leads');

    Route::any('/start-campaign/{id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'startCampaign'])->name('start_campaign');

    //Validate Suppression
    Route::any('/check-suppression-email/{id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'checkSuppressionEmail'])->name('check_suppression_email');
    Route::any('/check-suppression-domain/{id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'checkSuppressionDomain'])->name('check_suppression_domain');
    Route::any('/check-suppression-account-name/{id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'checkSuppressionAccountName'])->name('check_suppression_account_name');

    //Validate Target List
    Route::any('/check-target-domain/{id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'checkTargetDomain'])->name('check_target_domain');

    //Export
    Route::any('/export/{caratl_id}', [App\Http\Controllers\TeamLeader\LeadController::class, 'export'])->name('export');
});
