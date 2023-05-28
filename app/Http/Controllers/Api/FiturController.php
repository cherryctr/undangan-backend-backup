<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fitur;
// use Illuminate\Http\Request;

class FiturController extends Controller
{
    //
    public function index()
    {
        $fitur = Fitur::latest()->get();
        return response()->json([
            'success'   => true,
            'message'   => 'List Data Fitur',
            'products'  => $fitur
        ], 200);
    }

}
