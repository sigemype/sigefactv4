<?php

namespace Modules\MobileApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\MobileApp\Http\Resources\Api\AppConfigurationResource;
use Modules\MobileApp\Models\AppConfiguration;
use Illuminate\Http\Request;
use Modules\MobileApp\Http\Controllers\Api\AppConfigurationController as AppConfigurationControllerApi;


class AppConfigurationController extends Controller
{


    /**
     * @return array
     */
    public function record()
    {
        return app(AppConfigurationControllerApi::class)->record();
    }


    /**
     *
     * Actualizar configuracion gráfica de la app
     *
     * @param  Request $request
     * @return array
     */
    public function store(Request $request)
    {

        $record = AppConfiguration::firstOrFail();
        $record->theme_color = $request->theme_color;
        $record->card_color = $request->card_color;
        $record->header_waves = $request->header_waves;
        $record->app_mode = $request->app_mode;
        $record->direct_send_documents_whatsapp = $request->direct_send_documents_whatsapp;
        $record->save();

        return [
            'success' => true,
            'message' => 'Configuración gráfica actualizada',
            'data' => $record->getRowResource(),
        ];
    }


}