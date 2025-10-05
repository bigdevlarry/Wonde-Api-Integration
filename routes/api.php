<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SchoolControllers\TeacherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refresh');
    });

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('api');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.reset')
        ->middleware('auth:api');

    Route::middleware(['auth:api', 'verified'])->group(function () {
        Route::controller(TeacherController::class)->group(function () {
            Route::get('/teacher/{id}/students', 'allStudents');
        });
    });
});
