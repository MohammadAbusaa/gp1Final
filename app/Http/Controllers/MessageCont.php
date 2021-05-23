<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Carbon\Carbon;

class MessageCont extends Controller
{
    public function index(Request $request)
    {
        $msgs=Message::where('user_id',$request->user()->id)->orWhere('receiver_id',$request->user()->id)->orderBy('created_at','asc')->get();
        $groupedMsgs=$msgs->groupBy('receiver_id');
        $usersArr=[];
        $lastMsgs=[];
        foreach ($groupedMsgs[$request->user()->id] as $value) {
            if($groupedMsgs->has($value->user_id))
                $groupedMsgs[$value->user_id]->push($value);
        }
        $groupedMsgs->forget($request->user()->id);
        $sortedArr=[];
        foreach($groupedMsgs as $key=>$item)
            array_push($sortedArr,[
                $key=>$item->sortBy('created_at')
                        ]);
        foreach($groupedMsgs as $k=>$v){
            array_push($usersArr,[
                User::find($k),
                $v[0]['body'],
            ]);
        }
        return response()->json([
            'msgs'=>$groupedMsgs,
            'users'=>$usersArr,
            'user'=>$request->user(),
            'sorted'=>$sortedArr,
        ]);
        //dd($groupedMsgs);
    }
    public function store(Request $request)
    {
        $request->validate([
        'body'=>'required',
        'rec'=>'required|exists:users,id'
        ]);
        $msg=$request->user()->messages()->create([
            'body'=>$request->body,
            'receiver_id'=>$request->rec,
            'seen'=>false,
            'timestamps'=>Carbon::now(),
        ]);
        \broadcast(new MessageSent($request->user(),$msg,$request->rec))->toOthers();

        return response('msg sent');

    }
}
