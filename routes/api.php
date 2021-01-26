<?php

use App\Http\Controllers\Event\EventsController;
use App\Http\Controllers\Event\EventUsersController;
use App\Http\Controllers\Team\TeamsController;
use App\Http\Controllers\Team\TeamUsersController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\PasswordRecoveryController;
use App\Http\Controllers\User\PasswordRestorationController;
use App\Http\Controllers\User\UsersController;
use App\Http\Controllers\User\VerificationController;
use Illuminate\Support\Facades\Route;

Route::post('/login/employee', [AuthController::class, 'loginEmployee'])->name('login.employee');
Route::post('/login/guest', [AuthController::class, 'loginGuest'])->name('login.guest');

Route::post('/email/resend ', [VerificationController::class, 'resend'])->name('verification.resend');
Route::post('/email/verify', [VerificationController::class, 'verify'])->name('verification.verify');

Route::post('/password/recover', [PasswordRecoveryController::class, 'recover'])->name('password.request');
Route::post('/password/reset', [PasswordRestorationController::class, 'reset'])->name('password.reset');

Route::get('/events/code/{code}', [EventsController::class, 'show'])->name('events.show');

// Protected routes that require the user to be logged in
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');

    Route::get('/user', [UsersController::class, 'me'])->name('user.me');
    Route::post('/user', [UsersController::class, 'store'])->name('user.store');
    Route::patch('/user/{id}', [UsersController::class, 'update'])->name('user.update');

    Route::get('/teams', [TeamsController::class, 'index'])->name('teams.index');
    Route::post('/teams', [TeamsController::class, 'store'])->name('teams.store');

    Route::get('/teams/{id}', [TeamsController::class, 'show'])->name('teams.show');
    Route::put('/teams/{id}', [TeamsController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{id}', [TeamsController::class, 'destroy'])->name('teams.destroy');
    Route::patch('/teams/{id}/users', [TeamUsersController::class, 'update'])->name('team.users.update');

    Route::get('/events', [EventsController::class, 'index'])->name('events.index');
    Route::post('/events', [EventsController::class, 'store'])->name('events.store');

    Route::put('/events/{id}', [EventsController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventsController::class, 'destroy'])->name('events.destroy');
    Route::patch('/events/{id}/users', [EventUsersController::class, 'update'])->name('events.users.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
