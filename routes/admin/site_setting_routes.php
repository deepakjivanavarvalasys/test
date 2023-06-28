<?php

Route::prefix('site-settings')->name('site_settings.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Admin\SiteSettingController::class, 'index'])->name('list');
    Route::any('/get-site-settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'getSiteSettings'])->name('get_site_settings');

    Route::any('/store', [App\Http\Controllers\Admin\SiteSettingController::class, 'store'])->name('store');
    Route::any('/edit/{id}', [App\Http\Controllers\Admin\SiteSettingController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('update');

    Route::any('/destroy/{id}', [App\Http\Controllers\Admin\SiteSettingController::class, 'destroy'])->name('destroy');

});
