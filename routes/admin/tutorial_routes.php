<?php

Route::prefix('tutorial')->name('tutorial.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Admin\TutorialController::class, 'index'])->name('list');
    Route::any('/get-tutorials', [App\Http\Controllers\Admin\TutorialController::class, 'getTutorials'])->name('get_tutorials');
    Route::any('/store', [App\Http\Controllers\Admin\TutorialController::class, 'store'])->name('store');
    Route::any('/edit/{id}', [App\Http\Controllers\Admin\TutorialController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\Admin\TutorialController::class, 'update'])->name('update');
    Route::any('/destroy/{id}', [App\Http\Controllers\Admin\TutorialController::class, 'destroy'])->name('destroy');
});
