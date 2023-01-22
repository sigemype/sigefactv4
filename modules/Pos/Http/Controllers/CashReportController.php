<?php

namespace Modules\Pos\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Pos\Traits\CashReportTrait;
use Mpdf\Mpdf;
use App\Models\Tenant\Cash;
use Carbon\Carbon;


class CashReportController extends Controller
{
    
    use CashReportTrait;


    /**
     *
     * Generar reporte de Resumen de Operaciones Diarias
     *
     * @param  int $cash
     */
    public function reportSummaryDailyOperations($cash_id)
    {
        $cash = Cash::with(['cash_documents', 'cash_documents_credit'])->findOrFail($cash_id);
        $header_data = app(CashController::class)->getHeaderCommonDataToReport($cash);

        $data = $this->initDataSummaryDailyOperations();

        $this->setDataCreditSales($cash, $data);

        $this->setDataCashSalesPurchases($cash, $data);

        $this->calculateGlobalValues($data);

        $pdf_data = [
            'header_data' => $header_data,
            'data' => $data,
        ];

        $filename = 'Reporte_resumen_operaciones_diarias_'.date('YmdHis');

        return $this->generalToPrintReport('pos::cash.reports.report_summary_daily_operations_pdf', 'report_summary_daily_operations', $filename, $pdf_data);
    }

}
