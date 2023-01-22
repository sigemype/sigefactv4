<?php

namespace Modules\Restaurant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Exception;
use Modules\Restaurant\Models\RestaurantNote;



class NotesController extends Controller
{
    public function records()
    {
        $records = RestaurantNote::all();

        return compact('records');
    }
   

    public function store(Request $request)
    {
        $id = $request->input('id');
        $bank = RestaurantNote::firstOrNew(['id' => $id]);
        $bank->fill($request->all());
        $bank->save();

        return [
            'success' => true,
            'message' => ($id)?'Editado con éxito':'Registrado con éxito'
        ];
    }

    public function destroy($id)
    {
        try {            
            
            $bank = RestaurantNote::findOrFail($id);
            $bank->delete(); 

            return [
                'success' => true,
                'message' => 'Banco eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'El Registro esta siendo usado por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el Registro'];

        }
    }
}
