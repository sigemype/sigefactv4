<?php

namespace App\CoreFacturalo\Services\IntegratedQuery;
 
use Exception;
use Carbon\Carbon;

class ValidateCpe
{

    const BASE_URL = 'https://api.sunat.gob.pe/v1/contribuyente/contribuyentes';
    
    protected $company_number;
    protected $document_type_id;
    protected $series;
    protected $number;
    protected $date_of_issue;
    protected $total;
    protected $token;
    
    protected $document_state = [
        '0' => '-1', //'NO EXISTE' custom code
        '1' => '05', //'ACEPTADO'
        '2' => '11', //'ANULADO'
    ];
    
    protected $company_state = [
        '-' => '-',
        '00' => 'ACTIVO',
        '01' => 'BAJA PROVISIONAL',
        '02' => 'BAJA PROV. POR OFICIO',
        '03' => 'SUSPENSION TEMPORAL',
        '10' => 'BAJA DEFINITIVA',
        '11' => 'BAJA DE OFICIO',
        '12' => 'BAJA MULT.INSCR. Y OTROS ',
        '20' => 'NUM. INTERNO IDENTIF.',
        '21' => 'OTROS OBLIGADOS',
        '22' => 'INHABILITADO-VENT.UNICA',
        '30' => 'ANULACION - ERROR SUNAT   '
    ];

    protected $company_condition = [
        '-' => '-',
        '00' => 'HABIDO',
        '01' => 'NO HALLADO SE MUDO DE DOMICILIO',
        '02' => 'NO HALLADO FALLECIO',
        '03' => 'NO HALLADO NO EXISTE DOMICILIO',
        '04' => 'NO HALLADO CERRADO',
        '05' => 'NO HALLADO NRO.PUERTA NO EXISTE',
        '06' => 'NO HALLADO DESTINATARIO DESCONOCIDO',
        '07' => 'NO HALLADO RECHAZADO',
        '08' => 'NO HALLADO OTROS MOTIVOS',
        '09' => 'PENDIENTE',
        '10' => 'NO APLICABLE',
        '11' => 'POR VERIFICAR',
        '12' => 'NO HABIDO',
        '20' => 'NO HALLADO',
        '21' => 'NO EXISTE LA DIRECCION DECLARADA',
        '22' => 'DOMICILIO CERRADO',
        '23' => 'NEGATIVA RECEPCION X PERSONA CAPAZ',
        '24' => 'AUSENCIA DE PERSONA CAPAZ',
        '25' => 'NO APLICABLE X TRAMITE DE REVERSION',
        '40' => 'DEVUELTO'
    ];


    public function __construct($token, $company_number, $document_type_id, $series, $number, $date_of_issue, $total)
    {
        $this->company_number = $company_number;
        $this->document_type_id = $document_type_id;
        $this->series = $series;
        $this->number = $number;
        $this->date_of_issue = Carbon::parse($date_of_issue)->format('d/m/Y');
        $this->total = $total;
        $this->token = $token;
    }


    public function search()
    {

        try {

            $form_params = [
                'numRuc' => $this->company_number,
                'codComp' => $this->document_type_id,
                'numeroSerie' => $this->series,
                'numero' => $this->number,
                'fechaEmision' => $this->date_of_issue,
                'monto' => $this->total,
            ];


            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => self::BASE_URL."/{$this->company_number}/validarcomprobante",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($form_params),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$this->token}",
                    'Content-Type: application/json'
                ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);

            $res = json_decode($response, true);

            // dd($res);

            if($res['success']){

                return [
                    'success' => $res['success'],
                    'message' => $res['message'],
                    'data' => [
                        'state_type_id' =>  isset($this->document_state[ $res['data']['estadoCp'] ]) ? $this->document_state[ $res['data']['estadoCp'] ] : null,
                        'estadoCp' =>  $res['data']['estadoCp'] ?? null,
                        'estadoRuc' =>  $res['data']['estadoRuc'] ?? null,
                        'condDomiRuc' =>  $res['data']['condDomiRuc'] ?? null,
                        'observaciones' =>  $res['data']['observaciones'] ?? null,
                    ],
                ];

            }

            return $res;

        } catch (Exception $e) {

            return [
                'success' => false,
                'message' => "Code: {$e->getCode()} - Message: {$e->getMessage()}"
            ];

        }

    } 

    
    /**
     * 
     * Obtener estado del ruc
     *
     * @param  string $state
     * @return string
     */
    public function getCompanyState($state)
    {
        return $this->company_state[$state] ?? null;
    }

    
    /**
     * 
     * Obtener condicion del ruc
     *
     * @param  string $state
     * @return string
     */
    public function getConditionState($condition)
    {
        return $this->company_condition[$condition] ?? null;
    }
    
}
