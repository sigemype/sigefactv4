<?php

namespace Modules\Dispatch\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransportCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            return [
                'id' => $row->id,
                'plate_number' => $row->plate_number,
                'model' => $row->model,
                'brand' => $row->brand,
                'is_default' => $row->is_default?'SI':'',
                'is_active' => $row->is_active,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
