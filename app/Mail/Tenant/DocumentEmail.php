<?php

namespace App\Mail\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\WS\Services\BaseSunat;
use App\Models\Tenant\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;

class DocumentEmail extends Mailable
{
    use Queueable;
    use SerializesModels;
    use StorageDocument;

    public $company;
    public $document;

    public function __construct($company, $document)
    {
        $this->company = $company;
        $this->document = $document;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->document->filename, 'pdf');
        $xml = $this->getStorage($this->document->filename, 'signed');
        $cdr = null;

        if($this->document->document_type_id !== '03') {

            if($this->existFileInStorage($this->document->filename, 'cdr'))
            {
                $cdr = $this->getStorage($this->document->filename, 'cdr');
            }

        }


        $image_detraction = ($this->document->detraction) ? (($this->document->detraction->image_pay_constancy) ? storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'image_detractions'.DIRECTORY_SEPARATOR.$this->document->detraction->image_pay_constancy):false):false;

        $template_document_mail = config('tenant.template_document_mail');
        if($template_document_mail === 'default') {
            $template_document_mail_view = 'tenant.templates.email.document';
            $subject = 'Envio de Comprobante de Pago Electrónico';
        } else {
            $template_document_mail_view = 'tenant.templates.email.'.$template_document_mail;
            $subject = 'Folio '.$this->document->folio;
        }

        $email = $this->subject($subject)
                    ->from(config('mail.username'), 'Comprobante electrónico')
                    ->view($template_document_mail_view)
                    ->attachData($pdf, $this->document->filename.'.pdf')
                    ->attachData($xml, $this->document->filename.'.xml');


        // $file = $this->getCdr($this->document);

        if(!empty($cdr) ){
            $xml_cdr = (new BaseSunat())->getXmlResponse($cdr);
//            $email->attachData($cdr, $this->document->filename.'.zip');
            $email->attachData($xml_cdr, 'R-'.$this->document->filename.'.xml');
        }

        if($image_detraction){
            return $email->attachData(File::get($image_detraction), $this->document->detraction->image_pay_constancy);
        }

        return $email;
    }

    public function getCdr($document){
        $file = null;
        if( !empty($document->external_id)) {
            $file = $this->getStorage($document->filename, 'cdr');
        }
        return $file;

    }
}
