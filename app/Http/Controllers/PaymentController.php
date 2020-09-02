<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Order;
use App\OrderItem;
use App\Payment;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->auth = base64_encode('SB-Mid-server-RhcTfWbUDIJG780Eu7fYZP25:');
        $this->url = 'https://api.sandbox.midtrans.com/v2/charge';
        $this->transaction_req = [];
        $this->insertData = [];
        $this->data = new \stdClass;
        $this->response = new Controller();
    }

    public function index(){
        $datas = Payment::all();
        if ( $datas ){
            return $this->response->baseResponse("Success retrive data", $datas, true, 201);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Payment::find($id);

        $this->validate($request, [
            'order_id' => 'required',
            'transaction_id' => 'required',
            'payment_type' => 'required',
            'gross_amount' => 'required',
            'transaction_time' => 'required',
            'transaction_status' => 'required'
            ]);

        $data->order_id = $request->input('order_id');
        $data->transaction_id = $request->input('transaction_id');
        $data->payment_type = $request->input('payment_type');
        $data->gross_amount = $request->input('gross_amount');
        $data->transaction_time = $request->input('transaction_time');
        $data->transaction_status = $request->input('transaction_status');

            
        if ( $data->save() ){
            return $this->response->baseResponse("Success retrive data", $data, true, 201);
        } else {
            return $this->response->baseResponse("Failed retrive data", $this->data, true, 400);
        }
    }

    public function create(Request $request)
    {
        $req = $request->all();

        if ( $req["payment_type"] == "cash"){
            $this->transaction_req = [
                "payment_type" => $req["payment_type"],
                "transaction_details" => [
                    "order_id" => $req["order_id"],
                    "gross_amount" => $req["gross_amount"],
                    "cash" => $req["paid"]
                ]];
                
            $this->insertData = [
                "order_id" => $this->transaction_req["transaction_details"]["order_id"],
                "transaction_id" => uniqid(),
                "payment_type" => $this->transaction_req["payment_type"],
                "gross_amount" => $this->transaction_req["transaction_details"]["gross_amount"],
                "transaction_time" => date('Y-m-d'),
                "transaction_status" => "finish",
                "cash" => $this->transaction_req["transaction_details"]["cash"],
                "change" => $this->transaction_req["transaction_details"]["cash"] - intval($this->transaction_req["transaction_details"]["gross_amount"])
            ];

            $this->validate($request, [
                'payment_type' => 'required',
                'gross_amount' => 'required',
                'order_id' => 'required'            
            ]);
            $data = $this->insertData;

        } else {
            $this->validate($request, [
                'payment_type' => 'required',
                'gross_amount' => 'required',
                'order_id' => 'required',
                'bank' => 'required',
                'va_number' => 'required'   
            ]);

            $this->transaction_req = [
                "payment_type" => $req['payment_type'],
                "bank_transfer" => [
                    "bank" => $req['bank'],
                    "va_number" => $req['va_number']
                ],
                "transaction_details" => [
                    "order_id" => $req["order_id"],
                    "gross_amount" => $req["gross_amount"]
                ]
            ];

            $http_header = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic '.$this->auth,
                'Accept' => 'application/json'
            ];
    
            $response = Http::withHeaders($http_header)->post($this->url, $this->transaction_req);
            $data = $response->json();

            if ( $data["status_code"] == "406") {
                return response()->json(["status" => "failed", 
                                         "message" => "Transaksi sudah dilakukan! periksa kembali order_id anda"], 406);
            }else {
                $this->insertData = [
                    "order_id" => $data["order_id"],
                    "transaction_id" => $data["transaction_id"],
                    "payment_type" => $data["payment_type"],
                    "gross_amount" => $data["gross_amount"],
                    "transaction_time" => $data["transaction_time"],
                    "transaction_status" => $data["transaction_status"]
                ];
            }

        }
        
        if (Payment::create($this->insertData)){
            return $this->response->baseResponse("Transaction successfully!", $data, true, 201);
        } else {
            return $this->response->baseResponse("Failed send transaction", $this->data, false, 400);
        }
    }
        
    public function find($id)
    {
        $data = Payment::find($id);

        if ( $data ){
            return $this->response->baseResponse("Success retrive data", $data, true, 201);
        } else {
            return $this->response->baseResponse("Customer not found!", $this->data, false, 400);
        }
    }


    public function delete($id)
    {
        $data = Payment::find($id);
        if ($data->delete()){
            return $this->response->baseResponse("Success remove data", $data, true, 201);
        } else {
            return $this->response->baseResponse("Failed remove data", $this->data, false, 400);
        }
    }


    public function pushNotif(Request $request){
        $req = $request->all();
        $pay = Payment::where('order_id', $req["order_id"])->get();
        
        $payment = Payment::find($pay[0]->id);
        
        if(!$pay){
            return response()->json(["status" => "error", "messages" => "Id order not found"], 401);
        }

    
        $payment->transaction_status = $req["transaction_status"];
        $payment->transaction_time = $req["transaction_time"];
        $payment->transaction_id = $req["transaction_id"];

        if($payment->save()){
            return response()->json(["status" => "success", "messages" => "Transaksi berhasil diperbaharui!"], 200);
        }

    }
}
