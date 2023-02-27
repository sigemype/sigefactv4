<?php

namespace Modules\Dispatch\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DispatcherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'identity_document_type_id' => $this->identity_document_type_id,
            'number' => $this->number,
            'name' => $this->name,
            'address' => $this->address,
            'number_mtc' => $this->number_mtc,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ];
    }
}
