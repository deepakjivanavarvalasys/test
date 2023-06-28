<?php

Route::prefix('campaign-issue')->name('campaign_issue.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Manager\CampaignIssueController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\Manager\CampaignIssueController::class, 'show'])->name('show');
    Route::any('/get-issues', [App\Http\Controllers\Manager\CampaignIssueController::class, 'getIssues'])->name('get_issues');
    // Route::any('/get-issues', [App\Http\Controllers\Manager\CampaignIssueController::class, 'getIssues'])->name('get_issues');

    Route::post('/store', [App\Http\Controllers\Manager\CampaignIssueController::class, 'store'])->name('store');
    Route::any('/edit/{id}', [App\Http\Controllers\Manager\CampaignIssueController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\Manager\CampaignIssueController::class, 'update'])->name('update');
});
