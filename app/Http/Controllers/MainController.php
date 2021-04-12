<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{
    function login(){
        return view('auth.login');
    }
    function register(){
        return view('auth.register');
    }
    function save(Request $request){
        

        //Validate request
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:admins',
            'password'=>'required|min:5|max:12'
        ]);

        //insert data into database
        $admin = new Admin;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $save = $admin->save();

        if($save){
            return back()->with('success' ,'User succesfully added');

        }else{
            return back()->with('fail', 'something went wrong try again');
        }

    }
    function check(Request $request){
        //requested input
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:5|max:12'
       ]);
       $userInfo = Admin::where('email','=', $request->email)->first();

       if(!$userInfo){
        return back()->with('unnsuccesful','We do not recognize your email address');
        }else{
        //check password
            if(Hash::check($request->password, $userInfo->password)){
                $request->session()->put('LoggedUser', $userInfo->id);
                return redirect('admin/dashboard');
            }else{
                return back()->with('Incorrect credentials');
            }

        }
    }
    function dashboard(){
        $data = ['LoggedUserInfo'=>Admin::where('id','=', session('LoggedUser'))->first()];
        return view('admin.dashboard', $data);
    }
    function logout(){
        if(session()->has('LoggedUser')){
            session()->pull('LoggedUser');
            return redirect('/auth/login');
        }
    }
    
}
