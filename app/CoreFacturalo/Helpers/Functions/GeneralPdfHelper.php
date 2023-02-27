<?php

namespace App\CoreFacturalo\Helpers\Functions;

use App\CoreFacturalo\Template;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use App\Models\Tenant\PaymentMethodType;


class GeneralPdfHelper
{

    /**
     *
     * Retornar archivo temporal para vista previa
     *
     * @param  string $temp_folder
     * @param  string $file_content
     * @return mixed
     */
    public static function getPreviewTempPdf($temp_folder, $file_content)
    {
        $temp = tempnam(sys_get_temp_dir(), $temp_folder);

        file_put_contents($temp, $file_content);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="file.pdf"'
        ];

        return response()->file($temp, $headers);
    }


    /**
     *
     * Crear pdf general
     * Disponible: A4
     *
     * @param  string $document_type
     * @param  mixed $document
     * @param  string $format_pdf
     * @param  string $filename
     * @return mixed
     */
    public static function getBasicPdf($document_type, $document, $format_pdf = 'a4', $filename = null)
    {

        ini_set("pcre.backtrack_limit", "5000000");
        $template = new Template();
        $pdf = new Mpdf();

        $company = Company::active();
        $filename = $filename ?? $document->filename;

        $base_template = Establishment::find($document->establishment_id)->template_pdf;

        $html = $template->pdf($base_template, $document_type, $company, $document, $format_pdf);

        $pdf_font_regular = config('tenant.pdf_name_regular');
        $pdf_font_bold = config('tenant.pdf_name_bold');

        if ($pdf_font_regular != false)
        {

            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $default = [
                'fontDir' => array_merge($fontDirs, [
                    app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.
                                                DIRECTORY_SEPARATOR.'pdf'.
                                                DIRECTORY_SEPARATOR.$base_template.
                                                DIRECTORY_SEPARATOR.'font')
                ]),
                'fontdata' => $fontData + [
                    'custom_bold' => [
                        'R' => $pdf_font_bold.'.ttf',
                    ],
                    'custom_regular' => [
                        'R' => $pdf_font_regular.'.ttf',
                    ],
                ]
            ];


            $pdf = new Mpdf($default);
        }

        $path_css = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.
                                             DIRECTORY_SEPARATOR.'pdf'.
                                             DIRECTORY_SEPARATOR.$base_template.
                                             DIRECTORY_SEPARATOR.'style.css');

        $stylesheet = file_get_contents($path_css);

        $pdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

        $pdf->WriteHTML($html, HTMLParserMode::HTML_BODY);

        return $pdf->output('', 'S');

    }


    /**
     *
     * Retornar nombre de archivo
     *
     * @param  int|string $number
     * @param  int $id
     * @return string
     */
    public static function getNumberIdFilename($id, $number = null)
    {
        $data = $number ? [ $number, $id, date('Ymd') ] : [ $id, date('Ymd') ];

        return join('-', $data);
    }

    
    /**
     * 
     * @param  string $filename
     * @return array
     */
    public static function pdfResponseFileHeaders($filename)
    {
        return [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'.pdf'.'"'
        ];
    }

    
    /**
     *
     * Retornar archivo temporal para vista previa con nombre de archivo
     *
     * @param  string $temp_folder
     * @param  string $filename
     * @param  string $file_content
     * @return mixed
     * 
     */
    public static function getPreviewTempPdfWithFilename($temp_folder, $filename, $file_content)
    {
        $temp = tempnam(sys_get_temp_dir(), $temp_folder);

        file_put_contents($temp, $file_content);

        return response()->file($temp, self::pdfResponseFileHeaders($filename));
    }

        
    /**
     *
     * @param  float $value
     * @param  int $decimals
     * @return float
     */
    public static function setNumberFormat($value, $decimals = 2)
    {
        return number_format($value, $decimals, ".", "");
    }

    
    /**
     *
     * @param  string $id
     * @return string
     */
    public static function getPaymentMethodTypeDescription($id)
    {
        $record = PaymentMethodType::select('description')->find($id);

        if($record) return mb_strtoupper($record->description);

        return $id;
    }

}
