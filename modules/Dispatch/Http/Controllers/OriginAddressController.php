<?php

namespace Modules\Dispatch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dispatch\Http\Requests\OriginAddressRequest;
use Modules\Dispatch\Http\Resources\OriginAddressCollection;
use Modules\Dispatch\Http\Resources\OriginAddressResource;
use Modules\Dispatch\Models\OriginAddress;

class OriginAddressController extends Controller
{
    public function index()
    {
        return view('tenant.dispatches.origin_addresses.index');
    }

    public function columns()
    {
        return [
            'address' => 'Dirección',
        ];
    }

    public function tables()
    {
        $locations = func_get_locations();

        return [
            'locations' => $locations
        ];
    }

    public function records(Request $request)
    {
        $records = OriginAddress::where($request->column, 'like', "%{$request->value}%")
                            ->orderBy('address');

        return new OriginAddressCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function record($id)
    {
        $record = new OriginAddressResource(OriginAddress::findOrFail($id));

        return $record;
    }

    public function store(OriginAddressRequest $request)
    {
        $id = $request->input('id');
        $record = OriginAddress::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Dirección editada con éxito':'Dirección registrada con éxito',
            'id' => $record->id
        ];
    }

    public function destroy($id)
    {
        $record = OriginAddress::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Dirección eliminada con éxito'
        ];
    }
}
