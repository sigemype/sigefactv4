<?php

namespace App\CoreFacturalo\Requests\Web\Validation;

use Exception;

class DispatchValidation
{
    public static function validation($inputs)
    {
        $series = Functions::findSeries($inputs);
        if (!$series) throw new Exception("La serie no fue encontrada.");

        return $inputs;
    }
}
