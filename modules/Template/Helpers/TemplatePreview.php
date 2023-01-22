<?php

namespace Modules\Template\Helpers;

use Modules\Sale\Helpers\SaleStore;
use Modules\Sale\Models\Sale;

class TemplatePreview
{
    public function generate($data)
    {
        $template_id = $data['template_id'];
        $color_line = $data['color_line'];
        $background_head = $data['background_head'];
        $color_head_text = $data['color_head_text'];

        $sale = Sale::query()->first();
        $sale_store = new SaleStore();
        $document = $sale_store->createFacturalo($sale->id);
        $sale_store->createPdf($document);

//
//        $company_main = company_active();
//        $establishment_data = \Modules\Configurations\Models\Establishment::query()->with('configuration')->where('is_main', true)->first();
//
//        $print_footer_text = '';
//
//        if($document_type === 'sale') {
//
//            $items = [];
//            $template_item = new Item();
//            $template_item->internal_id = 'P0001';
//            $template_item->name = 'AAAAAAAAAAAAAAAAAAAAAA';
//            $template_item->description = [];
//            $template_item->attributes = [];
//            $template_item->unit_type_name = 'NIU';
//            $template_item->quantity = 1;
//            $template_item->unit_value = '100.00';
//            $template_item->unit_price = '118.00';
//            $template_item->total = '236.00';
//            $items[] = $template_item;
//
//            $template_item = new Item();
//            $template_item->internal_id = 'P0002';
//            $template_item->name = 'bbbbbbbbbbbbbbbbbbbbbbbbbb';
//            $template_item->description = [];
//            $template_item->attributes = [];
//            $template_item->unit_type_name = 'NIU';
//            $template_item->quantity = 2;
//            $template_item->unit_value = '200.00';
//            $template_item->unit_price = '236.00';
//            $template_item->total = '472.00';
//            $items[] = $template_item;
//
//            $company = new Company();
//            $company->name = $company_main->name;
//            $company->number = $company_main->number;
//            $company->trade_name = $company_main->trade_name;
//
//            $establishment = new Establishment();
//            $establishment->logo = '<img src="'.asset('images/logo_tufactura.png').'" class="logo"/>';
//            $establishment->main_address = $establishment_data->address;
//            $establishment->main_location = get_location_name_full($establishment_data->location_id);
//            $establishment->email = $establishment_data->email;
//            $establishment->web = $establishment_data->web;
//            $establishment->phone = $establishment_data->phone;
//            $establishment->cellphone = $establishment_data->cellphone;
//            $establishment->print_header_text = $establishment_data->configuration->print_header_text;
//            $establishment->print_footer_text = $establishment_data->configuration->print_footer_text;
//
//            $print_footer_text = $establishment_data->configuration->print_footer_text;
//
//
//            $person = new Person();
//            $person->name = 'IMPORTACION & EXPORTACION YU HUA S.A.C.';
//            $person->number = '20392962761';
//            $person->identity_document_type_name = 'RUC';
//
//            $person_address = new PersonAddress();
//            $person_address->address = 'JR. PARURO Nro. 745 BARRIOS ALTOS';
//            $person_address->location_name = 'LIMA - LIMA - LIMA';
//
//
//            $sale = new Invoice();
//            $sale->company = $company;
//            $sale->establishment = $establishment;
//            $sale->document_type_name = 'FACTURA ELECTRÓNICA';
//            $sale->number = 'F001-00000001';
//            $sale->date_of_issue = date('d/m/Y');
//            $sale->date_of_due = date('d/m/Y');
//            $sale->currency_type_symbol = 'S/';
//            $sale->currency_type_name = 'SOL';
//            $sale->guides = 'T001-1124456<br/>T001-1797456';
//            $sale->seller_name = 'JUAN';
//            $sale->payment_condition_name = 'CONTADO';
//            $sale->purchase_order = 'C001-124657';
//            $sale->person = $person;
//            $sale->person_address = $person_address;
//
//            $sale->total_to_letter = 'SON: MIL CIENTO OCHENTA CON 00/100 SOLES';
//            $sale->qr_image = '<img src="'.asset('images/qr.jpg').'" class="qr"/>';
//            $sale->hash = 'FjBdyLmiPhvdtPBgaTVtl3OdP18';
//
//            $sale->total_taxed = '1000.00';
//            $sale->total_igv = '180.00';
//            $sale->total = '1180.00';
//
//            $sale->observations = ['1. Observación 1', '2. Observación 2', '3. Observación 3'];
//            $sale->bank_accounts = 'BCP S/ 3432 423 / CCI: 4 23432<br/>BBVA S/ 757567567 / CCI: 7657567567567';
//
//            $sale->has_detraction = true;
//            $sale->detraction_message = 'SUJETO A DETRACCIÓN';
//            $sale->detraction_account_number = '1324564 23465465446 465 4645456';
//
//            $sale->items = $items;
//
//            $template_data = view('templates::pdf.a4_template_document_sale_1', ['company_configuration' => $company_main->configuration,
//                'sale' => $sale,
//                'template_id' => $template_id])->render();
//        }

        $html = view('templates::pdf.a4_template', ['template_data' => $template_data,
            'filename' => 'DEMO',
            'color_line' => $color_line,
            'background_head' => $background_head,
            'color_head_text' => $color_head_text])->render();

        $environment = environment_active();
        $url_cpe = (is_null($environment->url_cpe) || $environment->url_cpe === '') ? 'buscar' : $environment->url_cpe;

        $footer_text_1 = 'Representación impresa de la FACTURA ELECTRÓNICA';
        $footer_text_2 = 'Para consultar el comprobante ingresar a ' . $url_cpe;


        $pdf = new Pdf([
            'no-outline',
            'encoding' => 'UTF-8',
            'margin-top' => '1cm',
            'margin-right' => '1cm',
            'margin-bottom' => '1cm',
            'margin-left' => '1cm',
            'print-media-type',
            'dpi' => 200,
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',
            'user-style-sheet' => public_path('css/template.css'),
            'footer-html' => view('templates::pdf.footers.a4_template_footer', ['print_footer_text' => $print_footer_text,
                'footer_text_1' => $footer_text_1,
                'footer_text_2' => $footer_text_2])->render(),
            'javascript-delay' => 500,
        ]);

        $pdf->addPage($html);

        if (is_windows()) {
            $pdf->binary = public_path('vendor/wkhtmltopdf.exe');
        }

        $result = $pdf->toString();

        if (!$result) {
            return [
                'success' => false,
                'message' => $pdf->getError(),
            ];
        }

//        $temp = tempnam(asset('temp'), 'pdf');
        $file_name = 'temp/pdf_' . date('Ymdhis') . '.pdf';
        //$temp_file = public_path($file_name);
        file_put_contents(public_path($file_name), $result);

        return asset($file_name);
    }
}
