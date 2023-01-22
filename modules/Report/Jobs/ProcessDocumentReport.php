<?php

    namespace Modules\Report\Jobs;


    use Illuminate\Bus\Queueable;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Hyn\Tenancy\Models\Website;
    use Illuminate\Support\Facades\Log;
    use Exception;
    use App\Models\Tenant\DownloadTray;
    use Hyn\Tenancy\Environment;
    use App\CoreFacturalo\Helpers\Storage\StorageDocument;
    use Modules\Inventory\Exports\InventoryExport;
    use Barryvdh\DomPDF\Facade as PDF;
    use Modules\Inventory\Models\ItemWarehouse;
    use Mpdf\HTMLParserMode;
    use Mpdf\Mpdf;
    use Mpdf\Config\ConfigVariables;
    use Mpdf\Config\FontVariables;
    use Modules\Report\Exports\DocumentExport;
    use App\Traits\JobReportTrait;
    use Modules\Report\Http\Controllers\ReportDocumentController;
    

    class ProcessDocumentReport implements ShouldQueue
    {
        use Dispatchable;
        use InteractsWithQueue;
        use Queueable;
        use SerializesModels;
        use JobReportTrait;
        use StorageDocument;

        public $tray_id;
        public $columns;
        public $filters;
        public $website_id;


        /**
         * Create a new job instance.
         *
         * @return void
         */
        public function __construct(int $tray_id, int $website_id, array $filters, $columns)
        {
            $this->website_id = $website_id;
            $this->tray_id = $tray_id;
            $this->filters = $filters;
            $this->columns = $columns;
        }


        /**
         * Execute the job.
         *
         * @return void
         */
        public function handle()
        {
            $this->showLogInfo("ProcessDocumentReport Start WebsiteId => {$this->website_id}");

            $website = $this->findWebsite($this->website_id);
            $tenancy = app(Environment::class);
            $tenancy->tenant($website);
            
            try 
            {
                $download_tray = $this->findDownloadTray($this->tray_id);
                $format = $download_tray->format;
                $path = $this->getReportPath($format);
                $filename = $this->getReportFilename($download_tray->module, 'Reporte_Documentos_Ventas');
                $company = $this->getDataCompany();
                $establishment = $this->getDataEstablishment($this->filters['establishment_id_for_format']);
                
                $records = app(ReportDocumentController::class)->getRecords($this->filters, $this->filters['class_type_records'])->get();

                if ($format === 'pdf') 
                {
                    ini_set("pcre.backtrack_limit", "50000000");

                    Log::debug("Render pdf init");

                    $filters = $this->filters;
                    $columns = $this->columns;
                    $html = view('report::documents.report_pdf', compact("records", "company", "establishment", "filters","columns"))->render();
                    $html = htmlspecialchars_decode($html);
                    $base_template = $establishment->template_pdf;
                    $defaultConfig = (new ConfigVariables())->getDefaults();
                    $fontDirs = $defaultConfig['fontDir'];
                    $defaultFontConfig = (new FontVariables())->getDefaults();
                    $fontData = $defaultFontConfig['fontdata'];
                    $pdf_font_regular = config('tenant.pdf_name_regular');
                    $pdf_font_bold = config('tenant.pdf_name_bold');
    
                    $pdf = new Mpdf([
                        'format' => 'A4-L',
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
                    ]);

                    $path_css = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.
                                            DIRECTORY_SEPARATOR.'pdf'.
                                            DIRECTORY_SEPARATOR.'default'.
                                            DIRECTORY_SEPARATOR.'style.css');
                    
                    $stylesheet = file_get_contents($path_css);
                    
                    $pdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

                    $pdf->WriteHTML($html, HTMLParserMode::HTML_BODY);
        
                    Log::debug("Render pdf finish");


                    Log::debug("Upload pdf init");
                    
                    $this->uploadStorage($filename, $pdf->output('', 'S'), $path);

                    Log::debug("Upload pdf finish");
                    

                } 
                else 
                {
                    Log::debug("Render excel init");

                    //get categories
                    $categories = [];
                    $categories_services = [];

                    if($this->filters['include_categories'] == "1")
                    {
                        $categories = app(ReportDocumentController::class)->getCategories($records, false);
                        $categories_services = app(ReportDocumentController::class)->getCategories($records, true);
                    }

                    $inventoryExport = new DocumentExport();

                    $inventoryExport
                        ->records($records)
                        ->company($company)
                        ->establishment($establishment)
                        ->filters($this->filters)
                        ->categories($categories)
                        ->categories_services($categories_services)
                        ->columns($this->columns);

                    Log::debug("Render excel finish");

                    Log::debug("Upload excel init");

                    $inventoryExport->store(DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$filename.'.'.$format, 'tenant');

                    Log::debug("Upload excel finish");

                }

                $this->finishedDownloadTray($download_tray, $filename, $path);

            } 
            catch(Exception $e) 
            {
                $this->showLogInfo('ProcessDocumentReport Error transaction: '. $e->getMessage());
            }

            $this->showLogInfo('ProcessDocumentReport Finish transaction');
        }

        
        /**
         * The job failed to process.
         *
         * @param Exception $exception
         *
         * @return void
         */
        public function failed(Exception $exception)
        {
            Log::error($exception->getMessage());
        }
    }
