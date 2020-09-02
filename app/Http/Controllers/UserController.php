<?php

namespace App\Http\Controllers;
use App\Customer;
use App\User;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
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
        $data = Customer::where('email', $request->input('email'))->get();

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        $req = $request->all();

        if ( sizeof($data) > 0){
            if ($data[0]["status"] == 1){
                if ( $req["email"] == $data[0]["email"])
                {
                    if ( Hash::check($req["password"], $data[0]["password"])){
                        $user = new User();
                        $user->user_id = $data[0]["id"];
                        $user->save();
                        return response($content = ["messages" => "Success login", "status" => true, "data" => $data[0]], $status = 201);
                    } else {
                        return response($content = ["messages" => "Wrong password", "status" => false], $status = 300);
                    }
                } else {
                    return response($content = ["messages" => "Wrong email", "status" => false], $status = 300);
                }
            } else {
                return response($content = ["messages" => "Please verify your account !", "status" => false], 302);
            }

                
        } else {
            return response($content = ["status" => "failed", "message" => "failed login wrong email or password"], 322);
        }
    }
}
