<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostsCont extends Controller
{
    public function fetchPosts()
    {
        return Message::with('user')->get();
    }
    public function sendPosts(Request $request)
    {
        $post=$request->user()->messages()->create([
            'body'=>$request->body,
            'user_id'=>$request->user()->id,
            'room_id'=>$request->room_id,
        ]);
    }
}
