<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Payments extends Controller
{
    public function index()
    {
        return view('Payment');
    }
}
