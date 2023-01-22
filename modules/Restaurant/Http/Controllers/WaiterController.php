<?php
namespace Modules\Restaurant\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Restaurant\Models\Waiter;
use Illuminate\Http\Request;
use Exception;
use Modules\Restaurant\Models\RestaurantRole;
use App\Models\Tenant\User;

class WaiterController extends Controller
{
    public function records()
    {
        $role_mozo = RestaurantRole::where('code', 'MOZO')->first();
        $records = User::where('restaurant_role_id', $role_mozo ? $role_mozo->id : null)->get()->transform(function ($row){
            return [
                'id' => $row->id,
                'name' => $row->name,
            ];
        });

        return [
            'data' => $records
        ];
    }

    public function record($id)
    {
        $record = Waiter::findOrFail($id);
        return $record;
    }

    public function store(Request $request)
    {

        $id = $request->input('id');
        $bank = Waiter::firstOrNew(['id' => $id]);
        $bank->fill($request->all());
        $bank->save();

        return [
            'success' => true,
            'message' => ($id)?'Mozo editado con éxito':'Mozo registrado con éxito'
        ];
    }

    public function destroy($id)
    {
        try {            
            
            $bank = Waiter::findOrFail($id);
            $bank->delete(); 

            return [
                'success' => true,
                'message' => 'Eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'No puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar'];

        }
    }
}