<?php

Route::prefix('data')->name('data.')->group(function()
{
    Route::get('/list/{id}', [App\Http\Controllers\EmailMarketingExecutive\DataController::class, 'index'])->name('list');

    Route::any('/get-eme-data', [App\Http\Controllers\EmailMarketingExecutive\DataController::class, 'getEMEData'])->name('get_eme_data');

    Route::any('/edit/{id}', [App\Http\Controllers\EmailMarketingExecutive\DataController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\EmailMarketingExecutive\DataController::class, 'update'])->name('update');

    //Route::any('/take-lead-data/{ca_agent_id}/{data_id}', [App\Http\Controllers\Agent\LeadController::class, 'create'])->name('take_lead_data');
    Route::any('/take-lead-data', [App\Http\Controllers\EmailMarketingExecutive\DataController::class, 'takeLead'])->name('take_lead_data');

});
