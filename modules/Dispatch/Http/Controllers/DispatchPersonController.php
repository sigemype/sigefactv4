<?php

namespace Modules\Dispatch\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\Person;
use Illuminate\Http\Request;
use Modules\Dispatch\Http\Requests\DispatchPersonRequest;
use Modules\Dispatch\Models\DispatchAddress;
use Modules\Dispatch\Models\DispatchPerson;

class DispatchPersonController extends Controller
{
    public function tables()
    {
        $locations = func_get_locations();
        $identity_document_types = IdentityDocumentType::query()
            ->where('active', true)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->description,
                ];
            });

        return [
            'identity_document_types' => $identity_document_types,
            'locations' => $locations
        ];
    }

    public function store(DispatchPersonRequest $request)
    {
        $data = $request->all();
        $record = Person::query()->create($data);

        $data['person_id'] = $record->id;
        $address = DispatchAddress::query()->create($data);

        return [
            'success' => true,
            'data' => [
                'person_id' => $record->id,
                'address_id' => $address->id
            ]
        ];

//        $id = $request->input('id');
//        $record = DispatchPerson::query()->firstOrNew(['id' => $id]);
//        $record->fill($request->all());
//        $record->save();
//
//        return [
//            'success' => true,
//            'id' => $record->id
//        ];
    }

    public function getOptions()
    {
        return Person::query()
            ->without('country', 'department', 'province', 'district')
            ->where('type', 'customers')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'identity_document_type_description' => $row->identity_document_type->description,
                    'number' => $row->number,
                    'name' => $row->name,
                    'description' => $row->number . ' - ' . $row->name
                ];
            });
    }

    public function getFilterOptions(Request $request)
    {
        $input = $request->input('input');

        return Person::query()
            ->without('country', 'department', 'province', 'district')
            ->where('type', 'customers')
            ->where('number', 'like', "%{$input}%")
            ->orWhere('name', 'like', "%{$input}%")
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'identity_document_type_description' => $row->identity_document_type->description,
                    'number' => $row->number,
                    'name' => $row->name,
                    'description' => $row->number . ' - ' . $row->name
                ];
            });
    }
//    public function getOptions()
//    {
//        return DispatchPerson::query()
//            ->with('identity_document_type')
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => $row->id,
//                    'identity_document_type_id' => $row->identity_document_type_id,
//                    'identity_document_type_description' => $row->identity_document_type->description,
//                    'number' => $row->number,
//                    'name' => $row->name,
//                    'description' => $row->number . ' - ' . $row->name
//                ];
//            });
//    }

//    public function getFilterOptions(Request $request)
//    {
//        $input = $request->input('input');
//
//        return DispatchPerson::query()
//            ->with('identity_document_type')
//            ->where('number', 'like', "%{$input}%")
//            ->orWhere('name', 'like', "%{$input}%")
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => $row->id,
//                    'identity_document_type_id' => $row->identity_document_type_id,
//                    'identity_document_type_description' => $row->identity_document_type->description,
//                    'number' => $row->number,
//                    'name' => $row->name,
//                    'description' => $row->number . ' - ' . $row->name
//                ];
//            });
//    }
}
