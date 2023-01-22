<?php

namespace Modules\WhatsAppApi\Helpers;

use Illuminate\Support\Facades\Log;
use Exception;


class HttpConnectionApi
{

    protected $token;
    
    /**
     *
     * @param  string $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    
    /**
     * 
     * Petición
     *
     * @param  string $url
     * @param  array $params
     * @param  string $method
     * @return array
     */
    public function sendRequest($url, $params, $method)
    {

        try {

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$this->token}"
            ));

            $response = curl_exec($ch);
            $curl_error = curl_error($ch);

            if($curl_error) return $this->responseMessage(false, 'Error en la petición a la Api');

            return json_decode($response, true);

        }catch (Exception $e)
        {
            return $this->responseError($e);
        }

    }

        
    /**
     *
     * @param  boolean $success
     * @param  string $message
     * @return array
     */
    public function responseMessage($success, $message)
    {
        return [
            'success' => $success,
            'message' => $message,
        ];
    }
    
    
    /**
     *
     * @param  Exception $e
     * @return array
     */
    public function responseError($e)
    {
        Log::error("Line: {$e->getLine()} - File: {$e->getFile()} Message: {$e->getMessage()}");

        return [
            'success' => false,
            'message' => "Error desconocido: {$e->getMessage()}",
        ];
    }

}
