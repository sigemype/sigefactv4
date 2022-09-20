<?php

namespace Modules\Suscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Suscription\Models\Tenant\SuscriptionSection;
use Modules\Suscription\Http\Resources\SectionCollection;
use Modules\Suscription\Http\Resources\SectionResource;
use Modules\Suscription\Http\Requests\SectionRequest;


class SectionController extends Controller
{

    public function columns()
    {
        return [
            'name' => 'Nombre',
        ];
    }

    public function records(Request $request)
    {
        $records = SuscriptionSection::where($request->column, 'like', "%{$request->value}%")->latest('id');

        return new SectionCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function record($id)
    {
        return new SectionResource(SuscriptionSection::findOrFail($id));
    }


    /**
     * 
     * Crea o edita el registro
     *
     * @param SectionRequest $request
     *
     * @return array
     */
    public function store(SectionRequest $request)
    {

        $id = $request->input('id');

        $record = SuscriptionSection::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Sección editada con éxito':'Sección registrada con éxito',
        ];
    }


    public function destroy($id)
    {
        $record = SuscriptionSection::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Sección eliminada con éxito'
        ];
    }

}
