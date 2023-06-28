<?php

Route::prefix('lead')->name('lead.')->group(function()
{

    Route::get('/{id}/list', [App\Http\Controllers\Agent\LeadController::class, 'index'])->name('list');
    Route::get('/{id}/create', [App\Http\Controllers\Agent\LeadController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\Agent\LeadController::class, 'store'])->name('store');

    Route::get('edit/{id}', [App\Http\Controllers\Agent\LeadController::class, 'edit'])->name('edit');
    Route::post('update/{id}', [App\Http\Controllers\Agent\LeadController::class, 'update'])->name('update');

    Route::post('destroy/{id}', [App\Http\Controllers\Agent\LeadController::class, 'destroy'])->name('destroy');

    Route::any('/get-leads', [App\Http\Controllers\Agent\LeadController::class, 'getLeads'])->name('get_leads');


    Route::any('/start-campaign/{id}', [App\Http\Controllers\Agent\LeadController::class, 'startCampaign'])->name('start_campaign');

    //Validate Suppression
    Route::any('/check-suppression-email/{id}', [App\Http\Controllers\Agent\LeadController::class, 'checkSuppressionEmail'])->name('check_suppression_email');
    Route::any('/check-suppression-domain/{id}', [App\Http\Controllers\Agent\LeadController::class, 'checkSuppressionDomain'])->name('check_suppression_domain');
    Route::any('/check-suppression-account-name/{id}', [App\Http\Controllers\Agent\LeadController::class, 'checkSuppressionAccountName'])->name('check_suppression_account_name');

    //Validate Target List
    Route::any('/check-target-domain/{id}', [App\Http\Controllers\Agent\LeadController::class, 'checkTargetDomain'])->name('check_target_domain');

    Route::any('{ca_agent_id}/import', [App\Http\Controllers\Agent\LeadController::class, 'formImportLeads'])->name('form_import_leads');
    Route::any('/import-leads', [App\Http\Controllers\Agent\LeadController::class, 'importLeads'])->name('import_leads');

});
