<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Father;

class SignUpCont extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $typeOfReq=3;
        if(isset($request->type))$typeOfReq=$request->type;
        else $typeOfReq=3;
        if($typeOfReq==0){ // 0 is teacher
            $teacher=new Teacher($request->validate([
                'name'=>'required',
                'email'=>'required|unique:teachers,email',
                'password'=>'required',
            ]));
            $teacher->name=$request->name;
            $teacher->email=$request->email;
            $teacher->password=bcrypt($request->password);
            $teacher->save();
        }else if($typeOfReq==1){ // 1  is student
            $student=new Student($request->validate([
                'name'=>'required',
                'email'=>'required|unique:students,email',
                'password'=>'required',
            ]));
            $student->name=$request->name;
            $student->email=$request->email;
            $student->password=bcrypt($request->password);
            $student->save();
        }
        else if($typeOfReq==2){ // 2 is parent
            $parent=new Father($request->validate([
                'name'=>'required',
                'email'=>'required|unique:fathers,email',
                'password'=>'required',
            ]));
            $parent->name=$request->name;
            $parent->email=$request->email;
            $parent->password=bcrypt($request->password);
            $parent->save();
        }
        else return response('type is invalid!')->setStatusCode(422);
        return response('http://localhost:8080/LogIn');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
