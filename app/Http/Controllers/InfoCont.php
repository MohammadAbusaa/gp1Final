<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;

$uploadPath='\\uploads';

class InfoCont extends Controller
{
    public function getUploadPath()
    {
        return '/uploads';
    }
    public function showTeacherInfo(Request $request)
    {
        //dd($request->user()->teacher->id);
        $data = [
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'major'=>$request->user()->teacher->major,
        ];
        return response()->json($data);
    }

    public function updateName(Request $request)
    {
        $request->validate([
            'name'=>'required',
        ]);
        $request->user()->update(['name'=>$request->name]);
        return response('updated name');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
        ]);
        $email=User::where('email',$request->email)->get();
        if(!is_null($email))return response('taken');
        $request->user()->update(['email'=>$request->email,]);
        return response('updated email');
    }

    public function updatePass(Request $request)
    {
        $request->validate([
            'newPass'=>'required',
            'oldPass'=>'required|password',
            'confPass'=>'required',
        ]);
        if($request->newPass!=$request->confPass)return response('no match');
        $request->user()->update([
            'password'=>Hash::make($request->newPass),
        ]);
        return response('updated password');

    }
    
    public function updateStudentInfo()
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required',
        ]);
    }

    public function uploadImage(Request $request)
    {
        clearstatcache();
        $image=$request->file('image');
        //dd($image);
        if($image->isValid()){
            $res=$request->user()->files->where('name','personalPic'.$request->user()->id);
            if($res->isNotEmpty())File::find($res[0]->id)->delete();
            $path=(Storage::putFile($this->getUploadPath(),$image));

            $file=new File();
            $file->name='personalPic'.$request->user()->id;
            $file->ext=$image->getClientOriginalExtension();
            $file->path=base_path().'/storage/app/'.$path;
            $file->user_id=$request->user()->id;
            $file->size=$image->getSize();
            $file->save();
            
            

            return response('saved!');
        }
        return response('failed!',422);
    }

    public function showStudentInfo(Request $request)
    {
        $user=$request->user();
        $info=[
            'name'=>$user->name,
            'email'=>$user->email,
            'class'=>$user->student->class,
            //'image'=>$request->user()->files->where('name','personalPic'),
        ];
        return response()->json($info);
    }

    public function showProfilePic(Request $request)
    {
        //dd($request->user()->files->where('name','personalPic'.$request->user()->id));
        $path=$request->user()->files->where('name','personalPic'.$request->user()->id)->first();
        if(!is_null($path)){
            $conts=\file_get_contents($path['path']);
            $enc=base64_encode($conts);
            $info=\pathinfo($path['path'],PATHINFO_EXTENSION);
            return response()->json(['img'=>$enc,'type'=>$info]);
        }
        else return response('nothing');
    }

    public function getUserNotifications(Request $request)
    {
        $notif=$request->user()->notifications->map->only(['body','created_at']);
        return response()->json($notif);
    }
}
