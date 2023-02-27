<?php

namespace Modules\Dispatch\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use Modules\ApiPeruDev\Data\ServiceData;
use Modules\Dispatch\Http\Requests\DispatchAddressRequest;
use Modules\Dispatch\Models\DispatchAddress;

class DispatchAddressController extends Controller
{
    public function tables()
    {
        $locations = func_get_locations();

        return [
            'locations' => $locations
        ];
    }

    public function store(DispatchAddressRequest $request)
    {
        $id = $request->input('id');
        $record = DispatchAddress::query()->firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'id' => $record->id
        ];
    }

    public function destroy($id)
    {
        DispatchAddress::query()
            ->find($id)
            ->update([
                'is_active' => false,
            ]);

        return [
            'success' => true,
            'message' => 'Dirección eliminada con éxito'
        ];
    }

    public function getOptions($person_id)
    {
        return DispatchAddress::query()
            ->where('person_id', $person_id)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'location_id' => $row->location_id,
                    'address' => $row->address
                ];
            });
    }

    public function searchAddress($person_id)
    {
        $person = Person::query()->find($person_id);
        if ($person->identity_document_type_id === '1') {
            $type = 'dni';
        } elseif ($person->identity_document_type_id === '6') {
            $type = 'ruc';
        } else {
            return [
                'success' => false,
                'message' => 'No se encontró dirección'
            ];
        }
        return (new ServiceData())->service($type, $person->number);
    }
}
