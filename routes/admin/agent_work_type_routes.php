<?php

Route::prefix('campaign-settings')->name('campaign_settings.')->group(function()
{
    Route::prefix('agent-work-type')->name('agent_work_type.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\Admin\AgentWorkTypeController::class, 'index'])->name('list');
        Route::any('/get-agent-work-types', [App\Http\Controllers\Admin\AgentWorkTypeController::class, 'getAgentWorkTypes'])->name('get_agent_work_types');
        Route::any('/store', [App\Http\Controllers\Admin\AgentWorkTypeController::class, 'store'])->name('store');
        Route::any('/edit/{id}', [App\Http\Controllers\Admin\AgentWorkTypeController::class, 'edit'])->name('edit');
        Route::any('/update/{id}', [App\Http\Controllers\Admin\AgentWorkTypeController::class, 'update'])->name('update');
        Route::any('/destroy/{id}', [App\Http\Controllers\Admin\AgentWorkTypeController::class, 'destroy'])->name('destroy');
    });
});
