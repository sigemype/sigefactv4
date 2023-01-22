<?php

namespace Modules\ApiPeruDev\Services;

use App\Models\Tenant\Company;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\ApiPeruDev\Helpers\Zip;

class DispatchService
{
    public function getToken($soap_username, $soap_password, $client_id, $client_secret)
    {
        try {
            if (Cache::has('token_sunat')) {
                return [
                    'success' => true,
                    'token' => Cache::get('token_sunat'),
                    'cache' => true
                ];
            }
            if (is_null($soap_username) || $soap_username === '') {
                throw new Exception('El Soap Username es requerido');
            }
            if (is_null($soap_password) || $soap_password === '') {
                throw new Exception('El Soap Password es requerido');
            }
            if (is_null($client_id) || $client_id === '') {
                throw new Exception('El Client ID es requerido');
            }
            if (is_null($client_secret) || $client_secret === '') {
                throw new Exception('El Client Secret es requerido');
            }

            $curl = curl_init();
            $form_params = [
                'grant_type' => 'password',
                'scope' => 'https://api-cpe.sunat.gob.pe',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'username' => $soap_username,
                'password' => $soap_password,
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api-seguridad.sunat.gob.pe/v1/clientessol/{$client_id}/oauth2/token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($form_params),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $data = json_decode($response, true);
            Log::info('-----getToken------');
            Log::info($data);
            Log::info('-----getToken------');

            if (array_key_exists('access_token', $data)) {
                $token = $data['access_token'];
                Log::error('Cache toke_sunat actualizado');
                Cache::put('token_sunat', $token, 60);
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

    public function send($soap_username, $soap_password, $client_id, $client_secret, $filename, $file_content)
    {
        try {
            $res = $this->getToken($soap_username, $soap_password, $client_id, $client_secret);
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
                CURLOPT_URL => "https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/{$filename}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($form_params),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $res = json_decode($response, true);
            Log::info('-----send------');
            Log::info($res);
            Log::info('-----send------');

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

    public function ticket($soap_username, $soap_password, $client_id, $client_secret, $numTicket)
    {
        try {
            $res = $this->getToken($soap_username, $soap_password, $client_id, $client_secret);
            if (!$res['success']) {
                throw new Exception($res['message']);
            }
            $token = $res['token'];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/{$numTicket}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);
            Log::info('-----ticket------');
            Log::info($res);
            Log::info('-----ticket------');

            return $res;
        } catch (Exception $e) {
            $message = "Code: {$e->getCode()} - Message: {$e->getMessage()}";
            Log::info($message . ' ticket');
            return [
                'success' => false,
                'message' => $message
            ];
        }
    }
}
