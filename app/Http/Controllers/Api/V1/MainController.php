<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function status()
    {
        return response()->json([
            'status' => 'API is running',
            'version' => '1.0.0',
        ]);
    }
}
