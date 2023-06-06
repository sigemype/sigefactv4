<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class DispatchSaleNoteCollection extends ResourceCollection
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
                'type' => $row->status,
                'date_dispatch' => Carbon::parse($row->date_dispatch)->format('Y/m/d'),
                'time_dispatch' => Carbon::parse($row->time_dispatch)->format('H:i'),
                'person_pick' => $row->person_pick,
                'reference' => $row->reference,
                'person_dispatch' => $row->person_dispatch,
                'status' => $row->status,
            ];
        });
    }
}