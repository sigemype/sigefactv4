<?php

namespace Modules\Sale\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AgentCollection extends ResourceCollection
{

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            return $row->getRowResource();
        });
    }
    
}
