<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\DocumentType;
use Modules\Document\Models\SeriesConfiguration;

class Series extends ModelTenant
{
    protected $table = 'series';

    protected $fillable = [
        'establishment_id',
        'document_type_id',
        'number',
        'contingency',
    ];

    public function establishment() {
        return $this->belongsTo(Establishment::class);
    }

    public function document_type() {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function setNumberAttribute($value) {
        $this->attributes['number'] = strtoupper($value);
    }

    public function documents() {
        return $this->hasMany(Document::class, 'series', 'number');
    }

    public function series_configurations()
    {
        return $this->hasOne(SeriesConfiguration::class);
    }


    /**
     * @param int $establishment_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterEstablishment($query,$establishment_id = 0){
        return $query->where('establishment_id',$establishment_id);
    }
    /**
     * @param int $document_type_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterDocumentType($query,$document_type_id = 0){
        return $query->where('document_type_id',$document_type_id);
    }

}
