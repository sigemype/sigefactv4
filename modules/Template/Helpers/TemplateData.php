<?php

namespace Modules\Template\Helpers;

use Modules\Catalog\Helpers\LocationHelper;
use Modules\Facturalo\Models\Company\Company;
use Modules\Facturalo\Models\Company\Establishment;
use Modules\Facturalo\Models\Document\DocumentItem;
use Modules\Facturalo\Models\Person\Person;

class TemplateData
{
    public function getDataLogo($model)
    {
        $company = \Modules\Company\Models\Company::query()->first();
        $model->logo_url = get_url_logo($company->logo);
        $model->logo_public_path = get_public_path_logo($company->logo);

        return $model;
    }

    public function getDataEstablishmentConfiguration($model, $record)
    {
        $model->print_header_text = $record->establishment->configuration->print_header_text;
        $model->print_footer_text = $record->establishment->configuration->print_footer_text;
        $model->printing_format = $record->establishment->configuration->printing_format;

        return $model;
    }

    public function getDataCompany($record)
    {
        $company_data = $record->document->company_data;
        $company = new Company();
        $company->number = $company_data['number'];
        $company->name = $company_data['name'];
        $company->trade_name = $company_data['trade_name'];

        return $company;
    }

    public function getDataEstablishment($record)
    {
        $establishment_data = $record->document->establishment_data;
        $establishment = new Establishment();
        $establishment->code = key_exists('code', $establishment_data) ? $establishment_data['code'] : null;
        $establishment->address = key_exists('address', $establishment_data) ? $establishment_data['address'] : null;
        $establishment->location_id = key_exists('location_id', $establishment_data) ? $establishment_data['location_id'] : null;
        $establishment->country_id = key_exists('country_id', $establishment_data) ? $establishment_data['country_id'] : null;
        $establishment->email = key_exists('email', $establishment_data) ? $establishment_data['email'] : null;
        $establishment->phone = key_exists('phone', $establishment_data) ? $establishment_data['phone'] : null;
        $establishment->cellphone = key_exists('cellphone', $establishment_data) ? $establishment_data['cellphone'] : null;
        $establishment->web = key_exists('web', $establishment_data) ? $establishment_data['web'] : null;

        $location = (new LocationHelper())->locationName($establishment->location_id);
        $establishment->department_name = ($location) ? $location['department_name'] : null;
        $establishment->province_name = ($location) ? $location['province_name'] : null;
        $establishment->district_name = ($location) ? $location['district_name'] : null;
        $establishment->location_name = ($location) ? $location['location_name'] : null;

        return $establishment;
    }

    public function getDataEstablishmentMain($record)
    {
        $establishment_main_data = $record->document->establishment_main_data;
        if ($establishment_main_data) {
            $establishment = new Establishment();
            $establishment->code = key_exists('code', $establishment_main_data) ? $establishment_main_data['code'] : null;
            $establishment->address = key_exists('address', $establishment_main_data) ? $establishment_main_data['address'] : null;
            $establishment->location_id = key_exists('location_id', $establishment_main_data) ? $establishment_main_data['location_id'] : null;
            $establishment->country_id = key_exists('country_id', $establishment_main_data) ? $establishment_main_data['country_id'] : null;
            $establishment->email = key_exists('email', $establishment_main_data) ? $establishment_main_data['email'] : null;
            $establishment->phone = key_exists('phone', $establishment_main_data) ? $establishment_main_data['phone'] : null;
            $establishment->cellphone = key_exists('cellphone', $establishment_main_data) ? $establishment_main_data['cellphone'] : null;
            $establishment->web = key_exists('web', $establishment_main_data) ? $establishment_main_data['web'] : null;

            $location = (new LocationHelper())->locationName($establishment->location_id);
            $establishment->department_name = ($location) ? $location['department_name'] : null;
            $establishment->province_name = ($location) ? $location['province_name'] : null;
            $establishment->district_name = ($location) ? $location['district_name'] : null;
            $establishment->location_name = ($location) ? $location['location_name'] : null;

            return $establishment;
        }

        return null;
    }

    public function getDataPerson($record)
    {
        $person_data = $record->document->person_data;
        $person_address_data = ($record->document->person_address_data) ?: [];
        $person = new Person();
        $person->identity_document_type_id = $person_data['identity_document_type_id'];
        $person->identity_document_type_name = $record->person->identity_document_type->name;
        $person->number = $person_data['number'];
        $person->name = $person_data['name'];
        $person->trade_name = key_exists('trade_name', $person_data) ? $person_data['trade_name'] : null;
        $person->phone = key_exists('phone', $person_data) ? $person_data['phone'] : null;
        $person->email = key_exists('email', $person_data) ? $person_data['email'] : null;

        $person->address = key_exists('address', $person_address_data) ? $person_address_data['address'] : null;
        $person->location_id = key_exists('location_id', $person_address_data) ? $person_address_data['location_id'] : null;
        $person->country_id = key_exists('country_id', $person_address_data) ? $person_address_data['country_id'] : null;

        $location = (new LocationHelper())->locationName($person->location_id);
        $person->location_name = $location ? $location['location_name'] : null;

        return $person;
    }

    public function getDataDocument($model, $record)
    {
        $model->subtotal = $record->document->subtotal;
        $model->total_prepayment = $record->document->total_prepayment;
        $model->total_charge = $record->document->total_charge;
        $model->total_discount = $record->document->total_discount;
        $model->total_exportation = $record->document->total_exportation;
        $model->total_free = $record->document->total_free;
        $model->total_taxed = $record->document->total_taxed;
        $model->total_unaffected = $record->document->total_unaffected;
        $model->total_exonerated = $record->document->total_exonerated;
        $model->total_igv = $record->document->total_igv;
        $model->total_igv_free = $record->document->total_igv_free;
        $model->total_base_isc = $record->document->total_base_isc;
        $model->total_isc = $record->document->total_isc;
        $model->total_base_other_taxes = $record->document->total_base_other_taxes;
        $model->total_other_taxes = $record->document->total_other_taxes;
        $model->total_icbper = $record->document->total_icbper;
        $model->total_taxes = $record->document->total_taxes;
        $model->total_value = $record->document->total_value;
        $model->total_price = $record->document->total_price;
        $model->rounding = $record->document->rounding;
        $model->total = $record->document->total;
        $model->observations = $record->document->observations;

        return $model;
    }

    public function getDataDocumentItems($model)
    {
        $items = [];
        foreach ($model->document->items as $item) {
            $item_data = $item->item_data;

            $document_item = new DocumentItem();
            $document_item->name = $item_data['name'];
            $document_item->description = key_exists('description', $item_data) ? $item_data['description'] : null;
            $document_item->internal_id = key_exists('internal_id', $item_data) ? $item_data['internal_id'] : null;
            $document_item->item_code = key_exists('item_code', $item_data) ? $item_data['item_code'] : null;
            $document_item->item_code_gs1 = key_exists('item_code_gs1', $item_data) ? $item_data['item_code_gs1'] : null;
            $document_item->brand_name = key_exists('brand_name', $item_data) ? $item_data['brand_name'] : null;
            $document_item->unit_type_id = $item->unit_type_id;
            $document_item->unit_type_name = str_to_upper_utf8($item->unit_type->name);
            $document_item->presentation_name = str_to_upper_utf8($item->presentation_name);
            $document_item->quantity = $item->quantity;
            $document_item->quantity_base = $item->quantity_base;
            $document_item->unit_value = $item->unit_value;
            $document_item->price_type_id = $item->price_type_id;
            $document_item->unit_price = $item->unit_price;
            $document_item->subtotal = $item->subtotal;
            $document_item->affectation_igv_type_id = $item->affectation_igv_type_id;
            $document_item->total_base_igv = $item->total_base_igv;
            $document_item->percentage_igv = $item->percentage_igv;
            $document_item->total_igv = $item->total_igv;
            $document_item->system_isc_type_id = $item->system_isc_type_id;
            $document_item->total_base_isc = $item->total_base_isc;
            $document_item->percentage_isc = $item->percentage_isc;
            $document_item->total_isc = $item->total_isc;
            $document_item->total_base_other_taxes = $item->total_base_other_taxes;
            $document_item->percentage_other_taxes = $item->percentage_other_taxes;
            $document_item->total_other_taxes = $item->total_other_taxes;
            $document_item->factor_icbper = $item->factor_icbper;
            $document_item->total_icbper = $item->total_icbper;
            $document_item->total_taxes = $item->total_taxes;
            $document_item->total_value = $item->total_value;
            $document_item->total_charge = $item->total_charge;
            $document_item->total_discount = $item->total_discount;
            $document_item->total = $item->total;
            $items[] = $document_item;
        }

        return $items;
    }
}
