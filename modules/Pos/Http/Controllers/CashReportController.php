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
     * Reporte general de caja v2, asociado a pagos
     *
     * @param  int $cash_id
     */
    public function generalCashReportWithPayments($cash_id)
    {
        $cash = Cash::filterDataGeneralCashReport()->findOrFail($cash_id);

        $data = app(CashController::class)->getHeaderCommonDataToReport($cash);

        $filename = 'Reporte_general_caja_v2_'.date('YmdHis');

        return $this->generalToPrintReport(
            'pos::cash.reports.general_cash_report_payments_pdf', 
            'general_cash_report_payments',
            $filename, 
            $this->getDataCashReportWithPayments($cash, $data)
        );
    }

    
    /**
     *
     * Generar reporte de pagos asociados a caja, con destino caja y en efectivo
     * 
     * Disponible para cpe u nv
     *
     * @param  int $cash
     */
    public function reportPaymentsAssociatedCash($cash_id)
    {
        $cash = Cash::with([
                        'global_destination' => function($query){
                            return $query->filtersPaymentsAssociatedCash();
                        }
                    ])
                    ->findOrFail($cash_id);

        $data = app(CashController::class)->getHeaderCommonDataToReport($cash);

        $filename = 'Reporte_ingresos_caja_efectivo_'.date('YmdHis');

        return $this->generalToPrintReport(
            'pos::cash.reports.report_payments_associated_cash_pdf', 
            'report_payments_associated_cash',
            $filename, 
            $this->getDataPaymentsAssociatedCash($cash, $data)
        );
    }


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
