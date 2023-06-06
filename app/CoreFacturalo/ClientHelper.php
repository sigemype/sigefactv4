<?php

namespace App\CoreFacturalo;

use App\Traits\LockedEmissionTrait;


class ClientHelper
{
    use LockedEmissionTrait;


    /**
     * Obtener total de ventas mensual
     *
     * Usado en:
     * ClientController - Lista de clientes
     *
     * @param  string $start_date
     * @param  string $end_date
     * @param  Plan $plan
     * @return float
     */
    public function getSalesTotal($start_date, $end_date, $plan)
    {
        $totals = $this->getTotalsDocumentSaleNote($start_date, $end_date, $plan->includeSaleNotesSalesLimit());

        return $totals['total'];
    }

}
