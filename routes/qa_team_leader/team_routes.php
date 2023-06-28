<?php

Route::prefix('team')->name('team.')->group(function()
{

    Route::get('/list', [App\Http\Controllers\QATeamLeader\TeamController::class, 'index'])->name('list');
    Route::get('/view-details/{id}', [App\Http\Controllers\QATeamLeader\TeamController::class, 'show'])->name('show');
    Route::any('/get-team-members', [App\Http\Controllers\QATeamLeader\TeamController::class, 'getTeamMembers'])->name('get_team_members');

});
