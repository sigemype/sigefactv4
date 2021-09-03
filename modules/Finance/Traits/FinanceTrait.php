<?php

namespace Modules\Finance\Traits;

use App\Models\Tenant\{DocumentPayment, PurchasePayment, SaleNotePayment};
use App\Models\Tenant\BankAccount;
use App\Models\Tenant\Cash;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Modules\Expense\Models\ExpensePayment;
use Modules\Finance\Models\IncomePayment;
use Modules\Pos\Models\CashTransaction;
use Modules\Sale\Models\ContractPayment;
use Modules\Sale\Models\QuotationPayment;
use Modules\Sale\Models\TechnicalServicePayment;

trait FinanceTrait
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getPaymentDestinations(){

        $bank_accounts = self::getBankAccounts();
        $cash = $this->getCash();

        // dd($cash);
        if($cash){
            return $bank_accounts->push($cash);
        }

        return $bank_accounts;

    }


    private static function getBankAccounts(){

        return BankAccount::get()->transform(function($row) {
            return [
                'id' => $row->id,
                'cash_id' => null,
                'description' => "{$row->bank->description} - {$row->currency_type_id} - {$row->description}",
            ];
        });

    }


    /**
     * @return array|null
     */
    public function getCash()
    {
        $cash =  Cash::query()->where([['user_id',auth()->id()],['state',true]])->first();
        if($cash){
            return [
                'id' => 'cash',
                'cash_id' => $cash->id,
                'description' => ($cash->reference_number) ? "CAJA GENERAL - {$cash->reference_number}" : "CAJA GENERAL",
            ];
        }
        // else{

        //     $cash_create = Cash::create([
        //                             'user_id' => auth()->user()->id,
        //                             'date_opening' => date('Y-m-d'),
        //                             'time_opening' => date('H:i:s'),
        //                             'date_closed' => null,
        //                             'time_closed' => null,
        //                             'beginning_balance' => 0,
        //                             'final_balance' => 0,
        //                             'income' => 0,
        //                             'state' => true,
        //                             'reference_number' => null
        //                         ]);

        //     return [
        //         'id' => 'cash',
        //         'cash_id' => $cash_create->id,
        //         'description' => "CAJA GENERAL"
        //     ];

        // }

        return null;

    }

    public function createGlobalPayment($model, $row)
    {
        $destination = $this->getDestinationRecord($row);
        $company = Company::active();

        $model->global_payment()->create([
            'user_id' => auth()->id(),
            'soap_type_id' => $company->soap_type_id,
            'destination_id' => $destination['destination_id'],
            'destination_type' => $destination['destination_type'],
        ]);
    }

    public function getDestinationRecord($row)
    {
        if($row['payment_destination_id'] === 'cash'){
            $destination_id = $this->getCash()['cash_id'];
            $destination_type = Cash::class;

        }else{

            $destination_id = $row['payment_destination_id'];
            $destination_type = BankAccount::class;

        }

        return [
            'destination_id' => $destination_id,
            'destination_type' => $destination_type,
        ];
    }


    public function deleteAllPayments($payments){

        foreach ($payments as $payment) {
            $payment->delete();
        }

    }

    public function getCollectionPaymentTypes(){

        return [
            ['id'=> DocumentPayment::class, 'description' => 'COMPROBANTES (CPE)'],
            ['id'=> SaleNotePayment::class, 'description' => 'NOTAS DE VENTA'],
            ['id'=> PurchasePayment::class, 'description' => 'COMPRAS'],
            ['id'=> ExpensePayment::class, 'description' => 'GASTOS'],
            ['id'=> QuotationPayment::class, 'description' => 'COTIZACIÓN'],
            ['id'=> ContractPayment::class, 'description' => 'CONTRATO'],
            ['id'=> IncomePayment::class, 'description' => 'INGRESO'],
            // ['id'=> CashTransaction::class, 'description' => 'CAJA CHICA POS'],
            ['id'=> TechnicalServicePayment::class, 'description' => 'SERVICIO TÉCNICO'],
        ];
    }

    /**
     * @return \string[][]
     */
    public function getCollectionDestinationTypes(){

        $return = [
            ['id' => Cash::class, 'description' => 'CAJA GENERAL'],
            ['id' => BankAccount::class, 'description' => 'CUENTA BANCARIA'],
        ];
        /** @var Collection $banks */
        $banks = BankAccount::SelectIdDescription()->get()->transform(function ($v) {
            // Añade el banco con el standar App\Models\Tenant\BankAccount, separado por :: el id del banco
            $v = $v->toArray();
            $v['id'] = BankAccount::class."::".$v['id'];
            return $v;
        })->toArray();
        $return = array_merge($return, $banks);
        return $return;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    public function getDatesOfPeriod($request){

        $period = $request['period'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];

        $d_start = null;
        $d_end = null;

        switch ($period) {
            case 'month':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_start.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'between_months':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_end.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'date':
                $d_start = $date_start;
                $d_end = $date_start;
                break;
            case 'between_dates':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
        }

        return [
            'd_start' => $d_start,
            'd_end' => $d_end
        ];
    }


    /**
     * @param \Illuminate\Database\Eloquent\Collection $cash
     *
     * @return array
     */
    public function getBalanceByCash($cash){

        $document_payment = $this->getSumPayment($cash, DocumentPayment::class);
        $expense_payment = $this->getSumPayment($cash, ExpensePayment::class);
        $sale_note_payment = $this->getSumPayment($cash, SaleNotePayment::class);
        $purchase_payment = $this->getSumPayment($cash, PurchasePayment::class);
        $quotation_payment = $this->getSumPayment($cash, QuotationPayment::class);
        // $contract_payment = 0; //$this->getSumPayment($cash, ContractPayment::class);
        $contract_payment = $this->getSumPayment($cash, ContractPayment::class);
        $income_payment = $this->getSumPayment($cash, IncomePayment::class);
        $cash_pos = $this->getSumPaymentCashPos($cash, CashTransaction::class);
        $technical_service_payment = $this->getSumPayment($cash, TechnicalServicePayment::class);

        $entry = $document_payment + $sale_note_payment + $quotation_payment + $contract_payment + $income_payment + $cash_pos + $technical_service_payment;
        $egress = $expense_payment + $purchase_payment;

        $balance = $entry - $egress;

        return [

            'id' => 'cash',
            'description' => "CAJA GENERAL",
            'initial_balance' => self::FormatNumber($cash_pos),
            'expense_payment' => self::FormatNumber($expense_payment),
            'purchase_payment' => self::FormatNumber($purchase_payment),
            'document_payment' => self::FormatNumber($document_payment),
            'sale_note_payment' => self::FormatNumber($sale_note_payment),
            'quotation_payment' => self::FormatNumber($quotation_payment),
            'contract_payment' => self::FormatNumber($contract_payment),
            'income_payment' => self::FormatNumber($income_payment + $cash_pos),
            'technical_service_payment' => self::FormatNumber($technical_service_payment),
            'balance' => self::FormatNumber($balance)

        ];
    }

    /**
     * @param        $number
     * @param int    $decimal
     * @param string $decimal_separator
     * @param string $thousands_separator
     *
     * @return string
     */
    public static function FormatNumber($number){
        if($number == '-') return $number;
        if($number == 0) return '0.00';

        return number_format($number,2,'.', '');
    }

    public function getSumPaymentCashPos($record, $model)
    {
        return $record->where('payment_type', $model)->sum(function($row){
            return $row->payment->payment;
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $bank_accounts
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getBalanceByBankAcounts($bank_accounts)
    {
        $records = $bank_accounts->map(function($row) {

            $document_payment = $this->getSumPayment($row->global_destination, DocumentPayment::class);
            $expense_payment = $this->getSumPayment($row->global_destination, ExpensePayment::class);
            $sale_note_payment = $this->getSumPayment($row->global_destination, SaleNotePayment::class);
            $purchase_payment = $this->getSumPayment($row->global_destination, PurchasePayment::class);
            $quotation_payment = $this->getSumPayment($row->global_destination, QuotationPayment::class);
            // $contract_payment = 0; //$this->getSumPayment($row->global_destination, ContractPayment::class);
            $contract_payment = $this->getSumPayment($row->global_destination, ContractPayment::class);
            $income_payment = $this->getSumPayment($row->global_destination, IncomePayment::class);
            $technical_service_payment = $this->getSumPayment($row->global_destination, TechnicalServicePayment::class);

            $entry = $document_payment + $sale_note_payment + $quotation_payment + $contract_payment + $income_payment + $technical_service_payment;
            $egress = $expense_payment + $purchase_payment;
            $balance = $row->initial_balance + $entry - $egress;

            return [

                'id' => $row->id,
                'description' => "{$row->bank->description} - {$row->currency_type_id} - {$row->description}",
                'expense_payment' => self::FormatNumber($expense_payment),
                'sale_note_payment' => self::FormatNumber($sale_note_payment),
                'quotation_payment' => self::FormatNumber($quotation_payment),
                'contract_payment' => self::FormatNumber($contract_payment),
                'document_payment' => self::FormatNumber($document_payment),
                'purchase_payment' => self::FormatNumber($purchase_payment),
                'income_payment' => self::FormatNumber($income_payment),
                'initial_balance' => self::FormatNumber($row->initial_balance),
                'technical_service_payment' => self::FormatNumber($technical_service_payment),
                'balance' => self::FormatNumber($balance),

            ];

        });

        return $records;

    }

    public function getSumPayment($record, $model)
    {
        return $record->where('payment_type', $model)->sum(function($row){

            $total_credit_notes = ($row->instance_type == 'document') ? $this->getTotalCreditNotes($row->payment->associated_record_payment) : 0;
            $total_currency_type = $this->calculateTotalCurrencyType($row->payment->associated_record_payment, $row->payment->payment);

            return $total_currency_type - $total_credit_notes;

        });
    }


    public function getTotalCreditNotes($record)
    {

        $credit_notes = $record->affected_documents->where('note_type', 'credit');

        $total_credit_notes = $credit_notes->sum(function($note){

            if(in_array($note->document->state_type_id, ['01','03','05','07','13'])){
                return $this->calculateTotalCurrencyType($note->document, $note->document->total);
            }

            return 0;
        });

        return $total_credit_notes;

    }


    public function calculateTotalCurrencyType($record, $payment)
    {
        return ($record->currency_type_id === 'USD') ? $payment * $record->exchange_rate_sale : $payment;
    }


    /**
     * @param \Illuminate\Database\Eloquent\Collection $payment_method_types
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getRecordsByPaymentMethodTypes($payment_method_types) {

        $records = $payment_method_types->map(function ($row) {

            $document_payment = $this->getSumByPMT($row->document_payments, true);
            $sale_note_payment = $this->getSumByPMT($row->sale_note_payments);
            $purchase_payment = $this->getSumByPMT($row->purchase_payments);
            $quotation_payment = $this->getSumByPMT($row->quotation_payments);
            $contract_payment = $this->getSumByPMT($row->contract_payments);
            // $contract_payment = 0; //$this->getSumByPMT($row->contract_payments);
            $cash_transaction = $row->cash_transactions->sum('payment');
            $income_payment = $this->getSumByPMT($row->income_payments) + $cash_transaction;
            $technical_service_payment = $this->getSumByPMT($row->technical_service_payments);

            return [

                'id'                => $row->id,
                'description'       => $row->description,
                'expense_payment'   => '-',
                'sale_note_payment' => self::FormatNumber($sale_note_payment),

                'document_payment'          => self::FormatNumber($document_payment),
                'purchase_payment'          => self::FormatNumber($purchase_payment),
                'quotation_payment'         => self::FormatNumber($quotation_payment),
                'contract_payment'          => self::FormatNumber($contract_payment),
                'income_payment'            => self::FormatNumber($income_payment),
                'technical_service_payment' => self::FormatNumber($technical_service_payment),
                'balance'                   => self::FormatNumber($sale_note_payment + $document_payment + $purchase_payment + $quotation_payment + $contract_payment + $income_payment + $technical_service_payment),
            ];

        });

        return $records;
    }


    /**
     * @param \Illuminate\Database\Eloquent\Collection $expense_method_types
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getRecordsByExpenseMethodTypes($expense_method_types)
    {

        $records = $expense_method_types->map(function($row){

            // dd($row->expense_payments);
            $expense_payment = $this->getSumByPMT($row->expense_payments);

            return [

                'id' => $row->id,
                'description' => $row->description,
                'expense_payment' => self::FormatNumber($expense_payment),
                'sale_note_payment' => '-',
                'document_payment' => '-',
                'quotation_payment' => '-',
                'contract_payment' => '-',
                'income_payment' => '-',
                'purchase_payment' => '-',
                'technical_service_payment' => '-',
                'balance' =>  self::FormatNumber($expense_payment),

            ];

        });

        return $records;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $records
     * @param false                                    $include_credit_notes
     *
     * @return mixed
     */
    public function getSumByPMT($records, $include_credit_notes = false)
    {

        return $records->sum(function($row) use($include_credit_notes){

            $total_credit_notes = ($include_credit_notes) ? $this->getTotalCreditNotes($row->associated_record_payment) : 0;
            $total_currency_type = $this->calculateTotalCurrencyType($row->associated_record_payment, $row->payment);

            return $total_currency_type - $total_credit_notes;
        });

    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $records_by_pmt
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $records_by_emt
     *
     * @return array
     */
    public function getTotalsPaymentMethodType($records_by_pmt, $records_by_emt)
    {

        $t_documents = 0;
        $t_sale_notes = 0;
        $t_quotations = 0;
        $t_contracts = 0;
        $t_purchases = 0;
        $t_expenses = 0;
        $t_income = 0;
        $t_technical_services = 0;

        $t_documents = $records_by_pmt->sum('document_payment');
        $t_sale_notes = $records_by_pmt->sum('sale_note_payment');
        $t_quotations = $records_by_pmt->sum('quotation_payment');
        $t_contracts = $records_by_pmt->sum('contract_payment');
        $t_purchases = $records_by_pmt->sum('purchase_payment');
        $t_income = $records_by_pmt->sum('income_payment');
        $t_technical_services = $records_by_pmt->sum('technical_service_payment');
        $t_balance = $records_by_pmt->sum('balance') -$records_by_emt->sum('balance');
        $t_expenses = $records_by_emt->sum('expense_payment');
        /*
        foreach ($records_by_pmt as $value) {

            $t_documents += $value['document_payment'];
            $t_sale_notes += $value['sale_note_payment'];
            $t_quotations += $value['quotation_payment'];
            $t_contracts += $value['contract_payment'];
            $t_purchases += $value['purchase_payment'];
            $t_income += $value['income_payment'];
            $t_technical_services += $value['technical_service_payment'];

        }

        foreach ($records_by_emt as $value) {

            $t_expenses += $value['expense_payment'];

        }

        */
        return [
            't_documents' => self::FormatNumber($t_documents),
            't_sale_notes' => self::FormatNumber($t_sale_notes),
            't_quotations' => self::FormatNumber($t_quotations),
            't_contracts' => self::FormatNumber($t_contracts),
            't_purchases' => self::FormatNumber($t_purchases),
            't_expenses' => self::FormatNumber($t_expenses),
            't_income' => self::FormatNumber($t_income),
            't_technical_services' => self::FormatNumber($t_technical_services),
            't_balance' => self::FormatNumber($t_balance),
        ];

    }



    //cash transaction

    public function getCashTransaction($user_id){

        $cash =  Cash::where([['user_id', $user_id],['state',true]])->first();

        if($cash){

            return [
                'id' => 'cash',
                'cash_id' => $cash->id,
                'description' => ($cash->reference_number) ? "CAJA GENERAL - {$cash->reference_number}" : "CAJA GENERAL",
            ];

        }

        return null;

    }

    public function createGlobalPaymentTransaction($model, $row){

        $destination = $this->getDestinationRecordTransaction($row);
        $company = Company::active();

        $model->global_payment()->create([
            'user_id' => auth()->id(),
            'soap_type_id' => $company->soap_type_id,
            'destination_id' => $destination['destination_id'],
            'destination_type' => $destination['destination_type'],
        ]);

    }

    public function getDestinationRecordTransaction($row){

        if($row['payment_destination_id'] === 'cash'){

            $destination_id = $this->getCashTransaction($row['user_id'])['cash_id'];
            $destination_type = Cash::class;

        }else{

            $destination_id = $row['payment_destination_id'];
            $destination_type = BankAccount::class;

        }

        return [
            'destination_id' => $destination_id,
            'destination_type' => $destination_type,
        ];
    }


}
