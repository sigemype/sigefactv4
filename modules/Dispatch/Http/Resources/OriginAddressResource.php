<?php

namespace Modules\Dispatch\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OriginAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'location_id' => $this->location_id,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ];
    }
}
