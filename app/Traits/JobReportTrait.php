<?php

namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Environment;
use App\Models\Tenant\{
    User,
    DownloadTray,
    Company,
    Establishment,
};
use Illuminate\Support\Facades\Log;


trait JobReportTrait
{
    
                
    /**
     * 
     * Obtener registro asociado al tenant desde la tabla website de system
     * 
     * @return Website
     */
    public function getTenantWebsite()
    {
        return app(Environment::class)->tenant();
    }


    /**
     * 
     * Buscar website
     * 
     * @return Website
     */
    public function findWebsite($website_id)
    {
        return Website::find($website_id);
    }

        
    /**
     * 
     * Retorna usuario autenticado
     *
     * @return User
     */
    public function getCurrentUser()
    {
        return auth()->user();
    }
    

    /**
     *
     * @param  string $message
     * @return void
     */
    public function showLogInfo($message)
    {
        Log::info($message);
    }
    
    
    /**
     * 
     * Registrar reporte en cola
     *
     * @param  int $user_id
     * @param  string $module
     * @param  string $format
     * @param  string $type
     * @return DownloadTray
     */
    public function createDownloadTray($user_id, $module, $format, $type)
    {
        return DownloadTray::create([
            'user_id' => $user_id,
            'module' => $module,
            'format' => $format,
            'date_init' => date('Y-m-d H:i:s'),
            'type' => $type
        ]);
    }

    
    /**
     * 
     * Actualizar registro cuando culmina el proceso del reporte
     *
     * @param  DownloadTray $download_tray
     * @param  string $filename
     * @param  string $path
     * @return void
     */
    public function finishedDownloadTray(DownloadTray &$download_tray, $filename, $path)
    {
        $download_tray->file_name = $filename;
        $download_tray->date_end = date('Y-m-d H:i:s');
        $download_tray->status = 'FINISHED';
        $download_tray->path = $path;
        $download_tray->save();
    }

    
    /**
     * 
     * Buscar DownloadTray
     * 
     * @return DownloadTray
     */
    public function findDownloadTray($id)
    {
        return DownloadTray::find($id);
    }

        
    /**
     * @return Company
     */
    public function getDataCompany()
    {
        return Company::active();
    }

    
    /**
     *
     * @param  int $establishment_id
     * @return Establishment
     */
    public function getDataEstablishment($establishment_id)
    {
        return Establishment::findOrFail($establishment_id);
    }

    
    /**
     *
     * @param  string $format
     * @return string
     */
    public function getReportPath($format)
    {
        return "download_tray_{$format}";
    }

    
    /**
     *
     * @param  string $module
     * @param  string $title
     * @return string
     */
    public function getReportFilename($module, $title)
    {
        return $module."_{$title}_". date('YmdHis');
    }


    /**
     * 
     * Parámetros
     *
     * @param  array $request
     * @param  array $additional_params
     * @return array
     */
    public function getNewParams($request, $additional_params)
    {
        return array_merge($request, $additional_params);
    }

        
    /**
     * 
     * Mensaje
     *
     * @return array
     */
    public function getJobResponse($success = null, $message = null)
    {
        return [
            'success' => $success ?? true,
            'message' => $message ?? 'El reporte se esta procesando, puede ver el proceso en la bandeja de descargas.'
        ];
    }

}
