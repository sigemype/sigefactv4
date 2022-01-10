<?php

    namespace Modules\Finance\Http\Resources;

    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\ResourceCollection;
    use Illuminate\Support\Collection;
    use Modules\Finance\Http\Controllers\MovementController;
    use Modules\Finance\Models\GlobalPayment;
    use phpDocumentor\Reflection\DocBlock\Description;


    class MovementCollection extends ResourceCollection
    {
        /**
         * Transform the resource collection into an array.
         *
         * @param Request $request
         *
         * @return array
         */
        protected static $balance = 0;
        protected static $residuary = 0;
        protected static $request;


        public function toArray($request)
        {
            self::$request = $request;
            $this->calculateResiduary(self::$request);
            /** @var Collection $data */

            $data = $this->collection->transform(function (GlobalPayment $row, $key) use ($request) {
                $data_person = $row->data_person;
                $amount = $row->payment->payment;
                $document = $row->payment->document;
                // Convirtiendo el documento que esta hecho en dolares a soles
                if ($document) {
                    if ($document->currency_type_id === 'USD') {
                        $amount = $amount * $document->exchange_rate_sale;
                    }
                }
                self::$balance = ($row->type_movement == 'input') ? self::$balance + $amount : self::$balance - $amount;

                $index = $key + 1;
                $payment = $row->payment;
                // $timedate = $payment->date_of_payment->format('Y-m-d');
                $timedate = null;
                if( $payment->date_of_payment &&  $payment->date_of_payment != null){
                    $timedate = $payment->date_of_payment->toDateTimeString();

                }
                $document_type = '';
                $type_movement = $row->type_movement;
                $payments = $payment->payment;

                if($payment->associated_record_payment ) {
                    if ($payment->associated_record_payment->date_of_issue) {
                        $timedate = $payment->associated_record_payment->date_of_issue->format('Y-m-d') . " " . $payment->associated_record_payment->time_of_issue;
                        $timedate = Carbon::createFromFormat('Y-m-d H:i:s', $timedate)->toDateTimeString();
                    }

                    if ($payment->associated_record_payment->document_type) {
                        $document_type = $payment->associated_record_payment->document_type->description;
                    } elseif (isset($payment->associated_record_payment->prefix)) {
                        $document_type = $payment->associated_record_payment->prefix;
                    }
                }
                $destinationArray = $row->getDestinationWithCci();
                $destinationName = $destinationArray['name'] . " - " . $destinationArray['description'];

                $person_name = $data_person->name;
                $person_number = $data_person->number;
                $numberFull =$payment->associated_record_payment->number_full??null;
                if($row->instance_type == 'bank_loan_payment'){
                    /** @var \Modules\Finance\Models\GlobalPayment $row */
                    $person_name = $person_name->description;
                    $document_type = $row->instance_type_description;
                    // $person_name = $person_name->description;
                    $person_number= '';
                    // dd($row->payment->associated_record_payment);
                    $numberFull = $row->payment->associated_record_payment->getNumberFull();
                }

                return [
                    'index' => $index,
                    'payments' => $payments,
                    'document_type' => $document_type,
                    'id' => $row->id,
                    'destination_description' => $row->destination_description,
                    'destination_array' => $destinationArray,
                    'destination_name' => $destinationName,
                    'date_of_payment_class' => get_class($payment),
                    'date_of_payment' => $timedate,
                    'payment_method_type_description' => $this->getPaymentMethodTypeDescription($row),
                    'reference' => $payment->reference,
                    'total' => $amount,
                    'number_full' => $numberFull,
                    'currency_type_id' => $payment->associated_record_payment->currency_type_id??'PEN',
                    // 'document_type_description' => ($payment->associated_record_payment->document_type) ? $payment->associated_record_payment->document_type->description:'NV',
                    'document_type_description' => $this->getDocumentTypeDescription($row),
                    'person_name' => $person_name,
                    'person_number' => $person_number,
                    // 'payment' => $row->payment,
                    // 'payment_type' => $row->payment_type,
                    'instance_type' => $row->instance_type,
                    'instance_type_description' => $row->instance_type_description,
                    'user_id' => $row->user_id,
                    'user_name' => optional($row->user)->name,

                    'type_movement' => $row->type_movement,
                    'input' => ($row->type_movement == 'input') ? number_format($amount, 2, ".", "") : '-',
                    'output' => ($row->type_movement == 'output') ? number_format($amount, 2, ".", "") : '-',
                    'balance' => number_format(self::$balance, 2, ".", ""),
                    'items' => $this->getItems($row),


                ];
            });

            return $data;
        }

        public function calculateResiduary($request)
        {

            if ($request->page >= 2) {

                $data = app(MovementController::class)->getRecords($request, GlobalPayment::class)
                    ->limit(($request->page * 20) - 20)->get();

                $input = $data->where('type_movement', 'input')->sum('payment.payment');
                $output = $data->where('type_movement', 'output')->sum('payment.payment');

                self::$residuary += $input - $output;
                self::$balance = self::$residuary;

            }

        }

        public function getPaymentMethodTypeDescription($row)
        {

            $payment_method_type_description = '';

            if ($row->payment->payment_method_type) {

                $payment_method_type_description = $row->payment->payment_method_type->description;

            } elseif($row->payment->expense_method_type){
                $payment_method_type_description = $row->payment->expense_method_type->description;
            }

            return $payment_method_type_description;
        }

        public function getDocumentTypeDescription($row)
        {

            $document_type = '';

            if($row->payment->associated_record_payment) {
                if ($row->payment->associated_record_payment->document_type) {

                    $document_type = $row->payment->associated_record_payment->document_type->description;

                } elseif (isset($row->payment->associated_record_payment->prefix)) {

                    $document_type = $row->payment->associated_record_payment->prefix;

                }
            }
            return $document_type;

        }

        public function getItems($row)
        {

            if (in_array($row->instance_type, ['expense', 'income', 'bank_loan_payment'])) {

                return $row->payment->associated_record_payment->items->transform(function ($row, $key) {
                    return [
                        'description' => $row->description,
                    ];
                });
            }

            return [];

        }


    }
