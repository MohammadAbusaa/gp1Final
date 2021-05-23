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
    Route::post('/sendNewName',[InfoCont::class,'updateName']);
    Route::post('/sendNewEmail',[InfoCont::class,'updateEmail']);
    Route::post('/sendNewPass',[InfoCont::class,'updatePass']);
    Route::post('/sendHW/{id}',[RoomsCont::class,'storeHW']);
    Route::post('/getHandedStu/{id}',[RoomsCont::class,'getHandedTasks']);
    Route::post('/deletePost/{id}',[RoomsCont::class,'deletePost']);
    Route::post('/getRoomUsers/{id}',[RoomsCont::Class,'getRoomUsers']);
    Route::post('/sendCirc/{id}',[RoomsCont::class,'sendCirc']);
    Route::post('/getCircs/{id}',[RoomsCont::class,'getCirc']);
    Route::post('/downloadFile/{id}',[RoomsCont::class,'downloadFile']);
    Route::post('/chRoomName/{id}',[RoomsCont::class,'updateRoomName']);
    Route::post('/chRoomType/{id}', [RoomsCont::class,'updateRoomType']);
    Route::post('/chRoomPass/{id}', [RoomsCont::class,'updateRoomPassword']);
    Route::post('/deleteRoom/{id}',[RoomsCont::class,'deleteRoom']);
    Route::post('/deleteStuFromRoom/{room_id}/{student_id}',[RoomsCont::class,'deleteStudentFromRoom']);
    Route::post('/sendExam/{id}',[RoomsCont::class,'sendExam']);
    Route::post('/getRoomExams/{id}',[RoomsCont::class,'getRoomExams']);
    Route::post('/getExamQuestions/{id}',[RoomsCont::class,'getExamQuestions']);
    Route::put('/sendQ1/{id}',[RoomsCont::class,'sendQ1']);
    Route::put('/sendQ23/{id}',[RoomsCont::class,'sendQ23']);
    Route::post('/getQuestion/{id}',[RoomsCont::class,'getQuestion']);
    Route::put('/updateQ1/{id}',[RoomsCont::class,'updateQ1']);
    Route::put('/updateQ23/{id}',[RoomsCont::class,'updateQ23']);
    Route::post('/initExam/{id}',[RoomsCont::class,'initExam']);
    Route::post('/sendExamAnswer/{id}',[RoomsCont::class,'nextQuestion']);
    Route::post('/getUserNotifications',[InfoCont::class,'getUserNotifications']);
    Route::post('/getRandomRooms',[RoomsCont::class,'getRandomRooms']);
    Route::post('/sendTask/{id}',[RoomsCont::class,'handStudentTask']);
    Route::post('/updateStudentsMarks/{id}',[RoomsCont::class,'updateTaskVals']);
    Route::post('/getStudentsExamMarks/{id}',[RoomsCont::class,'getExamMarks']);
});

//Route::middleware('auth:sanctum')->post('/dashboard',[UserCont::class,'show']);


Route::put('/SignUpUser', [AuthCont::class, 'store']);

Route::post('/loginUser', [AuthCont::class, 'loginUser'])->name('login');
