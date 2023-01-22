<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * @package App\Http\Resources\Tenant
 */
class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request) {
        $modules = $this->getCurrentModuleByTenant()
                        ->pluck('module_id')
                        ->toArray();
        $levels = $this->getCurrentModuleLevelByTenant()
                       ->pluck('module_level_id')
                       ->toArray();


        return [
            'id'               => $this->id,
            'email'            => $this->email,
            'name'             => $this->name,
            'api_token'        => $this->api_token,
            'establishment_id' => $this->establishment_id,
            'type'             => $this->type,
            'zone_id'             => $this->zone_id,
            'modules'          => $modules,
            'levels'           => $levels,
            'locked'           => (bool)$this->locked,
            'document_id'      => $this->document_id,
            'permission_edit_cpe' => $this->permission_edit_cpe,
            'recreate_documents' => $this->recreate_documents,
            'series_id'        => ($this->series_id == 0) ? null : $this->series_id,
            'create_payment' => $this->create_payment,
            'delete_payment' => $this->delete_payment,
            'edit_purchase' => $this->edit_purchase,
            'annular_purchase' => $this->annular_purchase,
            'delete_purchase' => $this->delete_purchase,

            
            'identity_document_type_id' => $this->identity_document_type_id,
            'number' => $this->number,
            'address' => $this->address,
            'names' => $this->names,
            'last_names' => $this->last_names,
            'personal_email' => $this->personal_email,
            'corporate_email' => $this->corporate_email,
            'personal_cell_phone' => $this->personal_cell_phone,
            'corporate_cell_phone' => $this->corporate_cell_phone,
            'date_of_birth' => $this->date_of_birth,
            'contract_date' => $this->contract_date,
            'position' => $this->position,
            'multiple_default_document_types' => $this->multiple_default_document_types,

            'photo_filename' => $this->photo_filename,
            'photo_temp_image' => $this->getPhotoForView(),
            'photo_temp_path' => null,
            'default_document_types' => $this->default_document_types->transform(function($row){
                return $row->getDataMultipleDocumentType();
            }),
            'permission_force_send_by_summary' => $this->permission_force_send_by_summary,
        ];
    }
}
