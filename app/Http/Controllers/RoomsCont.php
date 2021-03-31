<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Events\PostSent;

class RoomsCont extends Controller
{
    public function getStudentRooms(Request $request)
    {
        $rooms=$request->user()->student->rooms;
        $arr=[];
        foreach ($rooms as $item) {
            array_push($arr,[
                'name'=>$item['name'],
                'room_id'=>$item['pivot']->room_id,
            ]);
        }
        return response()->json(['rooms'=>$arr]);
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
        $room->subject=$request->subject;
        $room->teacher_id=$request->user()->id;
        if($request->type=='public')$room->type=1;
        else $room->type=0;

        $room->save();

        return response('saved');

    }

    public function getRoomInfo(Request $request,$id)
    {
        $room=Room::find($id);
        $info=[
            'roomInf'=>[
                'name'=>$room->name,
                'teacher'=>$room->teacher->user->name,
                'subject'=>$room->subject,
            ],

        ];
        return response()->json($info);
    }

    public function getRoomPosts(Request $request,$id)
    {
        $room=Room::find($id)->first();
        $posts=$room->posts;
        return response()->json($posts);
    }

    public function storePost(Request $request,$id)
    {
        $post=new Post($request->validate([
            'post'=>'required',
        ]));
        $post->body=$request->post;
        $post->user_id=$request->user()->id;
        $post->room_id=$id;
        $post->save();

        \broadcast(new PostSent($request->user(),$post))->toOthers();

        return response('added');
    }
}
