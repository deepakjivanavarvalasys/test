<?php

Route::prefix('user-settings')->name('user_settings.')->group(function()
{
    Route::prefix('department')->name('department.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('list');
        Route::any('/get-departments', [App\Http\Controllers\Admin\DepartmentController::class, 'getDepartments'])->name('get_departments');
        Route::any('/store', [App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('store');
        Route::any('/edit/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'edit'])->name('edit');
        Route::any('/update/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('update');
        Route::any('/destroy/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'destroy'])->name('destroy');
    });
});
