<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


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

Route::group(['middleware'=>['auth:sanctum', 'api','cors']],function(){

    Route::post('logout',[UserController::class, 'logout']);
    Route::resource("user", \App\Http\Controllers\UserController::class);
    Route::get('/experts/{name}', [UserController::class, 'getExpertByName']);
    Route::post('/consulting', [\App\Http\Controllers\ConsultController::class, 'getConsultByName']);
    Route::get('/expert/availableTimes/{id}', [\App\Http\Controllers\UserController::class, 'getAvailableTimeOfAnExpert']);
    Route::get('/bookings', [UserController::class, 'getBooking']);
    Route::get('/appointments', [UserController::class, 'getAppointments']);
    Route::get('/user/messages/{id}', [UserController::class, 'getMessages']);
    Route::get('/experts', [UserController::class, 'getExperts']);

    Route::post('/editExpertProfile', [\App\Http\Controllers\UserController::class, 'editExpertProfile']);
	Route::post('/editNormalProfile', [\App\Http\Controllers\UserController::class, 'editNormalUserProfile']);


});

Route::resource('experience', \App\Http\Controllers\ExperienceController::class);
Route::resource("consult", \App\Http\Controllers\ConsultController::class);
Route::resource("availableTime", \App\Http\Controllers\AvailableTimeController::class);
Route::resource("message", \App\Http\Controllers\MessageController::class);
Route::resource("booking", \App\Http\Controllers\BookingController::class);
Route::resource("evaluation", \App\Http\Controllers\EvaluationController::class);
Route::resource("favorite", \App\Http\Controllers\FavoriteController::class);

//Route::get('/experts', [UserController::class, 'getExperts']);

Route::post("login", [UserController::class,'login']);
Route::post("register", [UserController::class,'register'])->middleware(['api', 'cors']);
