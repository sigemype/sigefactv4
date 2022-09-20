<?php

namespace Modules\Finance\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Hyn\Tenancy\Models\Website;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Hyn\Tenancy\Environment;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Models\Tenant\Company;
use App\Models\Tenant\Establishment;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Modules\Finance\Models\GlobalPayment;
use Modules\Finance\Exports\GlobalPaymentExport;
use Modules\Finance\Http\Controllers\GlobalPaymentController;
use App\Traits\JobReportTrait;


class ProcessGlobalPaymentReport implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, StorageDocument, JobReportTrait;

    public $params;
    public $tray_id;
    public $website_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $params, int $tray_id, int $website_id) 
    {
        $this->params = $params;
        $this->tray_id = $tray_id;
        $this->website_id = $website_id;

        // Log::info("tray_id => " . $this->tray_id);
        // Log::info("website_id => " . $this->website_id);
        // Log::info("data => " . json_encode($this->params));
        // Log::info("data 1 => " . $this->params['establishment_id']);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->showLogInfo("ProcessGlobalPaymentReport Start WebsiteId => {$this->website_id}");

        $website = $this->findWebsite($this->website_id);
        $tenancy = app(Environment::class);
        $tenancy->tenant($website);
        $path = null;


        try {

            $download_tray = $this->findDownloadTray($this->tray_id);
            $format = $download_tray->format;
            $this->showLogInfo("Init process format: {$format}");

            $path = $this->getReportPath($format);
            $filename = $this->getReportFilename($download_tray->module, 'Reporte_Pagos');

            // data
            $records = app(GlobalPaymentController::class)->getRecords($this->params , GlobalPayment::class)->get();
            $company = $this->getDataCompany();
            $establishment = $this->getDataEstablishment($this->params['establishment_id']);


            // procesar reporte
            if($format === 'pdf')
            {
                //@todo revisar formato, genera error
                // ini_set("pcre.backtrack_limit", "50000000");

                // $this->showLogInfo('Render pdf init');
                // $pdf = PDF::loadView('finance::global_payments.report_pdf', compact('records', 'company', 'establishment'))->setPaper('a4', 'landscape');

                // $this->showLogInfo('Upload pdf init');
                // $this->uploadStorage($filename, $pdf->stream($filename.'.'.$format), $path);
                // $this->showLogInfo('Upload pdf finish');
            }
            else
            {
                $this->showLogInfo('Render excel init');
                $global_payment_export = new GlobalPaymentExport();
                $global_payment_export->records($records)->company($company)->establishment($establishment);
                $this->showLogInfo('Render excel finish');
    
                $this->showLogInfo('Upload excel init');
                $global_payment_export->store(DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR . $filename.'.'.$format, 'tenant');
                $this->showLogInfo('Upload excel finish');
            }

            $this->finishedDownloadTray($download_tray, $filename, $path);

        } 
        catch(Exception $e) 
        {
            $this->showLogInfo('ProcessGlobalPaymentReport Error transaction: '. $e->getMessage());
        }

        $this->showLogInfo('ProcessGlobalPaymentReport Finish transaction');
    }


    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Log::error($exception->getMessage());
    }


}
