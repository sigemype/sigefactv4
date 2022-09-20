<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Series;


class UserDefaultDocumentType extends ModelTenant
{

    protected $fillable = [
        'user_id',
        'document_type_id',
        'series_id',
    ];


    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * @return BelongsTo
     */
    public function document_type()
    {
        return $this->belongsTo(DocumentType::class);
    }


    /**
     * @return BelongsTo
     */
    public function series()
    {
        return $this->belongsTo(Series::class, 'series_id');
    }

    public function getDataMultipleDocumentType()
    {
        return [
            'user_id' => $this->user_id,
            'document_type_id' => $this->document_type_id,
            'series_id' => $this->series_id,
            'default_series' => [],
        ];
    }

}
