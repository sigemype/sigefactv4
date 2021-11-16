<?php

namespace App\Http\Controllers;

use App\Models\Tenant\Person;
use App\Models\Tenant\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 * @mixin BaseController
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Devuelve un cliente basado en el id
     * @param int $id
     *
     * @return array
     */
    public function searchClientById($id = 0){

        $customers = Person::whereType('customers')
            ->where('id',$id)
            ->get()
            ->transform(function($row) {
                /** @var Person $row */
                return  $row->getCollectionData();
            });

        return compact('customers');
    }

    /**
     * Valida si existe una conexion valida al sitio de destino, se requiere configuracion adicional para guardarlo en el archivo
     * en ese caso se debe revisar la funcion ExtraLog
     *
     * @param string $host
     * @param int    $wait_seconds
     */
    public function pingSite ($host = 'demo.facturalo.pro', $wait_seconds = 1){

        $ports = [
            'http'  => 80,
            'https' => 443,
        ];
        $string = '';
        foreach ($ports as $key => $port) {
            $fp = @fsockopen($host, $port, $errCode, $errStr, $wait_seconds);
            $string .= "<br>Ping $host:$port ($key) ==> ";
            if ($fp) {
                $string.= 'SUCCESS';
                fclose($fp);
            } else {
                $string.= "ERROR: $errCode - $errStr";
            }
            $string.= PHP_EOL;
        }
        self::ExtraLog(__FILE__."::".__LINE__." \n Validando Host $host  \n>>>>\n$string");
    }

    /**
     * Guarda un log en facturalo si la variable EXTRA_LOG es verdadera en el archivo .env
     * @param string $string
     */
    public static function ExtraLog($string = ''){
        if(\Config('extra.extra_log') === true){
            \Log::channel('facturalo')->debug(
                "\n**************************************DEBUG SE ENCUENTRA ACTIVADO**********************************************************************************\n".
                $string.
                "\n**************************************DEBUG SE ENCUENTRA ACTIVADO**********************************************************************************\n");
        }
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function searchItemById($id)
    {
        $items = SearchItemController::getItemsToDocuments(null, $id);

        return compact('items');

    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function searchItems(Request $request)
    {
        $items = SearchItemController::getItemsToDocuments($request);

        return compact('items');
    }

    /**
     * @return User[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    public function getSellers()
    {

        return User::whereIn('type', ['seller', 'admin'])->orderBy('name')->get()->transform(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'type' => $row->type,
            ];
        });
    }

    /**
     * Metodo general para buscar clientes
     * @param Request $request
     *
     * @return array
     */
    public function searchCustomers(Request $request)
    {

        //true de boletas en env esta en true filtra a los con dni   , false a todos
        $identity_document_type_id = [1, 4, 6, 7, 0];
        if (in_array($request->operation_type_id, ['0101', '1001', '1004'])) {
            $identity_document_type_id = config('tenant.document_type_03_filter') ? [1] : [1, 4, 6, 7, 0];
            if ($request->document_type_id == '01') {
                $identity_document_type_id = [6];
            }
        }
        //dispatcher
        if($request->has('searchBy')) {
            if ($request->searchBy == 'dispatches') {
                $identity_document_type_id = ['6', '4', '1'];
            }
        }
        $customers = Person::where('number','like', "%{$request->input}%")
            ->orWhere('name','like', "%{$request->input}%")
            ->whereType('customers')->orderBy('name')
            ->whereIn('identity_document_type_id',$identity_document_type_id)
            ->whereIsEnabled()
            ->get()->transform(function($row) {
                /** @var  Person $row */
                return $row->getCollectionData();
            });

        return compact('customers');
    }


}
