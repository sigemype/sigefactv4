<?php

namespace Modules\Pos\Traits;

use App\CoreFacturalo\Helpers\Functions\GeneralPdfHelper;
use Mpdf\Mpdf;


trait CashReportTrait
{
        
    /**
     *
     * @return array
     */
    public function initDataSummaryDailyOperations()
    {
        return [
            'cash_sales_income' => [
                'total_cash' => 0,
                'total_transfer' => 0,
                'total' => 0,
            ],
            'credit_sales' => 0,
            'amortization_credit_sales' => [
                'total_cash' => 0,
                'total_transfer' => 0,
                'total' => 0,
            ],
            
            'purchase_cash' => [
                'total_cash' => 0,
                'total_transfer' => 0,
                'total' => 0,
            ],
            'credit_purchases' => 0,
            'amortization_credit_purchases' => [
                'total_cash' => 0,
                'total_transfer' => 0,
                'total' => 0,
            ],

            'total_cash_sales' => 0,
            'total_cash_purchases' => 0,

            'total_cash_sales_transfer' => 0,
            'total_cash_purchases_transfer' => 0,

            'cash_balance' => 0,
            'balance_transfer' => 0,
            'total_balance' => 0,
        ];
    }

    
    /**
     * 
     * Asignar datos de ventas al credito y amortizacion de ventas credito
     *
     * @param  Cash $cash
     * @param  array $data
     * @return void
     */
    public function setDataCreditSales($cash, &$data)
    {
        // para facturas y notas de venta
        foreach ($cash->cash_documents_credit as $cash_document_credit)
        {
            if($cash_document_credit->isPending())
            {
                $model_associated = $cash_document_credit->getDataModelAssociated();

                if($model_associated)
                {
                    $data_summary_daily = $model_associated->applySummaryDailyOperations();
                    
                    if($data_summary_daily['apply'])
                    {
                        $data['credit_sales'] += $model_associated->total;
                        
                        $total_cash = $model_associated->totalCashPaymentsWithoutDestination();
                        $total_transfer = $model_associated->totalTransferPayments();

                        $data['amortization_credit_sales']['total_cash'] += $total_cash;
                        $data['amortization_credit_sales']['total_transfer'] += $total_transfer;
                        $data['amortization_credit_sales']['total'] += $total_cash + $total_transfer;
                    }
                }
            }
        }
    }
    
    
    /**
     * 
     * Datos de ventas al contado efectivo/transferencia - Compras credito/contado
     *
     * @param  Cash $cash
     * @param  array $data
     * @return void
     */
    public function setDataCashSalesPurchases($cash, &$data)
    {
        foreach ($cash->cash_documents as $cash_document)
        {
            $model_associated = $cash_document->getDataModelAssociated();
            
            if($model_associated)
            {
                $data_summary_daily = $model_associated->applySummaryDailyOperations();

                if($data_summary_daily['apply'])
                {
                    if($data_summary_daily['transaction_type'] === 'income')
                    {
                        $this->setDataCashSalesIncome($model_associated, $data);
                    }
                    else if($data_summary_daily['transaction_type'] === 'egress')
                    {
                        if($data_summary_daily['document_type'] === 'purchases') $this->setDataCashCreditPurchases($model_associated, $data);
                    }
                }
            }
        }
    }
    
        
    /**
     * 
     * Asignar valores finales
     *
     * @param  array $data
     * @return void
     */
    public function calculateGlobalValues(&$data)
    {
        // total de ingresos al contado en efectivo
        $data['total_cash_sales'] = $data['cash_sales_income']['total_cash'] + $data['amortization_credit_sales']['total_cash'];

        // total compras al contado en efectivo
        $data['total_cash_purchases'] = $data['purchase_cash']['total_cash'] + $data['amortization_credit_purchases']['total_cash'];

        // total ingresos al contado por transferencia
        $data['total_cash_sales_transfer'] = $data['cash_sales_income']['total_transfer'] + $data['amortization_credit_sales']['total_transfer'];

        // total compras al contado por transferencia
        $data['total_cash_purchases_transfer'] = $data['purchase_cash']['total_transfer'] + $data['amortization_credit_purchases']['total_transfer'];


        // saldo en efectivo
        $data['cash_balance'] = $data['total_cash_sales'] - $data['total_cash_purchases'];

        // saldo por transferencias
        $data['balance_transfer'] = $data['total_cash_sales_transfer'] - $data['total_cash_purchases_transfer'];

        // saldo total
        $data['total_balance'] = $data['cash_balance'] + $data['balance_transfer'];
        
    }


    /**
     * 
     * Datos de ventas al contado efectivo/transferencia
     *
     * @param  $model_associated
     * @param  array $data
     * @return void
     */
    public function setDataCashSalesIncome($model_associated, &$data)
    {
        $total_cash = $model_associated->totalCashPaymentsWithoutDestination();
        $total_transfer = $model_associated->totalTransferPayments();
        $total_cash_transfer = $total_cash + $total_transfer;

        $data['cash_sales_income']['total_cash'] += $total_cash;
        $data['cash_sales_income']['total_transfer'] += $total_transfer;
        $data['cash_sales_income']['total'] += $total_cash_transfer;
    }


    /**
     * 
     * Datos de compras al contado efectivo/transferencia y credito
     *
     * @param  $model_associated
     * @param  array $data
     * @return void
     */
    public function setDataCashCreditPurchases($model_associated, &$data)
    {
        $total_cash = $model_associated->totalCashPaymentsWithoutDestination();
        $total_transfer = $model_associated->totalTransferPayments();
        $total_cash_transfer = $total_cash + $total_transfer;

        //es una compra por pagar
        if($model_associated->isToPay())
        {
            $data['credit_purchases'] += $model_associated->total;

            $data['amortization_credit_purchases']['total_cash'] += $total_cash;
            $data['amortization_credit_purchases']['total_transfer'] += $total_transfer;
            $data['amortization_credit_purchases']['total'] += $total_cash_transfer;
        }
        //compra en efectivo
        else
        {
            $data['purchase_cash']['total_cash'] += $total_cash;
            $data['purchase_cash']['total_transfer'] += $total_transfer;
            $data['purchase_cash']['total'] += $total_cash_transfer;
        }
    }

    
    /**
     * 
     * Imprimir reporte a4
     *
     * @param  string $view
     * @param  string $temp_folder
     * @param  string $filename
     * @param  array $data
     */
    public function generalToPrintReport($view, $temp_folder, $filename, $pdf_data)
    {
        $view = view($view, $pdf_data);
        $html = $view->render();

        $pdf = new Mpdf(['mode' => 'utf-8']);
        $pdf->WriteHTML($html);

        return GeneralPdfHelper::getPreviewTempPdfWithFilename($temp_folder, $filename, $pdf->output('', 'S'));
    }



}
