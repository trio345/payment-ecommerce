<?php

namespace App\Http\Controllers;
use App\Product;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->response = new Controller();
        $this->data = new \stdClass;
    }

    public function index(){
        $datas = Product::all();
        if ( $datas ){
            return $this->response->baseResponse("Success retrive data", $datas, true, 201);
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
            return $this->response->baseResponse("Success retrive data", $data, true, 201);
        } else {
            return $this->response->baseResponse("Failed retrive data", $this->data, false, 400);
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
            "category" => strtolower($request->input('category')),
            "stock" => $request->input('stock'),
            "images" => $request->input('images')
        ];
        
        if ( Product::create($data) ){
            return $this->response->baseResponse("Success create data", $data, true, 201);
        } else {
            return $this->response->baseResponse("Failed retrive data", $this->data, false, 400);
        }
    }


    public function find($id)
    {
        $data = Product::find($id);

        if ( $data ){
            return $this->response->baseResponse("Success retrive data", $data, true, 201);
        } else {
            return $this->response->baseResponse("Failed retrive data", $this->data, false, 400);
        }
    }


    public function delete($id)
    {
        $data = Product::find($id);
        if ($data->delete()){
            return $this->response->baseResponse("Success remove data", $data, true, 201);
        } else {
            return $this->response->baseResponse("Failed remove data", $this->data, false, 400);
        }
    }
}
