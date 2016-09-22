<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class NetworkController extends Controller
{
    //
    public function index(){
        return view('network.survey');
    }
}
