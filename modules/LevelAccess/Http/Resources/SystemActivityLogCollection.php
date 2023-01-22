<?php

namespace Modules\LevelAccess\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\LevelAccess\Models\SystemActivityLog;


class SystemActivityLogCollection extends ResourceCollection
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
            return $row->getRowResourceAccess();
        });

    }
    
}
