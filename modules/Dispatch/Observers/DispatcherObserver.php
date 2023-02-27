<?php

namespace Modules\Dispatch\Observers;

use Modules\Dispatch\Models\Dispatcher;

class DispatcherObserver
{
    public function saving(Dispatcher $record)
    {
        $record->number_mtc = func_str_to_upper_utf8($record->number_mtc);
    }
}
