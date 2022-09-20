<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\Cash;
use App\Models\Tenant\BankAccount;
use App\Models\Tenant\Company;
use Modules\Finance\Traits\FinanceTrait;
use Modules\Finance\Http\Resources\GlobalPaymentCollection;
use Modules\Finance\Exports\GlobalPaymentExport;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Tenant\Establishment;
use Carbon\Carbon;
use Modules\Finance\Jobs\ProcessGlobalPaymentReport;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\JobReportTrait;

/**
 * Class GlobalPaymentController
 *
 * @package Modules\Finance\Http\Controllers
 * @mixin Controller
 */
class GlobalPaymentController extends Controller
{

    use FinanceTrait, JobReportTrait;

    public function index(){

        return view('finance::global_payments.index');
    }


    public function filter(){

        $payment_types = $this->getCollectionPaymentTypes();
        $destination_types = $this->getCollectionDestinationTypes();

        return compact('payment_types', 'destination_types');
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Modules\Finance\Http\Resources\GlobalPaymentCollection
     */
    public function records(Request $request)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $records */
        $records = $this->getRecords($request->all(), GlobalPayment::class);
        
        return new GlobalPaymentCollection($records->paginate(config('tenant.items_per_page')));
    }

    
    /**
     * 
     * Obtener registros
     *
     * Usado en:
     * ProcessGlobalPaymentReport
     * records
     * 
     * @param  Request $request
     * @param  GlobalPayment $model
     * @return Builder
     */
    public function getRecords($request, $model)
    {
        $data_of_period = $this->getDatesOfPeriod($request);
        $payment_type = $request['payment_type'];
        $destination_type = $request['destination_type'];
        $id = 0;
        $position = strpos($destination_type, BankAccount::class.'::');
        if ($position !== false) {
            $id = str_replace(BankAccount::class.'::','',$destination_type);
            $destination_type = BankAccount::class;
        }

        $params = (object)[
            'date_start' => $data_of_period['d_start'],
            'date_end' => $data_of_period['d_end'],
            'user_id' => $request['user_id'] ?? null,
        ];

        // $records = $model::whereFilterPaymentType($params);
        $records = $model::whereFilterReportPayments($params); //filtros optimizados

        if($payment_type){
            $records = $records->whereDefinePaymentType($payment_type);
        }

        if($destination_type){
            $records = $records->whereDestinationType($destination_type);
        }
        if($id !== 0 && $model === GlobalPayment::class){
            $records->where('destination_id',$id);
        }

        return $records->latest();
    }


    /**
     * 
     * Generar reporte pdf
     *
     * @param  Request $request
     * @return array
     */
    public function pdf(Request $request) 
    {
        return $this->processJobReport($request, 'pdf');

        // descarga sin job
        // $company = Company::first();
        // $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        // $records = $this->getRecords($request->all(), GlobalPayment::class)->get();

        // $pdf = PDF::loadView('finance::global_payments.report_pdf', compact("records", "company", "establishment"))->setPaper('a4', 'landscape');

        // $filename = 'Reporte_Pagos_'.date('YmdHis');

        // return $pdf->download($filename.'.pdf');
    }

        
    /**
     * 
     * Generar reporte excel
     *
     * @param  Request $request
     * @return array
     */
    public function excel(Request $request) 
    {
        return $this->processJobReport($request, 'xlsx');

        // descarga sin job
        // $company = Company::first();
        // $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        // $records = $this->getRecords($request->all(), GlobalPayment::class)->get();

        // return (new GlobalPaymentExport)
        //         ->records($records)
        //         ->company($company)
        //         ->establishment($establishment)
        //         ->download('Reporte_Pagos_'.Carbon::now().'.xlsx');
    }

    
    /**
     * 
     * Procesar reporte en cola
     *
     * @param  Request $request
     * @param  string $type
     * @return array
     */
    private function processJobReport($request, $type)
    {
        $website = $this->getTenantWebsite();
        $user = $this->getCurrentUser();

        $additional_params = [
            'user_id' => $user->id, 
            'type' => $user->type, 
            'establishment_id' => $user->establishment_id
        ];

        $params = $this->getNewParams($request->all(), $additional_params);

        $tray = $this->createDownloadTray($user->id, 'FINANCE', $type, 'Reporte Pagos');

        ProcessGlobalPaymentReport::dispatch($params, $tray->id, $website->id);

        return $this->getJobResponse();
    }


}
