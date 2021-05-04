<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthCont;
use App\Http\Controllers\RoomsCont;
use App\Http\Controllers\InfoCont;
use App\Http\Controllers\MessageCont;


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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/showTInfo', [InfoCont::class, 'showTeacherInfo']);
    Route::post('/logoutUser', [AuthCont::class, 'logoutUser']);
    Route::post('/getStudentRooms', [RoomsCont::class, 'getStudentRooms']);
    Route::post('/makeRoom',[RoomsCont::class,'create']);
    //Route::post('/checkOldPass',[RoomsCont::class,'checkPass']);
    //Route::post('/updateInfo',[AuthCont::class,'updateInfo']);
    Route::post('/getRoomInfo/{id}',[RoomsCont::class,'getRoomInfo']);
    Route::post('/getPosts/{id}',[RoomsCont::class,'getRoomPosts']);
    Route::post('/getAssignments/{id}',[RoomsCont::class,'getRoomAssignments']);
    Route::post('/changeProfilePic',[InfoCont::class,'uploadImage']);
    Route::post('/addPost/{id}',[RoomsCont::class,'storePost']);
    Route::post('/showSInfo',[InfoCont::class,'showStudentInfo']);
    Route::post('/registerStudent/{id}',[RoomsCont::class,'addStudentToRoom']);
    Route::post('/searchRooms',[RoomsCont::class,'searchForRooms']);
    Route::post('/showProfilePic',[InfoCont::class,'showProfilePic']);
    Route::post('/getUserMessages',[MessageCont::class,'index']);
    Route::post('/storeMsg',[MessageCont::class,'store']);
    Route::post('/getTRooms',[RoomsCont::class,'getTeacherRooms']);
    Route::put('/sendNewName',[InfoCont::class,'updateName']);
    Route::post('/sendHW/{id}',[RoomsCont::class,'storeHW']);
    Route::post('/getHandedStu/{id}',[RoomsCont::class,'getHandedTasks']);
});

//Route::middleware('auth:sanctum')->post('/dashboard',[UserCont::class,'show']);


Route::put('/SignUpUser', [AuthCont::class, 'store']);

Route::post('/loginUser', [AuthCont::class, 'loginUser'])->name('login');
