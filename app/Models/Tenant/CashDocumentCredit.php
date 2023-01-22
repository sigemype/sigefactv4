<?php

namespace App\Models\Tenant;

class CashDocumentCredit extends ModelTenant
{

    protected $fillable = [
        'cash_id',
        'cash_id_processed',
        'document_id',
        'sale_note_id',
        'status'
    ];


    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function sale_note()
    {
        return $this->belongsTo(SaleNote::class);
    }


    /**
     * 
     * Retornar el modelo asociado dependiendo del registro relacionado
     * 
     */
    public function getDataModelAssociated()
    {
        if(!is_null($this->document_id)) return $this->document;

        if(!is_null($this->sale_note_id)) return $this->sale_note;
    }

    
    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'PENDING';
    }

}
