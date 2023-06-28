<?php

Route::prefix('user')->name('user.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('list');
    Route::any('/get-users', [App\Http\Controllers\Admin\UserController::class, 'getUsers'])->name('get_users');

    Route::any('/validate-employee-code', [App\Http\Controllers\Admin\UserController::class, 'validateEmployeeCode'])->name('validate_employee_code');
    Route::any('/validate-email', [App\Http\Controllers\Admin\UserController::class, 'validateEmail'])->name('validate_email');

    Route::any('/store', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
    Route::any('/edit/{id}', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
    Route::any('/update/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');

    Route::any('/destroy/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');


    Route::get('/my-profile', [App\Http\Controllers\Admin\UserController::class, 'my_profile'])->name('my_profile');
    Route::any('/change-password', [App\Http\Controllers\Admin\UserController::class, 'change_password'])->name('change_password');

});
