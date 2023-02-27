<?php

namespace Modules\Dispatch\Models;

use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\ModelTenant;

class Dispatcher extends ModelTenant
{
    protected $with = ['identity_document_type'];

    protected $fillable = [
        'identity_document_type_id',
        'number',
        'name',
        'address',
        'number_mtc',
        'is_default',
        'is_active'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function identity_document_type()
    {
        return $this->belongsTo(IdentityDocumentType::class, 'identity_document_type_id');
    }

}
