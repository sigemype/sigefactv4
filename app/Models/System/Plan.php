<?php

namespace App\Models\System;
use Hyn\Tenancy\Traits\UsesSystemConnection;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use UsesSystemConnection;


    protected $fillable = [
        'name',
        'pricing',
        'limit_users',
        'limit_documents',
        'plan_documents', 
        'locked', 
        'establishments_limit', 
        'establishments_unlimited', 
        
        'sales_limit', 
        'sales_unlimited', 
        'include_sale_notes_sales_limit', 
        'include_sale_notes_limit_documents', 
    ];


    protected $casts = [
        'establishments_unlimited' => 'boolean',
        'establishments_limit' => 'int',
        'sales_unlimited' => 'boolean',
        'sales_limit' => 'float',
        'include_sale_notes_sales_limit' => 'boolean',
        'include_sale_notes_limit_documents' => 'boolean',
    ];


    public function setPlanDocumentsAttribute($value)
    {
        $this->attributes['plan_documents'] = (is_null($value))?null:json_encode($value);
    }

    public function getPlanDocumentsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }


    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    
    /**
     *
     * @return bool
     */
    public function isEstablishmentsUnlimited()
    {
        return $this->establishments_unlimited;
    }

    
    /**
     *
     * @return bool
     */
    public function isSalesUnlimited()
    {
        return $this->sales_unlimited;
    }
    

    /**
     *
     * @return bool
     */
    public function includeSaleNotesSalesLimit()
    {
        return $this->include_sale_notes_sales_limit;
    }
    

    /**
     *
     * @return bool
     */
    public function includeSaleNotesLimitDocuments()
    {
        return $this->include_sale_notes_limit_documents;
    }
    
}
