<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Events\PostSent;
use App\Events\StudentJoined;

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
        $room=Room::find($id);
        //dd($room->name);
        $posts=$room->posts;
        return response()->json(['posts'=>$posts]);
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
        //dd(\broadcast(new PostSent($request->user(),$post))->toOthers());
        \broadcast(new PostSent($request->user(),$post))->toOthers();

        return response('added');
    }

    public function addStudentToRoom(Request $request,$id)
    {
        //dd($request->user());
        $request->user()->student->rooms()->attach($id);

        $students=Room::find($id)->students;

        \broadcast(new StudentJoined($students))->toOthers();

        return response()->json($students);
    }

    public function searchForRooms(Request $request)
    {
        // split on 1+ whitespace & ignore empty (eg. trailing space)
        $searchValues = preg_split('/\s+/', $request->name, -1, PREG_SPLIT_NO_EMPTY); 

        $rooms = Room::where(function ($q) use ($searchValues) {
        foreach ($searchValues as $value) {
            $q->orWhere('name', 'like', "%{$value}%");
        }
        })->orWhere('class',$request->class)->orWhere('subject',$request->subject)->get();


        $user_rooms=$request->user()->student->rooms->map->only(['name','id']);
        $curr_rooms=$rooms->map->only(['name','id']);
        //dd($curr_rooms);

        

        return response()->json($this->compare($user_rooms,$curr_rooms));
    }

    private function compare($coll1,$coll2)
    {
        $flag=false;
        $arr=[];
        foreach($coll2 as $item){
            $flag=false;
            foreach($coll1 as $item2){
                if($item['name']==$item2['name']){
                    $flag=true;
                    break;
                }
            }
            if(!$flag){
                array_push($arr,$item);
            }
        }
        return $arr;
    }

    public function getRoomAssignments(Request $request,$id)
    {
        $room=Room::find($id);
    }

    public function getTeacherRooms(Request $request)
    {
        $rooms=$request->user()->teacher->rooms->map->only(['id','name','type']);
        return response()->json($rooms);
    }
}
