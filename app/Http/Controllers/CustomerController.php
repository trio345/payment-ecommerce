<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Customer;
use App\Mail\RegisterMail;
use App\Mail\ResetPasswordMail;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;


class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

     
    public function __construct()
    {
        Cache::flush();
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

    public function changePassword(Request $request, $id)
    {
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

    public function resetPassword(Request $request){
        $this->validate($request, [
            "email" => 'required|email'
        ]);
        
        $customer = Customer::where('email', $request->input('email'))->first();
        
        if ( $customer !== null ){
            $customer->token = Str::random(6);
            $customer->save();

            Mail::to($customer["email"])->send(new ResetPasswordMail($customer));
            return response()->json(["status" => true, "token" => $customer->token], 201);
        } else {
            return response()->json(["message" => "Email isn't register"], 301);
        }
        

    }

    // public function retriveToken(Request $request, $token){
    //     $this->validate($request, [
    //         "email" => 'required|email',
    //         "password" => 'required|min:8'
    //     ]);

    //     $customer = Customer::where('token', $token)->first();
    //     if ( $customer["token"] != null){
    //         if ($customer["email"] == $request->input('email')){
    //             $customer->password = Hash::make($request->input("password"));
    //             $customer->token = null;
    //             $customer->save();
    //             return response()->json(["status" => true, "message" => "success reset password"], 200);
    //         } else {    
    //             return response()->json(["status" => false, "message" => "failed reset password!"], 301);
    //         }
    //     }  
    // }


    public function create(Request $request)
    {
        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required|min:8'
        ]);

        $response = [
            "full_name" => $request->input('full_name'),
            "email" => $request->input('email'),
            "phone_number" => $request->input('phone_number'),
            "password" => Hash::make($request->input('password'))
        ];
        $customer = Customer::where('email', $response["email"])->first();
    
        if ($customer === null){
                if ( Customer::create($response) ){
                    $customer = Customer::where('email', $response["email"])->first();
                    $customer->token = Str::random(42);
                    $customer->save();
                    // $data = Http::post('https://verticalcraneandlift.com/sendemail.php', $req);' 
                    Mail::to($customer["email"])->send(new RegisterMail($customer));

                    return response($content = ["status" => "success", "data" => $customer], $status = 201);
                } else {
                    return response($content = ["status" => "failed", "data" => null], $status = 300);
                }
        } else {
            return response($content = ["messages" => "Email already used", "status" => false], $status = 303);
        }

            
    }

    public function verifyRegister(Request $request, $token){
       $customer = Customer::where('token', $token)->first();
       if ( $customer["token"] != null){
           $customer->status = 1;
           $customer->token = null;
           $customer->save();
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