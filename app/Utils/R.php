<?php

namespace App\Utils;

use Illuminate\Routing\Controller as BaseController;

class R extends BaseController
{

    static public function ok($message = null, $data = null)
    {
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data,
        ]);
    }


    static public function error($code, $message = null, $data = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }


    static public function response($data = [])
    {
        return response()->json($data);
    }
}
