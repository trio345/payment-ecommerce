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
use App\Http\Controllers\Controller;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Storage;



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
        $this->response = new Controller();

    }

    public function index(){
        $datas = Customer::all();
        if ( $datas ){
            return $this->response->baseResponse("Success retrive data", $datas, true, 201);
        }
    }

    public function update(Request $request, $id)
    {
        if ( Customer::find($id) != [] ){
            $data = Customer::find($id);
        } else {
            return $this->response->baseResponse("User not found!", $datas, false, 400);
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
            return $this->response->baseResponse("Success retrive data", $data, true, 201);
        } else {
            $data = new \stdClass;
            return $this->response->baseResponse("Failed save data", $data, false, 400);
        }
    }

    public function changePassword(Request $request)
    {
        $email = $request->input('email');
        $customer = Customer::where('email', $email)->first();

        $this->validate($request, [
            'password' => 'required|min:8'
        ]);

        if ($customer != []){
            $customer->password = Hash::make($request->input('password'));
            $customer->save();
            return $this->response->baseResponse("Password has been changed", $customer, true, 201);
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
            $data = new \stdClass;
            return $this->response->baseResponse("Email isn't registed", $data, false, 400);
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
            "password" => Hash::make($request->input('password')),
            "token" => Str::random(42)
        ];
        $customer = Customer::where('email', $response["email"])->first();
        // var_dump($customer);
        // dd();
        if ($customer == null){
                if ( Customer::create($response)){      
                    $data = Customer::where('email', $request->input('email'))->first();    
                    Mail::to($response["email"])->send(new RegisterMail($data));

                    return response($content = ["status" => "success", "data" => $data], $status = 201);
                } else {
                    $data = new \stdClass;
                    return $this->response->baseResponse("Failed insert data!", $data, false, 400);
                }
        } else {
            return response($content = ["messages" => "Email already used", "status" => false], $status = 400);
        }

            
    }

    public function verifyRegister(Request $request, $token){
       $customer = Customer::where('token', $token)->first();
       
       if ( $customer["token"] != null){
           $customer->status = 1;
           $customer->save();        
       }

    //    $url = Storage::url('verified-mail.png');
       return view('emails.success_verify');
       
    }

    public function find($id)
    {
        $data = Customer::find($id);

        if ( $data ){
            return $this->response->baseResponse("Success retrive data", $data, true, 201);
        } else {
            $data = new \stdClass;
            return $this->response->baseResponse("Failed get data", $data, false, 400);
        }
    }

    public function delete($id)
    {
        $data = Customer::find($id);
        if ($data->delete()){
            return $this->response->baseResponse("Success retrive data", $data, true, 201);
        } else {
            return $this->response->baseResponse("Failed delete data!", $data, false, 400);
        }
    }
}