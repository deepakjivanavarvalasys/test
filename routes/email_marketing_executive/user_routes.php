<?php

Route::prefix('user')->name('user.')->group(function()
{

    Route::get('/my-profile', [App\Http\Controllers\EmailMarketingExecutive\UserController::class, 'my_profile'])->name('my_profile');
    Route::any('/update', [App\Http\Controllers\EmailMarketingExecutive\UserController::class, 'update'])->name('update');
    Route::any('/change-password', [App\Http\Controllers\EmailMarketingExecutive\UserController::class, 'change_password'])->name('change_password');

});
