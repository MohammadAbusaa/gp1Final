<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
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
        if($email)return response('taken');
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
        $path=$request->user()->files->where('name','personalPic'.$request->user()->id);
        if($path->isNotEmpty())
            return response()->file($path[0]->path);
        else return response('nothing');
    }
}
