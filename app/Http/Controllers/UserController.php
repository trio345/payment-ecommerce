<?php

namespace App\Http\Controllers;
use App\Customer;
use App\User;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function login(Request $request)
    {
        $data = Customer::where('email', $request->input('email'))->update();
        // print_r($data);
        // dd();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $req = $request->all();

        if ( sizeof($data) > 0){
            if ( $req["email"] == $data[0]["email"] && 
            $req["password"] == $data[0]["password"])
            {
                $user = new User();
                $user->user_id = $data[0]["id"];
                $user->save();
                return response($content = ["messages" => "success login", "status" => true, "data" => $data[0]], $status = 201);
            } 
        } else {
            return response($content = ["status" => "failed", "message" => "failed login wrong email or password"], 300);
        }
    }
}
