<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Father;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthCont extends Controller
{
    public function loginUser(Request $request)
    {
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        //dd(Auth::guard('teacher')->attempt(['email' => $creds['email'], 'password' => $creds['password']], $request->remember));
        if($user=DB::table('users')->where('email',$creds['email'])->first()){
            //dd($user);
            if($t=DB::table('teachers')->where('user_id',$user->id)->first()){
                if(Hash::check($creds['password'], $user->password)){
                    Auth::loginUsingId($user->id);
                    $token = $request->user()->createToken('teacher' . $user->id);
                    return response()->json([
                        'token' => $token->plainTextToken,
                        'user' => $user->name,
                        'link' => 'http://localhost:8080/teacher',
                        'time' => \time(),
                    ]);
                }
                else return response('invalid creds')->setStatusCode(422);
            }
            else if($s=DB::table('students')->where('user_id',$user->id)->first()){
                    if(Hash::check($creds['password'], $user->password)){
                        Auth::loginUsingId($user->id);
                        $token = $request->user()->createToken('student' . $user->id);
                        return response()->json([
                            'token' => $token->plainTextToken,
                            'user' => $user->name,
                            'link' => 'http://localhost:8080/student',
                            'time' => \time(),
                        ]);
                    }
                    else return response('invalid creds')->setStatusCode(422);
            }
            else if($f=DB::table('fathers')->where('user_id',$user->id)->first()){
                    if(Hash::check($creds['password'], $user->password)){
                        Auth::loginUsingId($user->id);
                        $token = $request->user()->createToken('father' . $user->id);
                        return response()->json([
                            'token' => $token->plainTextToken,
                            'user' => $user->name,
                            'link' => 'http://localhost:8080/parents',
                            'time' => \time(),
                        ]);
                    }
                else return response('invalid creds')->setStatusCode(422);
            }
            else return response()->json(['fail'=>'invalid creds']);
        }
        else return response()->json(['fail'=>'invalid creds']);

        /* previuos database structure
        if (auth()->guard('teacher')->attempt(['email' => $creds['email'], 'password' => $creds['password']], $request->remember)) {
            //$request->session()->regenerate(true);
            {
            $token = $request->user('teacher')->createToken('teacher' . $request->user('teacher')->id);
            return response()->json([
                'token' => $token->plainTextToken,
                'user' => $request->user('teacher')->name,
                'link' => 'http://localhost:8080/teacher',
                'time' => \time(),
            ]);
        }
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
        */
    }




    public function store(Request $request)
    {
        $typeOfReq = 3;
        if (isset($request->type)) $typeOfReq = $request->type;
        else $typeOfReq = 3;
        if ($typeOfReq == 0) { // 0 is teacher
            $teacher = new User($request->validate([
                'name' => 'required',
                'email' => 'required|unique:users,email|email',
                'password' => 'required',
            ]));
            $teacher->name = $request->name;
            $teacher->email = $request->email;
            $teacher->password = bcrypt($request->password);
            $teacher->save();
            ($teacher->teacher()->create([
                'id'=>$teacher->id,
            ]));
        } else if ($typeOfReq == 1) { // 1  is student
            $student = new User($request->validate([
                'name' => 'required',
                'email' => 'required|unique:users,email|email',
                'password' => 'required',
            ]));
            $student->name = $request->name;
            $student->email = $request->email;
            $student->password = bcrypt($request->password);
            $student->save();
            $student->student()->create([
                'id'=>$student->id,
            ]);
        } else if ($typeOfReq == 2) { // 2 is parent
            $parent = new User($request->validate([
                'name' => 'required',
                'email' => 'required|unique:users,email|email',
                'password' => 'required',
            ]));
            $parent->name = $request->name;
            $parent->email = $request->email;
            $parent->password = bcrypt($request->password);
            $parent->save();
            $parent->father()->create(['id'=>$parent->id]);
        } else return response('type is invalid!')->setStatusCode(422);
        return response('http://localhost:8080/LogIn');
    }

    public function logoutUser(Request $request)
    {
        //$request->session()->invalidate();
        //Auth::logout();
        //dd($request->user());
        $request->user()->currentAccessToken()->delete();
        return response('logged out');
        /*
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
        return response('error!');*/
    }



}
