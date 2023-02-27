<?php

namespace Modules\Dispatch\Http\Controllers;

use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dispatch\Http\Requests\DispatcherRequest;
use Modules\Dispatch\Http\Resources\DispatcherCollection;
use Modules\Dispatch\Http\Resources\DispatcherResource;
use Modules\Dispatch\Models\Dispatcher;

class DispatcherController extends Controller
{
    public function index()
    {
        return view('tenant.dispatches.dispatchers.index');
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
        $records = Dispatcher::where($request->column, 'like', "%{$request->value}%")
                            ->orderBy('name');

        return new DispatcherCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function tables()
    {
        $identity_document_types = IdentityDocumentType::whereActive()->get();
        // $api_service_token = config('configuration.api_service_token');
        $api_service_token = \App\Models\Tenant\Configuration::getApiServiceToken();
        return compact('identity_document_types', 'api_service_token');
    }

    public function record($id)
    {
        $record = new DispatcherResource(Dispatcher::findOrFail($id));

        return $record;
    }

    public function store(DispatcherRequest $request)
    {
        $id = $request->input('id');
        $is_default = $request->input('is_default');
        if($is_default) {
            Dispatcher::query()->update([
                'is_default' => false
            ]);
        }

        $record = Dispatcher::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Transportista editado con éxito':'Transportista registrado con éxito',
            'id' => $record->id
        ];
    }

    public function destroy($id)
    {
        $record = Dispatcher::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Transportista eliminado con éxito'
        ];
    }

    public function getOptions()
    {
        return Dispatcher::query()
            ->where('is_active', true)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'number' => $row->number,
                    'name' => $row->name,
                    'number_mtc' => $row->number_mtc,
                    'is_default' => $row->is_default,
                ];
            });
    }
}
