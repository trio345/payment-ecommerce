<?php

namespace App\Http\Controllers;
use App\Customer;
use Illuminate\Support\Facades\Http;

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
            "full_name" => $data->full_name,
            "email" => $data->email,
            "phone_number" => $data->phone_number,
            "password" => $data->password
        ];

        if ( $request->input('email') == $data->email && 
                Hash::check($request->input('password'), $data->password)
            ) {
                    if ( User::create($data->id) ){
                        return response($content = ["status" => "success", "data" => $response], $status = 201);
                    }
            } else {
                return response($content = ["status" => "failed", "message" => "failed email or password"], 300);
            }
            
        
    }
}
