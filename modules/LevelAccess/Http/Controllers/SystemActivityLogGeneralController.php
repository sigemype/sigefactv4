<?php

namespace Modules\LevelAccess\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\LevelAccess\Models\SystemActivityLog;
use Modules\LevelAccess\Http\Resources\{
    SystemActivityLogCollection
};
use Carbon\Carbon;
use Modules\LevelAccess\Exports\GeneralFormatExport;
use App\Http\Controllers\Controller;


class SystemActivityLogGeneralController extends Controller
{

    
    public function index()
    {
        return view('levelaccess::system_activity_logs.generals.index');
    }

    
    /**
     *
     * @return array
     */
    public function columns()
    {
        return [
            'date' => 'Fecha',
            'time' => 'Hora',
            'ip' => 'Ip',
        ];
    }

    
    /**
     *
     * @param  Request $request
     * @return SystemActivityLogCollection
     */
    public function records(Request $request)
    {
        $records = $this->getRecords($request);

        return new SystemActivityLogCollection($records->latest()->paginate(config('tenant.items_per_page')));
    }
    

    /**
     *
     * @param  Request $request
     * @return SystemActivityLogCollection
     */
    private function getRecords($request)
    {
        return SystemActivityLog::filterRecords($request);
    }

    
    /**
     * 
     * Verificar si el usuario requiere actualizacion de contraseña de acuerdo a la vigencia configurada
     *
     * @param  Request $request
     * @return array
     */
    public function checkLastPasswordUpdate(Request $request)
    {

        $quantity_month = $request->quantity_month_remember_change_password;
        $last_password_update = auth()->user()->last_password_update;

        $change_success = [
            'success' => true,
            'message' => 'Ha pasado mucho tiempo desde la última vez que modificó su contraseña, debe actualizarla en el módulo Usuarios.',
        ];

        if(!is_null($last_password_update))
        {
            $quantity_week = 1; // semanas para mostrar notificacion antes que cumpla la fecha

            $limit_date = Carbon::parse($last_password_update)->addMonths($quantity_month)->subWeeks($quantity_week);
            // $limit_date = Carbon::parse($last_password_update)->addMonths($quantity_month);
            $today = Carbon::now();
            
            if($limit_date->lte($today)) return $change_success;

            return [
                'success' => false
            ];
        }

        return $change_success;
    }

    
    /**
     *
     * @param  string $type
     * @param  Request $request
     * @return mixed
     */
    public function exportReport($type, Request $request)
    {
        if($type === 'excel')
        {
            $records = $this->getRecords($request)->latest()->get();

            $header_data = $this->generalDataForHeaderReport();

            $data = [
                'company' => $header_data['company'],
                'records' => $records,
            ];
            
            $general_format_export = new GeneralFormatExport();
            $general_format_export->view_name("levelaccess::system_activity_logs.reports.general_{$type}")->data($data);

            return $general_format_export->download($this->generalFilenameReport('Reporte_Actividades_Sistema_Generales', 'xlsx'));

        }

        return $this->generalResponse(false, 'Formato no permitido');
    }

}
