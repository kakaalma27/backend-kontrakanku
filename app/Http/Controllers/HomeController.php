<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        return view('home',["msg"=>"Hello!"]);
    }
    public function adminHome()
    {
        return view('home',["msg"=>"Hello!"]);
    }
}
