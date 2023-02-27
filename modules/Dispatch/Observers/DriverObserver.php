<?php

namespace Modules\Dispatch\Observers;

use Modules\Dispatch\Models\Driver;

class DriverObserver
{
    public function saving(Driver $driver)
    {
        $driver->license = func_str_to_upper_utf8($driver->license);
    }
}
