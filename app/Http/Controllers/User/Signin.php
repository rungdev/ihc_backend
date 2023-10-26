<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Signin extends Controller
{
    function signin () {
        return view('User.Signin');
    }

    function checkSignin(Request $request) {
        // print_r($request->session()->get('user'));
        $signin = DB::table('users')
                    ->where('user_name' , $request->username)
                    ->where('user_pwd' , md5($request->password));
        if($signin->count() > 0){
            $output = $signin->first();
            unset($output->user_pwd);
            $request->session()->put('user' , $output);
            $session = DB::table('group_permission')->where('usergroup_id', $output->user_group)->get();
            $session_arr = [];
            foreach ($session as $key => $value) {
                $session_arr[] = (array)$value;
            }
            $request->session()->put('permission' , $session_arr);
            return ['res_code' => "00", 'res_text' => 'login succcess'];
        }else{
            return ['res_code' => "01", 'res_text' => 'login fail'];
        }
    }
    
}
