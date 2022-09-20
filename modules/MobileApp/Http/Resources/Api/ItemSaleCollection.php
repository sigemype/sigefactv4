<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Inventory\Models\Warehouse;


class ItemSaleCollection extends ResourceCollection
{
    
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

        return $this->collection->transform(function($row, $key) use($warehouse){
            return $row->getSaleApiRowResource($warehouse);
        });

    }
    
}
