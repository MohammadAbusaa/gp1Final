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

    public function create(Request $request){
        $room=new Room($request->validate([
            'name'=>'required',
            'class'=>'required|in:one,two,three,four,five,six,seven',
            'subject'=>'required',
            'type'=>'required|in:public,private'
        ]));
        $room->name=$request->name;
        $room->class=$request->class;
        $room->type=$request->type;
        if(!($request->subject==''))$room->subject=$request->subject;
        $room->teacher_id=auth()->user('teacher')->id;

        $room->save();

        return response('saved');

    }

    public function checkPass(Request $request)
    {
        if(auth()->user()->getAuthPassword()==$request->password)return response()->json(['flag'=>true]);
        else return response()->json(['flag'=>false]);
    }

    public function getRoomInfo(Request $request,$id)
    {
        $room=Room::find($id);
        $info=[
            'name'=>$room->name,
            'teacher'=>$room->teacher,
            'subject'=>$room->subject,
        ];
    }
}
