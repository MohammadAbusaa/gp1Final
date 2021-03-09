<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignUpCont;
use App\Http\Controllers\LogInCont;
use App\Http\Controllers\UserCont;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/dashboard',[UserCont::class,'show'])->middleware('auth:sanctum');
//Route::middleware('auth:sanctum')->post('/dashboard',[UserCont::class,'show']);
Route::post('/logoutUser',[UserCont::class,'logoutUser'])->middleware('auth:sanctum');

Route::put('/SignUpUser',[SignUpCont::class,'store']);

Route::post('/loginUser',[LogInCont::class,'loginUser'])->name('login');
