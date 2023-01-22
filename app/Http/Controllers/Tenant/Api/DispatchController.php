<?php

namespace App\Http\Controllers\Tenant\Api;

use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Company;
use App\Models\Tenant\Dispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\ApiPeruDev\Http\Controllers\ServiceDispatchController;

class DispatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('input.request:dispatch,api', ['only' => ['store']]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'delivery.address' => 'required|max:100',
            'origin.address' => 'required|max:100',
        ]);

        $fact = DB::connection('tenant')->transaction(function () use ($request) {
            $facturalo = new Facturalo();
            $facturalo->save($request->all());
            $document = $facturalo->getDocument();
            $data = (new ServiceDispatchController())->getData($document->id);
            $facturalo->setXmlUnsigned((new ServiceDispatchController())->createXmlUnsigned($data));
            $facturalo->signXmlUnsigned();
//            $facturalo->createPdf();
//            $facturalo->sendEmail();

            return $facturalo;
        });

//        return $fact;

        $document = $fact->getDocument();

        $company = Company::query()
            ->select('soap_type_id')
            ->first();

        $res = null;
        $ticket = null;
        $reception_date = null;

        if ($company->soap_type_id === '02') {
            $res = ((new ServiceDispatchController())->send($document->external_id));
            $ticket = $res['ticket'];
            $reception_date = $res['reception_date'];
        }

        return [
            'success' => true,
            'data' => [
                'number' => $document->number_full,
                'filename' => $document->filename,
                'external_id' => $document->external_id,
                'ticket' => $ticket,
                'reception_date' => $reception_date,
            ],
            'res' => $res
        ];
    }

    public function statusTicket(Request $request)
    {
        $external_id = $request->input('external_id');
        $record = Dispatch::query()
            ->where('external_id', $external_id)
            ->first();

//        if ($record->soap_type_id === '02') {
            $res = ((new ServiceDispatchController())->statusTicket($external_id));
            (new Facturalo())->createPdf($record, 'dispatch', 'a4');

            return $res;
//        }
//
//        return [
//            'success' => false,
//            'data' => 'No es posible consultar el ticket de un comprobante registrado en un enterno DEMO'
//        ];
    }
}
