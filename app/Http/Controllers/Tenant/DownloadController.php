<?php
namespace App\Http\Controllers\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Http\Controllers\Controller;
use App\CoreFacturalo\Facturalo;
use App\CoreFacturalo\Template;
use App\Models\Tenant\Company;
use App\Models\Tenant\Dispatch;
use Mpdf\Mpdf;
use Exception;

class DownloadController extends Controller{
    use StorageDocument;
    public function downloadExternal($model, $type, $external_id, $format = null) {
        $model = "App\\Models\\Tenant\\".ucfirst($model);
        $document = $model::where('external_id', $external_id)->first();

        if (!$document) throw new Exception("El código {$external_id} es inválido, no se encontro documento relacionado");

        if ($format != null) $this->reloadPDF($document, 'invoice', $format);

        return $this->download($type, $document);
    }

    public function download($type, $document) {
        switch ($type) {
            case 'pdf':
                $folder = 'pdf';
                break;
            case 'xml':
                $folder = 'signed';
                break;
            case 'cdr':
                $folder = 'cdr';
                break;
            case 'quotation':
                $folder = 'quotation';
                break;
            case 'sale_note':
                $folder = 'sale_note';
                break;

            default:
                throw new Exception('Tipo de archivo a descargar es inválido');
        }

        //borrar despues
        // solo desarrollo
        // $this->reloadPDF($document, 'dispatch', 'a4');
        // $temp = tempnam(sys_get_temp_dir(), 'pdf');
        // file_put_contents($temp, $this->getStorage($document->filename, 'pdf'));

        // return response()->file($temp);
        //borrar antes
        return $this->downloadStorage($document->filename, $folder);
    }

    /**
     * @param      $model
     * @param      $external_id
     * @param null $format
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function toPrint($model, $external_id, $format = null) {
        $document_type = $model;
        $model = "App\\Models\\Tenant\\".ucfirst($model);
        $document = $model::where('external_id', $external_id)->first();

        if (!$document) throw new Exception("El código {$external_id} es inválido, no se encontro documento relacionado");

        if ($format != null) $this->reloadPDF($document, 'invoice', $format);
        if ($document_type == 'dispatch') $this->reloadPDF($document, 'dispatch', 'a4');

        $temp = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($temp, $this->getStorage($document->filename, 'pdf'));

        return response()->file($temp);
    }

    /**
     * Se usa para obtener el pdf individual standar por documento.
     *
     * @param      $model
     * @param      $external_id
     * @param null $format
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|null
     */
    public static function getPdf($model, $external_id, $format = null) {
        $download = new self();
        try {
            return $download->toPrint($model, $external_id, $format);
        } catch (Exception $e) {
            return null;
        }

    }

    /**
     * Reload PDF
     * @param  ModelTenant $document
     * @param  string $format
     * @return void
     */
    private function reloadPDF($document, $type, $format) {
        (new Facturalo)->createPdf($document, $type, $format);
    }
}
