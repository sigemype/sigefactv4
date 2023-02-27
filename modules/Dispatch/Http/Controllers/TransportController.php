<?php

namespace Modules\Dispatch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dispatch\Http\Requests\TransportRequest;
use Modules\Dispatch\Http\Resources\TransportCollection;
use Modules\Dispatch\Http\Resources\TransportResource;
use Modules\Dispatch\Models\Transport;

class TransportController extends Controller
{
    public function index()
    {
        return view('tenant.dispatches.transports.index');
    }

    public function columns()
    {
        return [
            'plate_number' => 'Nro. de Placa',
            'model' => 'Modelo',
            'brand' => 'Marca',
        ];
    }

    public function records(Request $request)
    {
        $records = Transport::where($request->column, 'like', "%{$request->value}%")
                            ->orderBy('plate_number');

        return new TransportCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function record($id)
    {
        $record = new TransportResource(Transport::findOrFail($id));

        return $record;
    }

    public function store(TransportRequest $request)
    {
        $id = $request->input('id');
        $is_default = $request->input('is_default');
        if($is_default) {
            Transport::query()->update([
                'is_default' => false
            ]);
        }
        $record = Transport::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Vehículo editado con éxito':'Vehículo registrado con éxito',
            'id' => $record->id
        ];
    }

    public function destroy($id)
    {
        $record = Transport::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Vehículo eliminado con éxito'
        ];
    }

    public function getOptions()
    {
        return Transport::query()
            ->where('is_active', true)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'plate_number' => $row->plate_number,
                    'model' => $row->model,
                    'brand' => $row->brand,
                    'is_default' => $row->is_default
                ];
            });
    }
}
