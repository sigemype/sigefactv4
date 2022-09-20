<?php

namespace Modules\Suscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Suscription\Models\Tenant\SuscriptionGrade;
use Modules\Suscription\Http\Resources\GradeCollection;
use Modules\Suscription\Http\Resources\GradeResource;
use Modules\Suscription\Http\Requests\GradeRequest;


class GradeController extends Controller
{

    public function columns()
    {
        return [
            'name' => 'Nombre',
        ];
    }

    public function records(Request $request)
    {
        $records = SuscriptionGrade::where($request->column, 'like', "%{$request->value}%")->latest('id');

        return new GradeCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function record($id)
    {
        return new GradeResource(SuscriptionGrade::findOrFail($id));
    }


    /**
     * 
     * Crea o edita el registro
     *
     * @param GradeRequest $request
     *
     * @return array
     */
    public function store(GradeRequest $request)
    {

        $id = $request->input('id');

        $record = SuscriptionGrade::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Grado editado con éxito':'Grado registrado con éxito',
        ];
    }


    public function destroy($id)
    {
        $record = SuscriptionGrade::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Grado eliminado con éxito'
        ];
    }

}
