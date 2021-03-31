<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
class InfoCont extends Controller
{
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

    public function updateTeacherInfo(Request $request)
    {
        $info=$request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required',
            'oldPassword'=>'required|password',
            'major'=>'required',
        ]);
        if($info['email']!=$request->user()->email)return response('you');
        $request->user()->update([
            'name'=>$info['name'],
            'email'=>$info['email'],
            'password'=>Hash::make($info['password']),
        ]);
        $request->user()->teacher->update(['major'=>$info['major']]);
        return response('updated');

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
        $image=$request->image;
        if($image->isValid()){
            $file=new File();
            $file->name=$image->name();
            $file->path=$image->path();
            $file->user_id=$request->user()->id;
            $file->size=$image->size();
        }
    }
}
