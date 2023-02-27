<?php

    namespace App\Traits;


    trait PaymentModelHelperTrait
    {

        /**
         * 
         * Filtros para obtener pagos al contado
         * Se determina contado por is_credit = false, en payment_method_types
         * 
         * Usado en:
         * Models\Tenant\DocumentPayment
         * Models\Tenant\SaleNotePayment
         * Models\Tenant\QuotationPayment
         *
         * @param  Builder $query
         * @return Builder
         */
        public function scopeWhereCashPaymentMethodType($query)
        {
            return $query->whereHas('payment_method_type', function($q){
                $q->filterCashPayments();
            });
        }

    }
