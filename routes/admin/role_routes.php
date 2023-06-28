<?php

Route::prefix('role')->name('role.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('list');
    Route::any('/get-roles', [App\Http\Controllers\Admin\RoleController::class, 'getRoles'])->name('get_roles');

    Route::any('/store', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
    Route::any('/edit/{id}', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');

    Route::any('/destroy/{id}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
    
});
