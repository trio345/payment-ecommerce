<?php

namespace App\Http\Controllers;

class ExampleController extends Controller
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
        $datas = Product::all();
        if ( $datas ){
            return response($content = ["status" => "success", "data" => ["attributes" => $datas]], $status = 201);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Product::find($id);

        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'category' => 'required',
            'stock' => 'required',
            'images' => 'required'
        ]);

        $data->name = $request->input('name');
        $data->price = $request->input('price');
        $data->description = $request->input('description');
        $data->category = $request->input('category');
        $data->stock = $request->input('stock');
        $data->images = $request->input('images');
            
        if ( $data->save() ){
            return response($content = ["status" => "success", "data" => ["attributes" => $data]], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }


    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'category' => 'required',
            'stock' => 'required',
            'images' => 'required'
        ]);

        $data = [
            "name" => $request->input('name'),
            "price" => $request->input('price'),
            "description" => $request->input('description'),
            "category" => $request->input('category'),
            "stock" => $request->input('stock'),
            "images" => $request->input('images')
        ];

        if ( Product::create($data) ){
            return response($content = ["status" => "success", "data" => $data], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }


    public function find($id)
    {
        $data = Product::find($id);

        if ( $data ){
            return response($content = ["status" => "success", "data" => ["attributes" => $data]], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"customer not found!"]);
        }
    }


    public function delete($id)
    {
        $data = Product::find($id);
        if ($data->delete()){
            return response($content = ["status" => "success", "messages" => "berhasil dihapus"], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"gagal dihapus!"]);
        }
    }
}
