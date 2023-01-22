<?php

namespace Modules\Store\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    protected $root;

    public function setFolder($soap_type_id, $folder)
    {
        $root = ($soap_type_id === '01') ? 'demo' : 'production';
        $this->root = $root . DIRECTORY_SEPARATOR . $folder;
    }

    /*
     * Payments
     */
    public function uploadPayment($filename, $file_content)
    {
        Storage::disk('tenant')->put('payments' . DIRECTORY_SEPARATOR . $filename, $file_content);
    }

    public function downloadPayment($filename)
    {
        return Storage::disk('tenant')->download('payments' . DIRECTORY_SEPARATOR . $filename);
    }

    /*
     * Certificate
     */
    public function uploadCertificate($filename, $file_content)
    {
        Storage::disk('tenant')->put('certificates' . DIRECTORY_SEPARATOR . $filename, $file_content);
    }

    public function getCertificate($filename)
    {
        return Storage::disk('tenant')->get('certificates' . DIRECTORY_SEPARATOR . $filename);
    }

    public function getCertificateDemo()
    {
        return file_get_contents(__DIR__ . '/../../Facturalo/Resources/certificate.pem');
    }

    public function uploadXmlSigned2($filename, $file_content)
    {
        $folder = $this->getFolder('signed', $filename);
        Storage::disk('tenant')->put($folder, $file_content);
    }

    /*
     * Xml Signed
     */
    public function uploadXmlSigned($filename, $file_content)
    {
        $folder = $this->getFolder('signed', $filename . '.xml');
        Storage::disk('tenant')->put($folder, $file_content);
    }

    public function downloadXmlSigned($filename)
    {
        $folder = $this->getFolder('signed', $filename . '.xml');
        return Storage::disk('tenant')->download($folder);
    }

    public function getXmlSigned($filename)
    {
        $folder = $this->getFolder('signed', $filename . '.xml');
        return Storage::disk('tenant')->get($folder);
    }

    /*
     * Pdf
     */
    public function uploadPdf($filename, $file_content, $format = 'a4')
    {
        $folder = $this->getFolder('pdf_' . $format, $filename . '.pdf');
        Storage::disk('tenant')->put($folder, $file_content);
    }

    public function downloadPdf($filename, $format = 'a4')
    {
        $folder = $this->getFolder('pdf_' . $format, $filename . '.pdf');
        return Storage::disk('tenant')->download($folder);
    }

    public function getPdf($filename, $format = 'a4')
    {
        $folder = $this->getFolder('pdf_' . $format, $filename . '.pdf');
        return Storage::disk('tenant')->get($folder);
    }

    public function existPdf($filename, $format = 'a4')
    {
        $folder = $this->getFolder('pdf_' . $format, $filename . '.pdf');
        return Storage::disk('tenant')->exists($folder);
    }

    public function printer($filename, $format = 'a4')
    {
        $temp = tempnam(sys_get_temp_dir(), 'pdf_' . $format);
        file_put_contents($temp, $this->getPdf($filename));

        return response()->file($temp);
    }

    /*
     * Cdr
     */
    public function uploadCdr($filename, $file_content)
    {
        $folder = $this->getFolder('cdr', 'R-' . $filename . '.xml');
        Storage::disk('tenant')->put($folder, $file_content);
    }

    public function downloadCdr($filename)
    {
        $folder = $this->getFolder('cdr', 'R-' . $filename . '.xml');
        return Storage::disk('tenant')->download($folder);
    }

    public function getCdr($filename)
    {
        $folder = $this->getFolder('cdr', 'R-' . $filename . '.xml');
        return Storage::disk('tenant')->get($folder);
    }

    /*
     * General
     */
    private function getFolder($type, $filename)
    {
        return $this->root . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $filename;
    }


    /*
     * Html
     */
    public function uploadHtml($filename, $file_content)
    {
        $folder = $this->getFolder('html', $filename . '.html');
        Storage::disk('tenant')->put($folder, $file_content);
    }

    public function downloadHtml($filename)
    {
        $folder = $this->getFolder('html', $filename . '.html');
        return Storage::disk('tenant')->download($folder);
    }

    public function getHtml($filename)
    {
        $folder = $this->getFolder('html', $filename . '.html');
        return Storage::disk('tenant')->get($folder);
    }

    public function existHtml($filename)
    {
        $folder = $this->getFolder('html', $filename . '.html');
        return Storage::disk('tenant')->exists($folder);
    }
}
