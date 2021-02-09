<?php

use App\Http\Controllers\Answer\AnswersController;
use App\Http\Controllers\Event\EventHostsController;
use App\Http\Controllers\Event\EventsController;
use App\Http\Controllers\Event\EventSessionsController;
use App\Http\Controllers\Question\QuestionAnswersController;
use App\Http\Controllers\Question\QuestionsController;
use App\Http\Controllers\Session\SessionResponsesController;
use App\Http\Controllers\Session\SessionsController;
use App\Http\Controllers\Team\TeamsController;
use App\Http\Controllers\Team\TeamUsersController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\PasswordRecoveryController;
use App\Http\Controllers\User\PasswordRestorationController;
use App\Http\Controllers\User\UsersController;
use App\Http\Controllers\User\VerificationController;
use Illuminate\Support\Facades\Route;

Route::post('/login/guest', [AuthController::class, 'loginGuest'])->name('login.guest');
Route::post('/login/employee', [AuthController::class, 'loginEmployee'])->name('login.employee');

Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
Route::post('/email/verify', [VerificationController::class, 'verify'])->name('verification.verify');

Route::post('/password/reset', [PasswordRestorationController::class, 'reset'])->name('password.reset');
Route::post('/password/recover', [PasswordRecoveryController::class, 'recover'])->name('password.request');

Route::get('/events/code/{code}', [QuestionsController::class, 'show'])->name('events.show');

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
    Route::get('/events/{id}/hosts', [EventHostsController::class, 'index'])->name('events.hosts.index');
    Route::patch('/events/{id}/hosts', [EventHostsController::class, 'update'])->name('events.hosts.update');
    Route::get('/events/{id}/sessions', [EventSessionsController::class, 'index'])->name('events.sessions.index');
    Route::post('/events/{id}/sessions', [EventSessionsController::class, 'store'])->name('events.sessions.store');
    Route::get('/events/{id}/questions', [EventSessionsController::class, 'index'])->name('events.questions.index');

    Route::post('/questions', [QuestionsController::class, 'store'])->name('questions.store');

    Route::get('/questions/{id}', [QuestionsController::class, 'show'])->name('questions.show');
    Route::put('/questions/{id}', [QuestionsController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [QuestionsController::class, 'destroy'])->name('questions.destroy');
    Route::patch('/questions/{id}/answers', [QuestionAnswersController::class, 'update'])->name('questions.answers.update');

    Route::post('/answers', [AnswersController::class, 'store'])->name('answers.store');

    Route::get('/answers/{id}', [AnswersController::class, 'show'])->name('answers.show');
    Route::put('/answers/{id}', [AnswersController::class, 'update'])->name('answers.update');
    Route::delete('/answers/{id}', [AnswersController::class, 'destroy'])->name('answers.destroy');

    Route::get('/sessions/{id}', [SessionsController::class, 'show'])->name('sessions.show');
    Route::get('/sessions/{id}/responses', [SessionResponsesController::class, 'index'])->name('sessions.responses.index');
    Route::patch('/sessions/{id}/responses', [SessionResponsesController::class, 'update'])->name('sessions.responses.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
