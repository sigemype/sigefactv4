<?php

namespace Modules\Report\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $pdf;

    public function __construct($company, $pdf, $excel)
    {
        $this->company = $company;
        $this->pdf = $pdf;
        $this->excel = $excel;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $pdf = $this->getStorage($this->document->filename, 'sale_note');

        return $this->subject('Envio de Reporte de documentos')
                    ->from(config('mail.username'), 'Reporte de documentos')
                    ->view('report::documents.email')
                    ->attachData($this->pdf, 'reporte_documentos'.'.pdf')
                    ->attachData($this->excel, 'reporte_documentos'.'.xlsx');
    }
}