<?php

namespace Modules\MobileApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class LiveAppController extends Controller
{
    public function index()
    {
        return view('mobileapp::mobile_app.index');
    }

    public function configuration()
    {
        return view('mobileapp::configuration.index');
    }

    public function premium()
    {
        return view('mobileapp::mobile_app_white.index');
    }
}
