<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogInCont extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginUser(Request $request)
    {
        $creds=$request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);
        if(auth()->guard('teacher')->attempt(['email'=>$creds['email'],'password'=>$creds['password']],$request->remember)){
            $request->session()->regenerate(true);
            $user=$request->user();
            dd($user);

             return response()->json([
                'user'=>$user,
                'link'=>'http://localhost:8080/dashboard',
            ]);
        }
        else if(auth()->guard('student')->attempt(['email'=>strval($creds['email']),'password'=>strval($creds['password'])],$request->remember)){
            $request->session()->regenerate(true);

            $user=auth()->user();
            dd($user);

             return response([
                 'user'=>$user,
                 'link'=>'http://localhost:8080/dashboard',
             ]);
        }
        else if(Auth::guard('father')->attempt(['email'=>$creds['email'],'password'=>$creds['password']],$request->remember)){
            $request->session()->regenerate(true);

            $user=$request->user();


             return response([
                 'user'=>$user,
                 'link'=>'http://localhost:8080/dashboard',
             ]);
        }
        return response('No match for '.$request->email .'\n'.$creds['email'].'\t'.$creds['password'])->setStatusCode(422);
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
