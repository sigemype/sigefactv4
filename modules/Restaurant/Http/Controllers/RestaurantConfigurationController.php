<?php

namespace Modules\Restaurant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Restaurant\Models\RestaurantConfiguration;
use Modules\Restaurant\Models\RestaurantRole;
use Modules\Restaurant\Models\RestaurantTable;
use Modules\Restaurant\Models\RestaurantTableEnv;
use App\Models\Tenant\User;
use App\Models\Tenant\Company;



class RestaurantConfigurationController extends Controller
{
    /**
     * muestra vista para utilizar en mozo
     */
    public function configuration()
    {
        return view('restaurant::configuration.index');
    }

    /**
     * obtiene configuración para utilizar en mozo
     */
    public function record()
    {
        $configurations = RestaurantConfiguration::first();
        $company = Company::query()->first();
        $user = auth()->user();

        return [
            'success' => true,
            'data' => $configurations->getCollectionData(),
            'info' => ['ruc' => $company->number, 'userEmail' => $user->email, 'socketServer' => config('tenant.socket_server') ?? 'http://localhost:8070'],
        ];
    }

    public function tablesAndEnv()
    {
        $tables = RestaurantTable::whereNotNull('environment')->get()->transform(function ($row) {
            return (object)[
                'id' => $row->id,
                'status' => $row->status,
                'products' => (array)$row->products,
                'total' => (float)$row->total,
                'personas' => $row->personas,
                'label' => $row->label,
                'shape' => $row->shape,
                'environment' => $row->environment,
            ];
        });

        $configuration = RestaurantConfiguration::first();

        return [
            'tables' => $tables,
            'enabled_environment_1' => (object)['active' => (bool)$configuration->enabled_environment_1, 'tablesQuantity' => $configuration->tables_quantity],
            'enabled_environment_2' => (object)['active' => (bool)$configuration->enabled_environment_2, 'tablesQuantity' => $configuration->tables_quantity_environment_2],
            'enabled_environment_3' => (object)['active' => (bool)$configuration->enabled_environment_3, 'tablesQuantity' => $configuration->tables_quantity_environment_3],
            'enabled_environment_4' => (object)['active' => (bool)$configuration->enabled_environment_4, 'tablesQuantity' => $configuration->tables_quantity_environment_4],
        ];
    }

    /**
     * guarda cada nueva configuración para utilizar en mozo
     */
    public function setConfiguration(Request $request)
    {
        $configuration = RestaurantConfiguration::first();
        $configuration->fill($request->all());
        if (!$configuration->menu_pos && !$configuration->menu_order && !$configuration->menu_tables) {
            $configuration->menu_pos = true;
        }
        $configuration->save();

        $this->generateMesas($request);

        return [
            'success' => true,
            'configuration' => $configuration->getCollectionData(),
            'message' => 'Configuración actualizada',
        ];
    }

    private function generateMesas($request)
    {
        $enabled_environment_1 = (bool)$request->enabled_environment_1;
        $enabled_environment_2 = (bool)$request->enabled_environment_2;
        $enabled_environment_3 = (bool)$request->enabled_environment_3;
        $enabled_environment_4 = (bool)$request->enabled_environment_4;

        $tables_quantity_environment_1 = (int)$request->tables_quantity;
        $tables_quantity_environment_2 = (int)$request->tables_quantity_environment_2;
        $tables_quantity_environment_3 = (int)$request->tables_quantity_environment_3;
        $tables_quantity_environment_4 = (int)$request->tables_quantity_environment_4;

        RestaurantTable::truncate();

        //create env 1
        if ($enabled_environment_1) {
            for ($i = 1; $i <= $tables_quantity_environment_1; $i++) {
                RestaurantTable::create([
                    'status' => 'available',
                    'products' => array(),
                    'total' => 0,
                    'personas' => 1,
                    'label' => strval($i),
                    'shape' => 'CUADRADO',
                    'environment' => RestaurantTableEnv::ENV_1,
                ]);
            }
        }

        //create env 2
        if ($enabled_environment_2) {
            for ($i = 1; $i <= $tables_quantity_environment_2; $i++) {
                RestaurantTable::create([
                    'status' => 'available',
                    'products' => [],
                    'total' => 0,
                    'personas' => 1,
                    'label' => strval($i),
                    'shape' => 'CUADRADO',
                    'environment' => RestaurantTableEnv::ENV_2,
                ]);
            }
        }

        //create env 3
        if ($enabled_environment_3) {
            for ($i = 1; $i <= $tables_quantity_environment_3; $i++) {
                RestaurantTable::create([
                    'status' => 'available',
                    'products' => [],
                    'total' => 0,
                    'personas' => 1,
                    'label' => strval($i),
                    'shape' => 'CUADRADO',
                    'environment' => RestaurantTableEnv::ENV_3,
                ]);
            }
        }


        //create env 4
        if ($enabled_environment_4) {
            for ($i = 1; $i <= $tables_quantity_environment_4; $i++) {
                RestaurantTable::create([
                    'status' => 'available',
                    'products' => [],
                    'total' => 0,
                    'personas' => 1,
                    'label' => strval($i),
                    'shape' => 'CUADRADO',
                    'environment' => RestaurantTableEnv::ENV_4,
                ]);
            }
        }
    }

    /**
     * consulta los roles actuales
     */
    public function getRoles()
    {
        $roles = RestaurantRole::orderBy('name', 'ASC')->get();
        $alls = $roles->transform(function ($item) {
            return $item->getCollectionData();
        });

        return [
            'success' => true,
            'data' => $alls
        ];
    }

    /**
     * consulta los usuarios actuales
     */
    public function getUsers()
    {
        $users = User::orderBy('name')->get();
        $alls = $users->transform(function ($item) {
            return $item->getCollectionRestaurantData();
        });

        return [
            'success' => true,
            'data' => $alls
        ];
    }

    /**
     * asigna o actualiza un rol a un usuario
     */
    public function setRole(Request $request)
    {
        $user = User::find($request->user_id);
        $user->restaurant_role_id = $request->role_id;
        $user->save();

        return [
            'success' => true,
            'message' => 'Rol asignado a usuario exitosamente',
        ];
    }

    public function saveTable($id, Request $request)
    {
        $table = RestaurantTable::findOrFail($id);
        $table->fill($request->all());
        $table->save();

        return [
            'success' => true,
            'message' => 'Mesa actualizada.',
        ];
    }

    public function getTable($id)
    {
        $row = RestaurantTable::findOrFail($id);

        $table = (object)[
            'id' => $row->id,
            'status' => $row->status,
            'products' => (array)$row->products,
            'total' => (float)$row->total,
            'personas' => $row->personas,
            'label' => $row->label,
            'shape' => $row->shape,
            'environment' => $row->environment,
        ];

        return compact('table');
    }
}
