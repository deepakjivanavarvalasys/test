<?php

Route::prefix('lead')->name('lead.')->group(function()
{

    Route::get('/{id}/list', [App\Http\Controllers\QualityAnalyst\LeadController::class, 'index'])->name('list');
    Route::any('reject/{id}', [App\Http\Controllers\QualityAnalyst\LeadController::class, 'reject'])->name('reject');
    Route::any('approve/{id}', [App\Http\Controllers\QualityAnalyst\LeadController::class, 'approve'])->name('approve');
    Route::any('/get-campaign-leads', [App\Http\Controllers\QualityAnalyst\LeadController::class, 'getCampaignLeads'])->name('get_campaign_leads');

    //Export
    Route::any('/export/{caqa_id}', [App\Http\Controllers\QualityAnalyst\LeadController::class, 'export'])->name('export');
});
