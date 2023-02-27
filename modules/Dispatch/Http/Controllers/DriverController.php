<?php

namespace Modules\Dispatch\Http\Controllers;

use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dispatch\Http\Requests\DriverRequest;
use Modules\Dispatch\Http\Resources\DriverCollection;
use Modules\Dispatch\Http\Resources\DriverResource;
use Modules\Dispatch\Models\Driver;

class DriverController extends Controller
{

    public function index()
    {
        return view('tenant.dispatches.drivers.index');
    }

    public function columns()
    {
        return [
            'name' => 'Nombre',
            'number' => 'Número',
        ];
    }

    public function records(Request $request)
    {
        $records = Driver::where($request->column, 'like', "%{$request->value}%")
                            ->orderBy('name');

        return new DriverCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function tables()
    {
        $identity_document_types = IdentityDocumentType::whereActive()->get();
        $api_service_token = \App\Models\Tenant\Configuration::getApiServiceToken();

        return compact('identity_document_types', 'api_service_token');
    }

    public function record($id)
    {
        $record = new DriverResource(Driver::findOrFail($id));

        return $record;
    }

    public function store(DriverRequest $request)
    {
        $id = $request->input('id');
        $is_default = $request->input('is_default');
        if($is_default) {
            Driver::query()->update([
                'is_default' => false
            ]);
        }

        $record = Driver::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Conductor editado con éxito':'Conductor registrado con éxito',
            'id' => $record->id
        ];
    }

    public function destroy($id)
    {
        $record = Driver::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Conductor eliminado con éxito'
        ];
    }

    public function getOptions()
    {
        return Driver::query()
            ->where('is_active', true)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'number' => $row->number,
                    'name' => $row->name,
                    'license' => $row->license,
                    'telephone' => $row->telephone,
                    'is_default' => $row->is_default,
                ];
            });
    }
}
