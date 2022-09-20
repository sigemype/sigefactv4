<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Document;
use App\Models\Tenant\StateType;
use Modules\MobileApp\Http\Requests\Api\ValidateDocumentRequest;
use App\Models\Tenant\Company;
use Illuminate\Support\Facades\DB;
use App\CoreFacturalo\Services\IntegratedQuery\{
    AuthApi,
    ValidateCpe,
};

class ValidateDocumentController extends Controller
{

    protected $access_token;
    protected $validate_cpe;
    

    /**
     * 
     * Obtener token de sunat y asignarlo
     *
     * @return void
     */
    public function setToken()
    {
        $auth_api = (new AuthApi())->getToken();
        if(!$auth_api['success']) return $auth_api;
        $this->access_token = $auth_api['data']['access_token'] ?? null;

        if(!$this->access_token) throw new Exception('Error al obtener token de autenticación SUNAT');
    }

    
    /**
     * Validar cpe
     *
     * @param  ValidateDocumentRequest $request
     * @return array
     */
    public function validateDocument(ValidateDocumentRequest $request)
    {
        
        $this->setToken();
        $company_number = Company::getRecordIndividualColumn('number');
        $this->validate_cpe = new ValidateCpe($this->access_token,$company_number, $request->document_type_id, $request->series, $request->number, $request->date_of_issue, $request->total);
        $response = $this->validate_cpe->search();

        if(!$response['success']) return $response;

        $data_response = [
            'message' => $response['message'],
            'sunat_state_type_id' => null,
            'code' => '-2',
            'response' => $response,
        ];

        if ($response['success']) 
        {
            $data_response['sunat_state_type_id'] = $response['data']['state_type_id'];
            $data_response['code'] = $response['data']['estadoCp'];
        }
        
        return $this->getResponse($data_response);

    }
    
    
    /**
     * 
     * Generar respuesta
     *
     * @param  array $data_response
     * @return array
     */
    public function getResponse($data_response)
    {

        $sunat_state_type_id = $data_response['sunat_state_type_id'];

        if(is_null($sunat_state_type_id))
        {
            $sunat_state_type_description = 'Error en la busqueda: '.$data_response['message'];
        }
        else
        {
            $sunat_state_type_description = null;
            $state_type = StateType::find($sunat_state_type_id);
            $sunat_state_type_description = ($state_type) ? $state_type->description : 'No existe';
        }

        return [
            'success' => true,
            'message' => 'Consulta realizada correctamente.',
            'data' => [
                'state_type_id' => $sunat_state_type_id,
                'state_type_description' => $sunat_state_type_description,
                'code_sunat' => $data_response['code'],
                'message_sunat' => $data_response['message'],
                'state_ruc' => $this->validate_cpe->getCompanyState($data_response['response']['data']['estadoRuc']),
                'condition_ruc' => $this->validate_cpe->getConditionState($data_response['response']['data']['condDomiRuc']),
            ]
        ];
    }

}
