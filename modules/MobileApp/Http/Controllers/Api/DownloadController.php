<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Http\Controllers\Controller;
use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Tenant\QuotationController;
use App\CoreFacturalo\Template;
use App\Models\Tenant\Company;
use Mpdf\Mpdf;
use Exception;
use Html2Text\Html2Text;
use Illuminate\Http\Request;
use App\Http\Controllers\Tenant\Api\SaleNoteController;


class DownloadController extends Controller
{
    
    /**
     * 
     * Retornar pdf en html
     *
     * @param  string $model
     * @param  string $external_id
     * @param  string $format
     * @return string
     */
    public function documentPrintPdf($model, $external_id, $format, $extend_pdf_height = 0) 
    {
        $path_model = "App\\Models\\Tenant\\".ucfirst($model);
        $document = $path_model::where('external_id', $external_id)->first();

        if (!$document) throw new Exception("El código {$external_id} es inválido, no se encontro documento relacionado");

        $html = $this->getHtmlPdf($model, $document, $format);

        $this->replaceElementsInHtml($html, $format, $extend_pdf_height);

        return $html;
    }

    
    /**
     * 
     * Reemplazar ancho en formato pdf - altura adicional para ticket (impresion directa app)
     *
     * @param  string $html
     * @param  string $format
     * @param  float $extend_pdf_height
     * @return void
     */
    private function replaceElementsInHtml(&$html, $format, $extend_pdf_height)
    {
        // se reemplaza ancho para impresion desde app para tickets
        $size_width = $this->getSizeWidth($format);

        if($size_width)
        {
            $search_key = '<style>';
            $replace_size = "{$search_key} @media print { .page, .page-content, html, body, .framework7-root, .views, .view { height: auto !important; width: {$size_width}mm !important;}}";
    
            $html = str_replace($search_key, $replace_size, $html);
        }

        // se agrega un div para aumentar la altura del pdf, se utiliza para impresion directa desde app
        if($extend_pdf_height > 0)
        {
            $search_key_extend = '</body>';
            $replace_size_extend = "<div style='height:".$extend_pdf_height."px'></div>{$search_key_extend}";
    
            $html = str_replace($search_key_extend, $replace_size_extend, $html);
        }
    }


    /**
     * 
     * Obtener medida del formato ticket para asignar el valor a la impresión
     *
     * @param  string $format
     * @return float
     */
    public function getSizeWidth($format)
    {
        $size_width = null;

        switch ($format) 
        {
            case 'ticket_50':
                $size_width = 45;
                break;
            
            case 'ticket_58':
                $size_width = 56;
                break;

            case 'ticket':
                $size_width = 78;
                break;
        }

        return $size_width;
    }


    /**
     * 
     * Reload Ticket
     * 
     * @param  string $document
     * @param  string $format
     * @return string
     */
    private function getHtmlPdf($model, $document, $format) 
    {
        $html = null;

        if($model === 'document')
        {
            $html = (new Facturalo)->createPdf($document, 'invoice', $format, 'html');
        }
        else
        {
            $html = app(SaleNoteController::class)->createPdf($document, $format, null, 'html');
        }

        return $html;
    }

    
    /**
     * 
     * Retornar pdf en texto
     *
     * @param  string $model
     * @param  string $external_id
     * @param  string $format
     * @return string
     */
    public function documentPrintText($model, $external_id, $format) 
    {
        $html = $this->documentPrintPdf($model, $external_id, $format);

        return trim((new Html2Text($html))->getText());
    }

        
    /**
     * Usado para pruebas
     *
     * @param  Request $request
     * @return void
     */
    // public function documentPrintPdfUpload(Request $request) 
    // {

    //     $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));

    //     file_put_contents(public_path('logo'.DIRECTORY_SEPARATOR.$request->external_id.".png"), $data);

    //     return [
    //         'successs' => true
    //     ];
        
    // }


}
