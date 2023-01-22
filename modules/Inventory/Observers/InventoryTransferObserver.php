<?php

namespace Modules\Inventory\Observers;

use App\Models\Tenant\Company;
use Illuminate\Support\Str;
use Modules\Inventory\Models\Guide;
use Modules\Inventory\Models\InventoryTransfer;

class InventoryTransferObserver
{
    public function creating(InventoryTransfer $inventory_transfer)
    {
        $company = Company::query()->first();

        $inventory_transfer->user_id = auth()->id();
        $inventory_transfer->external_id = Str::uuid()->toString();
        $inventory_transfer->soap_type_id = $company->soap_type_id;

        $number = $this->getNumberDocument($inventory_transfer);
        $filename = join('-', [$company->number, $inventory_transfer->document_type_id, $inventory_transfer->series, $number]);
        $inventory_transfer->number = $number;
        $inventory_transfer->filename = $filename;
    }

    private function getNumberDocument($inventory_transfer)
    {
        if ($inventory_transfer->number === '#') {
            $record = InventoryTransfer::query()
                ->select('number')
                ->where('soap_type_id', $inventory_transfer->soap_type_id)
                ->where('document_type_id', $inventory_transfer->document_type_id)
                ->where('series', $inventory_transfer->series)
                ->orderBy('number', 'desc')
                ->first();

            return ($record) ? $record->number + 1 : 1;
        }

        return $inventory_transfer->number;
    }
}
