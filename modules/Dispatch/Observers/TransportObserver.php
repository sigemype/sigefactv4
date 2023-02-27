<?php

namespace Modules\Dispatch\Observers;

use Modules\Dispatch\Models\Transport;

class TransportObserver
{
    public function saving(Transport $record)
    {
        $record->plate_number = func_str_to_upper_utf8(str_replace('-', '', $record->plate_number));
        $record->model = func_str_to_upper_utf8($record->model);
        $record->brand = func_str_to_upper_utf8($record->brand);
    }
}
