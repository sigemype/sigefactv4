<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\DocumentFeeCollection;
use App\Models\System\PaymentMethodType;
use App\Models\Tenant\Document;
use App\Models\Tenant\DocumentFee;

class DocumentFeeController extends Controller{
    
    public function records($document_id){
        
        $records = DocumentFee::where('document_id', $document_id)->get();

        return new DocumentFeeCollection($records);
    }

    public function tables(){
        return [
            'payment_method_types' => PaymentMethodType::all(),
            'payment_destinations' => $this->getPaymentDestinations()
        ];
    }

    public function document($document_id){
        $document = Document::find($document_id);

        $total_paid = collect($document->payments)->sum('payment');
        $total = $document->total;
        $total_difference = round($total - $total_paid, 2);

        return [
            'number_full' => $document->number_full,
            'total_paid' => $total_paid,
            'total' => $total,
            'total_difference' => $total_difference
        ];

    }
}
