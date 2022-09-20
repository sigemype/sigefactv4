<?php

namespace Modules\MobileApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\User;
use Modules\MobileApp\Http\Resources\AppPermissionResource;
use Modules\MobileApp\Models\AppModule;


class AppPermissionController extends Controller
{


    /**
     * @return array
     */
    public function record($id)
    {
        return new AppPermissionResource(User::findOrFail($id));
    }


    /**
     * @return array
     */
    public function tables()
    {
        $app_configuration = app(AppConfigurationController::class)->record();
        $pos_document_types = collect(auth()->user()->getPosDocumentTypes())->pluck('module');

        return compact('app_configuration', 'pos_document_types');
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

        $user = User::findOrFail($request->id);
        $app_modules = collect($request->app_modules)->where('checked', true)->pluck('id')->toArray();
        $user->app_modules()->sync($app_modules);

        return [
            'success' => true,
            'message' => 'Permisos actualizados correctamente',
        ];

    }


}