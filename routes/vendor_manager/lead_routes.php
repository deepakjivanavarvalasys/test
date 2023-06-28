<?php

Route::prefix('lead')->name('lead.')->group(function()
{

    Route::get('/{id}/list', [App\Http\Controllers\VendorManager\LeadController::class, 'index'])->name('list');
    Route::get('edit/{id}', [App\Http\Controllers\VendorManager\LeadController::class, 'edit'])->name('edit');
    Route::post('update/{id}', [App\Http\Controllers\VendorManager\LeadController::class, 'update'])->name('update');
    Route::any('reject/{id}', [App\Http\Controllers\VendorManager\LeadController::class, 'reject'])->name('reject');
    Route::any('/import-leads', [App\Http\Controllers\VendorManager\LeadController::class, 'importLeads'])->name('import_leads');

    Route::any('/get-vendor-leads', [App\Http\Controllers\VendorManager\LeadController::class, 'getVendorLeads'])->name('get_vendor_leads');

    //Validate Suppression
    Route::any('/check-suppression-email/{id}', [App\Http\Controllers\VendorManager\LeadController::class, 'checkSuppressionEmail'])->name('check_suppression_email');
    Route::any('/check-suppression-domain/{id}', [App\Http\Controllers\VendorManager\LeadController::class, 'checkSuppressionDomain'])->name('check_suppression_domain');
    Route::any('/check-suppression-account-name/{id}', [App\Http\Controllers\VendorManager\LeadController::class, 'checkSuppressionAccountName'])->name('check_suppression_account_name');

    //Validate Target List
    Route::any('/check-target-domain/{id}', [App\Http\Controllers\VendorManager\LeadController::class, 'checkTargetDomain'])->name('check_target_domain');

});
