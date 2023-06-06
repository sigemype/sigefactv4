<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'name' => $this->name,
            'pricing' => $this->pricing,
            'limit_documents' => $this->limit_documents,
            'limit_users' => $this->limit_users,
            // 'plan_documents' => $this->plan_documents,
            'plan_documents' => [],
            'locked' => $this->locked,

            'establishments_limit' => $this->establishments_limit,
            'establishments_unlimited' => $this->establishments_unlimited,

            'sales_limit' => $this->sales_limit,
            'sales_unlimited' => $this->sales_unlimited,
            'include_sale_notes_sales_limit' => $this->include_sale_notes_sales_limit,
            'include_sale_notes_limit_documents' => $this->include_sale_notes_limit_documents,
            
        ];
    }
}