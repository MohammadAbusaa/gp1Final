<?php

namespace App\Http\Controllers;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomsCont extends Controller
{
    public function index(){

        return response()->json(Room::all()->student);
    }
    public function showStudentRooms(Request $request,$id){

    }
}
