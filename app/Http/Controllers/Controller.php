<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function baseResponse($msg, $data, $status, $code){
        return response()->json(["status" => $status, "messages" => $msg, "data" => $data], $status = $code);
    }
}
