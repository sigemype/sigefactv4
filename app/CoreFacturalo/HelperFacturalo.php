<?php

namespace App\CoreFacturalo;

use App\Models\Tenant\Bank;
use App\Models\Tenant\BankAccount;
use Mpdf\HTMLParserMode;


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

    
    /**
     * 
     * Validar si se agrega ticket de despacho al pdf
     *
     * @param  string $format_pdf
     * @param  string $type
     * @param  Document|SaleNote $document
     * @return bool
     */
    public function isAllowedAddDispatchTicket($format_pdf, $type, $document)
    {
        return in_array($format_pdf, ['ticket', 'ticket_58', 'ticket_50']) && in_array($type, ['invoice', 'sale-note']) && (bool) $document->dispatch_ticket_pdf;
    }

        
    /**
     * 
     * Agregar ticket de despacho al pdf
     *
     * @param  Mpdf $pdf
     * @param  Company $company
     * @param  Document|SaleNote $document
     * @param  array $additional_data
     * @return void
     */
    public function addDocumentDispatchTicket(&$pdf, $company, $document, $additional_data)
    {
        list($template, $base_pdf_template, $width, $calculated_height) = $additional_data;

        $html_dispatch_ticket = $template->pdfWithoutFormat($base_pdf_template, 'document_dispatch_ticket', $company, $document);
        $additional = 0;
        $base_height = 40;

        if($document->reference_data) $additional += 10;

        $pdf->AddPageByArray([
            'orientation' => 'P',
            'newformat' => [
                $width,
                $base_height + $calculated_height + $additional,
            ],
            'mgt' => 0,
            'mgr' => 1,
            'mgb' => 0,
            'mgl' => 1
        ]);
        
        $pdf->writeHTML($html_dispatch_ticket, HTMLParserMode::HTML_BODY);
    }

}
