<?php

namespace App\Http\Controllers;
use App\Customer;

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

    public function create(Request $request)
    {
        $data = Customer::all()->where('email', $request->input('email'));
        

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $response = [
            "full_name" => $data[3]["full_name"],
            "email" => $data[3]["email"],
            "phone_number" => $data[3]["phone_number"],
            "password" => $data[3]["password"]
        ];
        

        if ($request->input('password') != $data[3]["password"])
            {
                echo $data[3]["full_name"];
                User::create($data[3]["id"]);
                return response($content = ["status" => "success", "data" => $response], $status = 201);
            } else {
                return response($content = ["status" => "failed", "message" => "failed login wrong email or password"], 300);
            }
            
        
    }
}
