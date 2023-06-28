<?php

Route::prefix('team')->name('team.')->group(function()
{

    Route::get('/list', [App\Http\Controllers\TeamLeader\TeamController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\TeamLeader\TeamController::class, 'show'])->name('show');
    Route::any('/get-team-members', [App\Http\Controllers\TeamLeader\TeamController::class, 'getTeamMembers'])->name('get_team_members');

});
