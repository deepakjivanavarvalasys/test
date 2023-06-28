<?php

Route::prefix('data')->name('data.')->group(function()
{
    Route::get('/list/{id}', [App\Http\Controllers\Agent\DataController::class, 'index'])->name('list');

    Route::any('/get-agent-data', [App\Http\Controllers\Agent\DataController::class, 'getAgentData'])->name('get_agent_data');

    Route::any('/edit/{id}', [App\Http\Controllers\Agent\DataController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\Agent\DataController::class, 'update'])->name('update');

    //Route::any('/take-lead-data/{ca_agent_id}/{data_id}', [App\Http\Controllers\Agent\LeadController::class, 'create'])->name('take_lead_data');
    Route::any('/take-lead-data', [App\Http\Controllers\Agent\DataController::class, 'takeLead'])->name('take_lead_data');

});
