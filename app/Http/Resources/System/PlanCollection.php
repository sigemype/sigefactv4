<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PlanCollection extends ResourceCollection
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
                'name' => $row->name,
                'pricing' => $row->pricing,
                'limit_users' => $row->limit_users,
                'limit_documents' => $row->limit_documents,
                // 'plan_documents' => $row->plan_documents, 
                'locked' => (bool) $row->locked, 
                
                'establishments_limit' => $row->establishments_limit,
                'establishments_unlimited' => $row->establishments_unlimited,

                'sales_limit' => $row->sales_limit,
                'sales_unlimited' => $row->sales_unlimited,
                'include_sale_notes_sales_limit' => $row->include_sale_notes_sales_limit,
            ];
        });
    }
}