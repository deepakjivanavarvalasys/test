<?php

Route::prefix('geo')->name('geo.')->group(function()
{
    Route::prefix('region')->name('region.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\Admin\GeoController::class, 'regionIndex'])->name('list');
        Route::any('/get-regions', [App\Http\Controllers\Admin\GeoController::class, 'getRegions'])->name('get_regions');
        Route::any('/store', [App\Http\Controllers\Admin\GeoController::class, 'regionStore'])->name('store');
        Route::any('/edit/{id}', [App\Http\Controllers\Admin\GeoController::class, 'regionEdit'])->name('edit');
        Route::any('/update/{id}', [App\Http\Controllers\Admin\GeoController::class, 'regionUpdate'])->name('update');
        Route::any('/destroy/{id}', [App\Http\Controllers\Admin\GeoController::class, 'regionDestroy'])->name('destroy');
    });

    Route::prefix('country')->name('country.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\Admin\GeoController::class, 'countryIndex'])->name('list');
        Route::any('/get-countries', [App\Http\Controllers\Admin\GeoController::class, 'getCountries'])->name('get-countries');
        Route::any('/store', [App\Http\Controllers\Admin\GeoController::class, 'countryStore'])->name('store');
        Route::any('/edit/{id}', [App\Http\Controllers\Admin\GeoController::class, 'countryEdit'])->name('edit');
        Route::any('/update/{id}', [App\Http\Controllers\Admin\GeoController::class, 'countryUpdate'])->name('update');
        Route::any('/destroy/{id}', [App\Http\Controllers\Admin\GeoController::class, 'countryDestroy'])->name('destroy');
    });
    
});
