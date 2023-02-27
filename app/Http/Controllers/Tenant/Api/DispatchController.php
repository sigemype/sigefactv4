<?php

namespace App\Http\Controllers\Tenant\Api;

use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Controller;
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
            return $facturalo;
        });

        $document = $fact->getDocument();

        return [
            'success' => true,
            'data' => [
                'number' => $document->number_full,
                'filename' => $document->filename,
                'external_id' => $document->external_id,
            ],
        ];
    }

    public function send(Request $request)
    {
        $external_id = $request->input('external_id');
        $record = Dispatch::query()
            ->where('external_id', $external_id)
            ->first();
        if (!$record) {
            return [
                'success' => false,
                'message' => 'El external id es incorrecto'
            ];
        }
        return ((new ServiceDispatchController())->send($external_id));
    }

    public function statusTicket(Request $request)
    {
        $external_id = $request->input('external_id');
        $record = Dispatch::query()
            ->where('external_id', $external_id)
            ->first();
        if (!$record) {
            return [
                'success' => false,
                'message' => 'El external id es incorrecto'
            ];
        }
        $res = ((new ServiceDispatchController())->statusTicket($external_id));
        (new Facturalo())->createPdf($record, 'dispatch', 'a4');
        return $res;

    }
}
