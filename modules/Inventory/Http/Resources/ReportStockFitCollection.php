<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection; 


class ReportStockFitCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            return  $row->getRowResourceReportStock();
        });
    }
  
}
