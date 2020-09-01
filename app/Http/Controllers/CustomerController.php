<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Customer;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;


class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(){
        $datas = Customer::all();
        if ( $datas ){
            return response($content = ["message" => "success retrive data", "status" => true,"data" => $datas], $status = 201);
        }
    }

    public function update(Request $request, $id)
    {
        if ( Customer::find($id) != [] ){
            $data = Customer::find($id);
        } else {
            return response()->json($content = ["status" => "302", "messages" => "user not found"]);
        }

        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required|min:8'
        ]);

        $data->full_name = $request->input('full_name');
        $data->email = $request->input('email');
        $data->phone_number = $request->input('phone_number');
    
        if ( $data->save() ){
            return response($content = ["status" => "success", "data" => $data], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }

    public function changePassword(Request $request, $id){
        if ( Customer::find($id) != [] ){
            $data = Customer::find($id);
        } else {
            return response()->json($content = ["status" => "302", "messages" => "user not found"]);
        }

        $this->validate($request, [
            'new_password' => 'required|min:8'
        ]);

        $req = $request->all();
        if ($req != []){
            $data->password = $req["new_password"];
            $data->save();
            return response()->json($content = ["status" => 400, "messages" => "password has been changed!"], 400);
        }

    }


    public function create(Request $request)
    {
        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email|unique:customers',
            'phone_number' => 'required',
            'password' => 'required|min:8'
        ]);

        $response = [
            "full_name" => $request->input('full_name'),
            "email" => $request->input('email'),
            "phone_number" => $request->input('phone_number'),
            "password" => $request->input('password')
        ];

            
        if ( Customer::create($response) ){
            // Mail::to($response["email"])->send(new OrderMail());
            return response($content = ["status" => "success", "data" => $response], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }




    public function find($id)
    {
        $data = Customer::find($id);

        if ( $data ){
            return response($content = ["status" => "success", "data" => $data], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"customer not found!"]);
        }
    }

    public function delete($id)
    {
        $data = Customer::find($id);
        if ($data->delete()){
            return response($content = ["status" => "success", "messages" => "berhasil dihapus"], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"gagal dihapus!"]);
        }
    }

}