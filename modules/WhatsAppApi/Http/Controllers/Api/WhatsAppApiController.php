<?php

namespace Modules\WhatsAppApi\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\WhatsAppApi\Services\WhatsAppCloudApi;
use Modules\WhatsAppApi\Http\Requests\Api\SendMessageRequest;


class WhatsAppApiController extends Controller
{
    
    /**
     * Enviar mensaje
     * Disponible texto y documento pdf
     *
     * @param  SendMessageRequest $request
     * @return array
     */
    public function sendMessage(SendMessageRequest $request)
    {
        return (new WhatsAppCloudApi())->sendMessage($request->all());
    }

}
