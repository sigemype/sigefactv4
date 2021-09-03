<?php

namespace Modules\Finance\Models;

use App\Models\Tenant\{Bank, DocumentPayment, PurchasePayment, SaleNotePayment, User};
use App\Models\Tenant\Cash;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\SoapType;
use Modules\Expense\Models\ExpensePayment;
use Modules\Pos\Models\CashTransaction;
use Modules\Sale\Models\ContractPayment;
use Modules\Sale\Models\QuotationPayment;
use Modules\Sale\Models\TechnicalServicePayment;

/**
 * Modules\Finance\Models\GlobalPayment
 *
 * @property-read CashTransaction $cas_transaction
 * @property-read ContractPayment $con_payment
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $destination
 * @property-read DocumentPayment $doc_payments
 * @property-read ExpensePayment $exp_payment
 * @property-read mixed $data_person
 * @property-read mixed $destination_description
 * @property-read mixed $instance_type
 * @property-read mixed $instance_type_description
 * @property-read mixed $type_movement
 * @property-read mixed $type_record
 * @property-read \Modules\Finance\Models\IncomePayment $inc_payment
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $payment
 * @property-read PurchasePayment $pur_payment
 * @property-read QuotationPayment $quo_payment
 * @property-read SaleNotePayment $sln_payments
 * @property-read SoapType $soap_type
 * @property-read TechnicalServicePayment $tec_serv_payment
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalPayment whereDefinePaymentType($payment_type)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalPayment whereFilterPaymentType($params)
 * @mixin \Eloquent
 */
class GlobalPayment extends ModelTenant
{

    protected $fillable = [
        'soap_type_id',
        'destination_id',
        'destination_type',
        'payment_id',
        'payment_type',
        'user_id',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function destination()
    {
        return $this->morphTo();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function payment()
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function doc_payments()
    {
        return $this->belongsTo(DocumentPayment::class, 'payment_id')
                    ->wherePaymentType(DocumentPayment::class);
    }

    /**
     * @return mixed
     */
    public function exp_payment()
    {
        return $this->belongsTo(ExpensePayment::class, 'payment_id')
                    ->wherePaymentType(ExpensePayment::class);
    }

    /**
     * @return mixed
     */
    public function sln_payments()
    {
        return $this->belongsTo(SaleNotePayment::class, 'payment_id')
                    ->wherePaymentType(SaleNotePayment::class);
    }

    /**
     * @return mixed
     */
    public function pur_payment()
    {
        return $this->belongsTo(PurchasePayment::class, 'payment_id')
                    ->wherePaymentType(PurchasePayment::class);
    }

    /**
     * @return mixed
     */
    public function quo_payment()
    {
        return $this->belongsTo(QuotationPayment::class, 'payment_id')
                    ->wherePaymentType(QuotationPayment::class);
    }

    /**
     * @return mixed
     */
    public function con_payment()
    {
        return $this->belongsTo(ContractPayment::class, 'payment_id')
                    ->wherePaymentType(ContractPayment::class);
    }

    /**
     * @return mixed
     */
    public function inc_payment()
    {
        return $this->belongsTo(IncomePayment::class, 'payment_id')
                    ->wherePaymentType(IncomePayment::class);
    }

    /**
     * @return mixed
     */
    public function cas_transaction()
    {
        return $this->belongsTo(CashTransaction::class, 'payment_id')
                    ->wherePaymentType(CashTransaction::class);
    }

    /**
     * @return mixed
     */
    public function tec_serv_payment()
    {
        return $this->belongsTo(TechnicalServicePayment::class, 'payment_id')
                    ->wherePaymentType(TechnicalServicePayment::class);
    }

    public function getDestinationDescriptionAttribute()
    {
        if($this->destination_type  === Cash::class) return 'CAJA GENERAL';
        $destination = $this->destination;
        try{
            $bank_id = $destination->bank_id;
            $bank = Bank::find($bank_id);
            if($bank !== null){
                try{
                    return $bank->description;
                }catch (\Exception $e){
                    // do nothing
                    return '';
                }
            }
        }catch (\Exception $e){
            // do nothing
            return '';
        }
        return '';
        return $this->destination_type === Cash::class ? 'CAJA GENERAL': "{$this->destination->bank->description} - {$this->destination->currency_type_id} - {$this->destination->description}";
    }

    public function getTypeRecordAttribute()
    {
        return $this->destination_type === Cash::class ? 'cash':'bank_account';
    }

    public function getInstanceTypeAttribute()
    {
        $instance_type = [
            DocumentPayment::class => 'document',
            SaleNotePayment::class => 'sale_note',
            PurchasePayment::class => 'purchase',
            ExpensePayment::class => 'expense',
            QuotationPayment::class => 'quotation',
            ContractPayment::class => 'contract',
            IncomePayment::class => 'income',
            CashTransaction::class => 'cash_transaction',
            TechnicalServicePayment::class => 'technical_service',
        ];

        return $instance_type[$this->payment_type];
    }

    public function getInstanceTypeDescriptionAttribute()
    {

        $description = null;

        switch ($this->instance_type) {
            case 'document':
                $description = 'CPE';
                break;
            case 'sale_note':
                $description = 'NOTA DE VENTA';
                break;
            case 'purchase':
                $description = 'COMPRA';
                break;
            case 'expense':
                $description = 'GASTO';
                break;
            case 'quotation':
                $description = 'COTIZACIÓN';
                break;
            case 'contract':
                $description = 'CONTRATO';
                break;
            case 'income':
                $description = 'INGRESO';
                break;
            case 'cash_transaction':
                $description = 'INGRESO';
                break;
            case 'technical_service':
                $description = 'SERVICIO TÉCNICO';
                break;
        }

        return $description;
    }


    public function getTypeMovementAttribute()
    {
        $type = null;

        switch ($this->instance_type) {

            case 'document':
            case 'sale_note':
            case 'quotation':
            case 'contract':
            case 'income':
            case 'cash_transaction':
            case 'technical_service':
                $type = 'input';
                break;
            case 'purchase':
            case 'expense':
                $type = 'output';
                break;

        }

        return $type;

    }


    public function getDataPersonAttribute(){

        $record = $this->payment->associated_record_payment;

        switch ($this->instance_type) {

            case 'document':
            case 'sale_note':
            case 'quotation':
            case 'contract':
            case 'technical_service':
                $person['name'] = $record->customer->name;
                $person['number'] = $record->customer->number;
                break;
            case 'purchase':
            case 'expense':
                $person['name'] = $record->supplier->name;
                $person['number'] = $record->supplier->number;
                break;
            case 'income':
                $person['name'] = $record->customer;
                $person['number'] = '';
            case 'cash_transaction':
                $person['name'] = '-';
                $person['number'] = '';
        }

        return (object) $person;
    }


    public function scopeWhereFilterPaymentType($query, $params) {

        /** DocumentPayment  */
        $query->whereHas('doc_payments', function ($q) use ($params) {
            if($params->date_start) {
                $q->where('date_of_payment', '>=', $params->date_start);
            }
            if($params->date_end) {
                $q->where('date_of_payment', '<=', $params->date_end);
            }
            // $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
              $q->whereHas('associated_record_payment', function ($p) {
                  $p->whereStateTypeAccepted()->whereTypeUser();
              });
        });
        $query->OrWhereHas('exp_payment', function ($q) use ($params) {
            if($params->date_start) {
                $q->where('date_of_payment', '>=', $params->date_start);
            }
            if($params->date_end) {
                $q->where('date_of_payment', '<=', $params->date_end);
            }
            // $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
            $q->whereHas('associated_record_payment', function ($p) {
                  $p->whereStateTypeAccepted()->whereTypeUser();
              });
        });
        /*SaleNotePayment*/
        $query->OrWhereHas('sln_payments', function ($q) use ($params) {
            if($params->date_start) {
                $q->where('date_of_payment', '>=', $params->date_start);
            }
            if($params->date_end) {
                $q->where('date_of_payment', '<=', $params->date_end);
            }
            // $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
            $q->whereHas('associated_record_payment', function ($p) {
                  $p->whereStateTypeAccepted()->whereTypeUser()
                    ->whereNotChanged();
              });
        });
        /*PurchasePayment*/
        $query->OrWhereHas('pur_payment', function ($q) use ($params) {
            if($params->date_start) {
                $q->where('date_of_payment', '>=', $params->date_start);
            }
            if($params->date_end) {
                $q->where('date_of_payment', '<=', $params->date_end);
            }
            // $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
            $q->whereHas('associated_record_payment', function ($p) {
                  $p->whereStateTypeAccepted()->whereTypeUser();
              });

        });
        /*QuotationPayment*/
        $query
            ->OrWhereHas('quo_payment', function ($q) use ($params) {
                if($params->date_start) {
                    $q->where('date_of_payment', '>=', $params->date_start);
                }
                if($params->date_end) {
                    $q->where('date_of_payment', '<=', $params->date_end);
                }
                // $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                $q->whereHas('associated_record_payment', function ($p) {
                      $p->whereStateTypeAccepted()->whereTypeUser()
                        ->whereNotChanged();
                  });

            });
        /*ContractPayment*/
        $query
            ->OrWhereHas('con_payment', function ($q) use ($params) {
                if($params->date_start) {
                    $q->where('date_of_payment', '>=', $params->date_start);
                }
                if($params->date_end) {
                    $q->where('date_of_payment', '<=', $params->date_end);
                }
                // $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                $q  ->whereHas('associated_record_payment', function ($p) {
                      $p->whereStateTypeAccepted()->whereTypeUser()
                        ->whereNotChanged();
                  });

            });
        /* IncomePayment */
        $query
            ->OrWhereHas('inc_payment', function ($q) use ($params) {
                if($params->date_start) {
                    $q->where('date_of_payment', '>=', $params->date_start);
                }
                if($params->date_end) {
                    $q->where('date_of_payment', '<=', $params->date_end);
                }
                // $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                $q  ->whereHas('associated_record_payment', function ($p) {
                      $p->whereStateTypeAccepted()->whereTypeUser();
                  });

            });
        /*CashTransaction*/
        $query
            ->OrWhereHas('cas_transaction', function ($q) use ($params) {
                if($params->date_start) {
                    $q->where('date', '>=', $params->date_start);
                }
                if($params->date_end) {
                    $q->where('date', '<=', $params->date_end);
                }

                // $q->whereBetween('date', [$params->date_start, $params->date_end]);
            });
        /* TechnicalServicePayment */
        $query
            ->OrWhereHas('tec_serv_payment', function ($q) use ($params) {
                if($params->date_start) {
                    $q->where('date_of_payment', '>=', $params->date_start);
                }
                if($params->date_end) {
                    $q->where('date_of_payment', '<=', $params->date_end);
                }
                // $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                $q  ->whereHas('associated_record_payment', function ($p) {
                      $p->whereTypeUser();
                  });

            });
        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWhereDefinePaymentType($query, $payment_type)
    {

        if($payment_type === IncomePayment::class){
            return $query->whereIn('payment_type', [CashTransaction::class, $payment_type]);
        }

        return $query->wherePaymentType($payment_type);

    }
}
