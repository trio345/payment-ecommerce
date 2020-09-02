<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\MidtransController;
use App\Order;
use App\OrderDetail;
use Illuminate\Support\Facades\Http;




class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // /
    }

    public function index(){
        $datas = Order::with('order_details')->get();
        if ( $datas ){
            return response($content = ["status" => "success", "data" => $datas], $status = 201);
        }
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        $this->validate($request, [
            'user_id' => 'required'
        ]);

        $request_all = $request->all();
        $order->user_id = $request_all["user_id"];
        $order->status = 'created';
        
        if ( $order->save() ){
            $request_order = $request_all["order_detail"];
            for ($i = 0; $i < count($request_order); $i++){
                $order_item = OrderDetail::where('order_id', $id)->first();
                $order_item->product_id = $request_order[$i]["product_id"];
                $order_item->quantity = $request_order[$i]["quantity"];
                $order_item->save();
            }
        }

    return response($content = ["status" => "success", "messages" => "Data berhasil diupdate!"], 201);
    }


    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'order_detail.*' => 'present|array',
            'amount' => 'required'
        ]);
        $order = new Order();
        $request_all = $request->all();
        $order->user_id = $request_all["user_id"];
        $order->amount = $request_all["amount"];
        $order->status = 'created';
        
        if ( $order->save() ){

            $request_order = $request_all["order_detail"];
            for ($i = 0; $i < count($request_order); $i++){
                $order_detail = new OrderDetail();
                $order_detail->order_id = $order->id;
                $order_detail->product_id = $request_order[$i]["product_id"];
                $order_detail->quantity = $request_order[$i]["quantity"];
                $order_detail->save();
            }

            
        }
        return response($content = ["status" => "success", "messages" => "Data berhasil ditambahkan"], 201);

    }


    public function delete($id)
    {
        $order = Order::find($id);
        if ( $order->delete() ){
            $order_item = OrderDetail::where('order_id', $id)->delete();
        
            return response($content = ["status" => "success", "messages" => "Data berhasil dihapus!"], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"customer not found!"]);
        }
    }


    public function find($id)
    {
        $getJoin = Order::where('id', $id)->with('order_details')->get();
        
        if ($getJoin){
            return response()->json(["messages"=>"success retrive data", "status" => true, "data" => $getJoin], 201);
        } else{ 
            return response()->json(["messages"=>"data not found"], 403);
        }
    }
}
