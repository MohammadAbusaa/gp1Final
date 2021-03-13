<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomsCont extends Controller
{
    public function getStudentRooms(Request $request)
    {
        return response()->json(['rooms'=>auth()->user('student')->rooms]);
    }
    public function showStudentRoom(Request $request, $id)
    {
        
    }
}
