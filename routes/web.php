<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignUpCont;
use App\Http\Controllers\LogInCont;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::put('/SignUpUser',[SignUpCont::class,'store']);

//Route::post('/LogInUser',[LogInCont::class,'show']);
Route::post('/loginUser',[LogInCont::class,'loginUser'])->name('login');