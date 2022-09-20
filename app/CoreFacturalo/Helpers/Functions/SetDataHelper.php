<?php

namespace App\CoreFacturalo\Helpers\Functions;

use App\Models\Tenant\Catalogs\District;
use Illuminate\Database\Query\Builder;
use DB;

class SetDataHelper
{
     
    /**
     * 
     * Obtener data para insertar modulo en tenant
     *
     * @param  string $value
     * @param  string $description
     * @param  int $order_menu
     * @return array
     */
    public static function getModuleData($value, $description, $order_menu)
    {
        return [
            'value' => $value,
            'description' => $description,
            'order_menu' => $order_menu,
        ];
    }


    /**
     * Obtener data para insertar modulo en system
     *
     * @param  string $value
     * @param  string $description
     * @param  int $sort
     * @return array
     */
    public static function getModuleDataSystem($value, $description, $sort)
    {
        return [
            'value' => $value,
            'description' => $description,
            'sort' => $sort,
        ];
    }

    
    /**
     * 
     * Obtener conexion con la tabla modules
     *
     * @return Builder
     */
    public static function getModuleConnection($connection)
    {
        return DB::connection($connection)->table('modules');
    }


    /**
     * 
     * Validar y registrar distrito
     *
     * @param  string $district_id
     * @param  string $description
     * @return void
     */
    public static function createDistrict($district_id, $description)
    {

        $district = District::find($district_id);

        if(!$district)
        {
            $province_id = substr($district_id, 0 ,4);

            District::insert([
                'id' => $district_id,
                'province_id' => $province_id,
                'description' => $description,
                'active' => true,
            ]);
        }

    }
    

    /**
     * 
     * Eliminar distrito
     *
     * @param  string $district_id
     * @return void
     */
    public static function deleteDistrict($district_id)
    {
        District::where('id', $district_id)->delete();
    }

}
