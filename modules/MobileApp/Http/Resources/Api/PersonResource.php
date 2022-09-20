<?php

namespace Modules\MobileApp\Http\Resources\Api;

use App\Models\Tenant\Configuration;
use App\Models\Tenant\ItemSupply;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->getApiRowResource();
    }
}
