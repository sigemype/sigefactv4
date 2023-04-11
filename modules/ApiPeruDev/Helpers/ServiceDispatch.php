<?php

namespace Modules\ApiPeruDev\Helpers;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ServiceDispatch
{
    protected $ruc;
    protected $is_demo;
    protected $soap_username;
    protected $soap_password;
    protected $client_id;
    protected $client_secret;

    public function setCredentials($ruc, $is_demo, $soap_username, $soap_password, $client_id, $client_secret)
    {
        $this->ruc = $ruc;
        $this->is_demo = $is_demo;

        if ($this->is_demo) {
            $this->soap_username = "{$this->ruc}MODDATOS";
            $this->soap_password = 'MODDATOS';
            $this->client_id = DispatchEndpoints::CLIENT_ID_BETA;
            $this->client_secret = DispatchEndpoints::CLIENT_SECRET_BETA;
        } else {
            $this->soap_username = $soap_username;
            $this->soap_password = $soap_password;
            $this->client_id = $client_id;
            $this->client_secret = $client_secret;
        }
    }

    private function getEndpointToken()
    {
        $url = ($this->is_demo) ? DispatchEndpoints::TOKEN_BETA : DispatchEndpoints::TOKEN;

        return str_replace('{client_id}', $this->client_id, $url);
    }

    private function getEndpointSend($filename)
    {
        $url = ($this->is_demo) ? DispatchEndpoints::SEND_BETA : DispatchEndpoints::SEND;

        return str_replace('{filename}', $filename, $url);
    }

    private function getEndpointTicket($numTicket)
    {
        $url = ($this->is_demo) ? DispatchEndpoints::TICKET_BETA : DispatchEndpoints::TICKET;

        return str_replace('{numTicket}', $numTicket, $url);
    }

    public function getToken()
    {
        try {
            if (Cache::has("{$this->ruc}_token_sunat")) {
                return [
                    'success' => true,
                    'token' => Cache::get("{$this->ruc}_token_sunat"),
                    'cache' => true
                ];
            }

            if (is_null($this->soap_username) || $this->soap_username === '') {
                throw new Exception('El Soap Username es requerido');
            }
            if (is_null($this->soap_password) || $this->soap_password === '') {
                throw new Exception('El Soap Password es requerido');
            }
            if (is_null($this->client_id) || $this->client_id === '') {
                throw new Exception('El Client ID es requerido');
            }
            if (is_null($this->client_secret) || $this->client_secret === '') {
                throw new Exception('El Client Secret es requerido');
            }

            $curl = curl_init();
            $form_params = [
                'grant_type' => 'password',
                'scope' => 'https://api-cpe.sunat.gob.pe',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'username' => $this->soap_username,
                'password' => $this->soap_password,
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->getEndpointToken(),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => http_build_query($form_params),
                CURLOPT_HTTPHEADER => array(
                    'Conte: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $data = json_decode($response, true);

            if (array_key_exists('access_token', $data)) {
                $token = $data['access_token'];
                Cache::put("{$this->ruc}_token_sunat", $token, 60);
                return [
                    'success' => true,
                    'token' => $token,
                    'cache' => false
                ];
            }

            $error_description = $data['error_description'] ?? '';
            $error = $data['error'] ?? '';

            $message = 'Error al obtener token - error_description: ' . $error_description . ' error: ' . $error;

            return [
                'success' => false,
                'message' => $message
            ];
        } catch (Exception $e) {
            $message = "Code: {$e->getCode()} - Message: {$e->getMessage()}";
            Log::info($message . ' getToken');
            return [
                'success' => false,
                'message' => $message
            ];
        }
    }

    public function send($filename, $file_content)
    {
        try {
            $res = $this->getToken();
            if (!$res['success']) {
                throw new Exception($res['message']);
            }
            $token = $res['token'];
            $file_zip = (new Zip())->compress($filename . '.xml', $file_content);
            $form_params = [
                "archivo" => [
                    'nomArchivo' => $filename . '.zip',
                    'arcGreZip' => base64_encode($file_zip),
                    'hashZip' => hash('sha256', $file_zip)
                ]
            ];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->getEndpointSend($filename),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => json_encode($form_params),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $res = json_decode($response, true);

            if (key_exists('cod', $res)) {

            } else {

            }
            if (key_exists('status', $res)) {
                if ($res['status'] === 401) {
                    throw new Exception('No se encuentra autorizado');
                }
            }
            return [
                'success' => true,
                'data' => $res
            ];
        } catch (Exception $e) {
            $message = "Code: {$e->getCode()} - Message: {$e->getMessage()}";
            Log::info($message . ' send');
            return [
                'success' => false,
                'message' => $message
            ];
        }
    }

    public function ticket($numTicket)
    {
        try {
            $res = $this->getToken();
            if (!$res['success']) {
                throw new Exception($res['message']);
            }
            $token = $res['token'];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->getEndpointTicket($numTicket),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            return json_decode($response, true);
        } catch (Exception $e) {
            $message = "Code: {$e->getCode()} - Message: {$e->getMessage()}";
            return [
                'success' => false,
                'message' => $message
            ];
        }
    }
}
