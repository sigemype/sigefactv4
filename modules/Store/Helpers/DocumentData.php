<?php

namespace Modules\Store\Helpers;

use App\Models\Tenant\Company;
use Modules\Inventory\Entities\Company\CompanyEntity;
use Modules\Inventory\Entities\Company\EstablishmentEntity;

class DocumentData
{
    public function setDocumentCommon($record, $data)
    {
        $data->id = $record->id;
        $data->filename = $record->filename;
        $data->soap_type_id = $record->soap_type_id;
        $data->document_type_id = $record->document_type_id;
        $data->document_type_name = func_str_to_upper_utf8($record->document_type->name);
        $data->series = $record->series;
        $data->number = $record->number;
        $data->date_of_issue = $record->date_of_issue;
        $data->time_of_issue = $record->time_of_issue;

        return $data;
    }

    public function setCompany($record = null)
    {
        $company = Company::query()->first();
        if (is_null($record)) {
            $record = $company;
        }

        $company_entity = new CompanyEntity();
        $company_entity->number = $record->number;
        $company_entity->name = $record->name;
        $company_entity->trade_name = $record->trade_name;

        return $company_entity;
    }

    public function setEstablishment($establishment)
    {
        $establishment_entity = new EstablishmentEntity();
        $establishment_entity->code = $establishment->code;
        $establishment_entity->name = $establishment->name;
        $establishment_entity->address = func_str_to_upper_utf8($establishment->address);
        $establishment_entity->location_id = $establishment->location_id;
        $establishment_entity->country_id = $establishment->country_id;
        $establishment_entity->email = $establishment->email;
        $establishment_entity->phone = $establishment->phone;
        $establishment_entity->cellphone = $establishment->cellphone;
        $establishment_entity->web = $establishment->web;

//        $location = (new LocationHelper())->locationName($establishment_entity->location_id);
//        $establishment_entity->department_name = ($location) ? $location['department_name'] : null;
//        $establishment_entity->province_name = ($location) ? $location['province_name'] : null;
//        $establishment_entity->district_name = ($location) ? $location['district_name'] : null;
//        $establishment_entity->location_name = ($location) ? $location['location_name'] : null;

        return $establishment_entity;
    }
}
