<?php

namespace App\Http\Controllers;

//use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function prosesLogin(Request $request)
    {

        //   $pass = 'nigger';
        //   echo Hash::make($pass);
          if(Auth::guard('name')->attempt(['nrp' => $request->nrp, 'password' => $request->password])){
             return redirect('/dashboard');
          }else{
             return redirect('/')->with((['warning' => 'NRP / Password Salah']));
          }

    }

    public function prosesLogout()
    {
        if(Auth::guard('name')->check()){
            Auth::guard('name')->logout();
            return redirect('/');
        }
    }
}
