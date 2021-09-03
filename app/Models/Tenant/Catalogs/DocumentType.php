<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class DocumentType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_document_types";
    public $incrementing = false;
    protected $fillable = [
        'active',
        'short',
        'description'
    ];


    /**
     * @return mixed
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * @param mixed $active
     *
     * @return DocumentType
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShort() {
        return $this->short;
    }

    /**
     * @param mixed $short
     *
     * @return DocumentType
     */
    public function setShort($short) {
        $this->short = $short;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return DocumentType
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyActive($query){
        return $query->where('active',1);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyAvaibleDocuments($query){
        return $query->OnlyActive()->wherein('id',['01', '03', '07', '08', '09', '20','40', '80']);
    }

    /**
     * Devuelve los elementos activos para compras
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDocumentsActiveToPurchase($query){
        return $query->OnlyActive()->wherein('id', ['01', '02', '03', 'GU75', 'NE76', '14','07', '08']);
    }


}
