<?php

namespace App\CoreFacturalo;

use App\Models\Tenant\Bank;
use App\Models\Tenant\BankAccount;

class HelperFacturalo
{
    public function xml_item_name($row)
    {
        $name = $row->item->description;
        if(!is_null($row->name_product_pdf) && $row->name_product_pdf !== '') {
            $name .= ' | '.strip_tags($row->name_product_pdf);
        }
        return substr($name, 0, 250);
    }

    public function pdf_item_name($document, $row)
    {
        if($document->data_json) {
            return $row->name_product_pdf;
        }
        $name = $row->item->description;
        if(!is_null($row->name_product_pdf) && $row->name_product_pdf !== '') {
            $name .= '<br/>'.strip_tags($row->name_product_pdf);
        }
        return $name;
    }

    public function bank_accounts_format()
    {
        $banks = Bank::query()->get();
        $data = [];
        foreach ($banks as $bank) {
            $bank_accounts_usd = BankAccount::query()->where('bank_id', $bank->id)->where('currency_type_id', 'USD')->get();
            $bank_accounts_usd_array = [];
            foreach ($bank_accounts_usd as $row) {
                $bank_accounts_usd_array[] = [
                    'number' => $row->number,
                    'cci' => $row->cci,
                ];
            }

            $bank_accounts_pen = BankAccount::query()->where('bank_id', $bank->id)->where('currency_type_id', 'PEN')->get();
            $bank_accounts_pen_array = [];
            foreach ($bank_accounts_pen as $row) {
                $bank_accounts_pen_array[] = [
                    'number' => $row->number,
                    'cci' => $row->cci,
                ];
            }
            if(count($bank_accounts_usd_array) > 0 || count($bank_accounts_pen_array) > 0) {
                $data[] = [
                    'bank_name' => $bank->description,
                    'bank_accounts_usd' => $bank_accounts_usd_array,
                    'bank_accounts_pen' => $bank_accounts_pen_array,
                ];
            }
        }

        return $data;
    }

    public function date_of_issue_format($date)
    {
        $date_format = explode('-',  $date->format('Y-m-d'));
        $month_name = ['01' => 'ENERO',     '02' => 'FEBRERO', '03' => 'MARZO',     '04' => 'ABRIL',
                       '05' => 'MAYO',      '06' => 'JUNIO',   '07' => 'JULIO',     '08' => 'AGOSTO',
                       '09' => 'SETIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE'];

        return $date_format[2].'-'.$month_name[$date_format[1]].'-'.$date_format[0];
    }
}
