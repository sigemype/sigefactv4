<?php

namespace Modules\WhatsAppApi\Services;

use Modules\WhatsAppApi\Helpers\HttpConnectionApi;
use App\Models\Tenant\{
    Company
};
use Exception;


class WhatsAppCloudApi
{

    private const GRAPH_URL = 'https://graph.facebook.com/';
    protected $api_version;
    protected $phone_number_id;
    protected $token;
    protected $base_url;
    protected $http_connection;

    
    /**
     * @return void
     */
    public function __construct()
    {
        $company = Company::selectDataWhatsAppApi()->first();
        $this->phone_number_id = $company->ws_api_phone_number_id;
        $this->token = $company->ws_api_token;
        $this->api_version = config('whatsappapi.whatsapp_cloud_api_version');
        $this->base_url = self::GRAPH_URL.$this->api_version.'/'.$this->phone_number_id.'/';
        $this->http_connection = new HttpConnectionApi($this->token);

    }
    
    
    /**
     * 
     * Enviar mensaje
     *
     * @param  array $params_data
     * @param  string $prefix_number
     * @return array
     * 
     */
    public function sendMessage($params_data)
    {
        $validate_data = $this->validateData();
        if(!$validate_data['success']) return $validate_data;

        try
        {
            $params = $this->getGeneralParams($params_data);
            // dd($params, $this->base_url, $this->token);

            $response = $this->http_connection->sendRequest("{$this->base_url}messages", $params, 'POST');
            // dd($response);

            return $this->processResponse($response);
            
        } 
        catch(Exception $e) 
        {
            return $this->http_connection->responseError($e);
        }
    }
    
        
    /**
     * 
     * Validar datos
     *
     * @return array
     */
    private function validateData()
    {
        // datos de configuracion
        if(!$this->phone_number_id)
        {
            return [
                'success' => false,
                'message' => 'No tiene registrado correctamente el identificador de número de teléfono',
            ];
        }

        if(!$this->token)
        {
            return [
                'success' => false,
                'message' => 'No tiene registrado correctamente el token de acceso',
            ];
        }
        // datos de configuracion

        
        return [
            'success' => true,
            'message' => null,
        ];
    }


    /**
     * 
     * Procesar respuesta / Validar errores
     *
     * @param  array $response
     * @return array
     */
    private function processResponse($response)
    {
        $error_data = $response['error'] ?? null;

        if($error_data)
        {
            $error_message = $error_data['message'] ?? '';
            
            return $this->http_connection->responseMessage(false, 'Ocurrió un problema al enviar el mensaje: '.$error_message);
        }

        $messages = $response['messages'] ?? [];

        if(count($messages) > 0)
        {
            return $this->http_connection->responseMessage(true, 'Mensaje enviado correctamente.');
        }

        return $this->http_connection->responseMessage(false, 'Ocurrió un problema al enviar el mensaje');
    }

    
    /**
     * 
     * Obtener parámetros
     *
     * @param  array $params
     * @return array
     */
    private function getGeneralParams($params)
    {
        $type = $params['send_type'];
        $prefix_number = $params['prefix_number'] ?? '51';

        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $prefix_number.$params['phone_number'],
            'type' => $type
        ];

        if($type === 'document')
        {
            $data['document'] = $params['document'];
        }
        else
        {
            $data['text'] = [
                'preview_url' => false,
                'body' => $params['message']
            ];
        }
        
        return $data;
    }
    
}