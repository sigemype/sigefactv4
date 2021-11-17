<?php

namespace App\Http\Controllers;

use App\Mail\ContactWebEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebController extends Controller{
    
    public function index(){       
        return view('web.index');
    }

    public function send_mail(Request $request){
        Mail::to('facturacion@sigefact.pe')
            ->bcc('contabilidad@jjmm.com.pe')
            ->send(new ContactWebEmail( $request->nombre, $request->empresa, $request->telefono, $request->ruc, $request->correo, $request->mensaje));

            return redirect()->route('web.index');
    }
}
