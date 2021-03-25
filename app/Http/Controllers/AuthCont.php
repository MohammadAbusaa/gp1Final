<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Father;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

class AuthCont extends Controller
{
    public function loginUser(Request $request)
    {
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (auth()->guard('teacher')->attempt(['email' => $creds['email'], 'password' => $creds['password']], $request->remember)) {
            //$request->session()->regenerate(true);
            $token = $request->user('teacher')->createToken('teacher' . $request->user('teacher')->id);
            return response()->json([
                'token' => $token->plainTextToken,
                'user' => $request->user('teacher')->name,
                'link' => 'http://localhost:8080/teacher',
                'time' => \time(),
            ]);
        } else if (auth()->guard('student')->attempt(['email' => strval($creds['email']), 'password' => strval($creds['password'])], $request->remember)) {
            //$request->session()->regenerate(true);

            $token = $request->user('student')->createToken('student' . $request->user('student')->id);

            return response()->json([
                'token' => $token->plainTextToken,
                'user' => $request->user('student')->name,
                'link' => 'http://localhost:8080/dashboard',
                'time' => \time(),
            ]);
        } else if (Auth::guard('father')->attempt(['email' => $creds['email'], 'password' => $creds['password']], $request->remember)) {
            //$request->session()->regenerate(true);

            $token = $request->user()->createToken('father' . $request->user('father')->id);

            return response()->json([
                'token' => $token->plainTextToken,
                'user' => $request->user('student')->name,
                'link' => 'http://localhost:8080/dashboard',
            ]);
        }
        return response('No match for ' . $request->email . '\n' . $creds['email'] . '\t' . $creds['password'])->setStatusCode(422);
    }




    public function store(Request $request)
    {
        $typeOfReq = 3;
        if (isset($request->type)) $typeOfReq = $request->type;
        else $typeOfReq = 3;
        if ($typeOfReq == 0) { // 0 is teacher
            $teacher = new Teacher($request->validate([
                'name' => 'required',
                'email' => 'required|unique:teachers,email|unique:students,email|unique:fathers,email',
                'password' => 'required',
            ]));
            $teacher->name = $request->name;
            $teacher->email = $request->email;
            $teacher->password = bcrypt($request->password);
            $teacher->save();
        } else if ($typeOfReq == 1) { // 1  is student
            $student = new Student($request->validate([
                'name' => 'required',
                'email' => 'required|unique:teachers,email|unique:students,email|unique:fathers,email',
                'password' => 'required',
            ]));
            $student->name = $request->name;
            $student->email = $request->email;
            $student->password = bcrypt($request->password);
            $student->save();
        } else if ($typeOfReq == 2) { // 2 is parent
            $parent = new Father($request->validate([
                'name' => 'required',
                'email' => 'required|unique:teachers,email|unique:students,email|unique:fathers,email',
                'password' => 'required',
            ]));
            $parent->name = $request->name;
            $parent->email = $request->email;
            $parent->password = bcrypt($request->password);
            $parent->save();
        } else return response('type is invalid!')->setStatusCode(422);
        return response('http://localhost:8080/LogIn');
    }



    public function show(Request $request)
    {
        //dd($request->user()->id);
        $data = [
            'username' => $request->user()->name,
            'email' => $request->user()->email,
        ];
        return response()->json($data);
    }

    public function logoutUser(Request $request)
    {
        //$request->session()->invalidate();
        //Auth::logout();
        //dd($request->user());
        $request->user()->currentAccessToken()->delete();
        return response('logged out');
        if ($request->user('teacher')) {
            $request->user('teacher')->currentAccessToken()->delete();
            return response('logged out!');
        } else if ($request->user('student')) {
            $request->user('student')->currentAccessToken()->delete();
            return response('logged out!');
        } else if ($request->user('father')) {
            $request->user('father')->currentAccessToken()->delete();
            return response('logged out!');
        }
        return response('error!');
    }

    public function updateInfo(Request $request)
    {
        $info=$request->validate([
            'name'=>'required',
            'email'=>'required|unique:teachers,email|unique:students,email|unique:fathers,email',
            'password'=>'required'
        ]);
    }
}
