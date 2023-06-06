<?php

namespace App\Traits;

use Hyn\Tenancy\Contracts\Hostname;
use Hyn\Tenancy\Environment;
use App\Models\System\{
    Client,
    Plan
};
use App\Models\Tenant\{
    Configuration,
    SaleNote
};
use Exception;
use Modules\Document\Helpers\DocumentHelper;
use Modules\Dashboard\Helpers\DashboardData;


trait LockedEmissionTrait
{

    /**
     * 
     * Obtener registro asociado al tenant desde la tabla hostname de system
     * 
     * @return Hostname
     */
    public function getTenantHostname()
    {
        return app(Environment::class)->hostname();
    }


    /**
     *
     * @param  string $column
     * @return mixed
     */
    public function getConfigurationColumn($column)
    {
        return Configuration::getRecordIndividualColumn($column);
    }

        
    /**
     *
     * @param  int $hostname_id
     * @return Client
     */
    public function getClientByHostname($hostname_id)
    {
        return Client::where('hostname_id', $hostname_id)
                    ->whereFilterWithOutRelations()
                    ->select([
                        'id',
                        'name',
                        'plan_id'
                    ])
                    ->first();
    }

    
    /**
     *
     * @param  array $columns
     * @param  int $plan_id
     * @return Plan
     */
    public function getPlan($columns, $plan_id)
    {
        return Plan::select($columns)->find($plan_id);
    }
    
    
    /**
     * 
     * Buscar cliente y obtener plan
     *
     * @param  array $columns
     * @return Plan
     */
    public function getClientPlan($columns)
    {
        $tenant_hostname = $this->getTenantHostname();
        $client = $this->getClientByHostname($tenant_hostname->id);

        return $this->getPlan($columns, $client->plan_id);
    }

    
    /**
     *
     * @param  string $model
     * @return int
     */
    public function getQuantityByModel($model)
    {
        return $model::count();
    }

    
    /**
     *
     * @param  string $message
     * @return void
     */
    public function throwException($message)
    {
        throw new Exception($message);
    }

    
    /**
     * 
     * Validar el limite de ventas mensual, cpe y nv
     *
     * @return array
     */
    public function exceedSalesLimit($type = 'document')
    {
        //fecha de inicio del ciclo de facturacion
        $start_billing_cycle = DocumentHelper::getStartBillingCycleFromSystem();
        
        if($start_billing_cycle)
        {
            $plan = $this->getClientPlan(['id', 'name', 'sales_limit', 'sales_unlimited', 'include_sale_notes_sales_limit']);

            if(!$plan->isSalesUnlimited())
            {
                if($type === 'document' || ($type === 'sale-note' && $plan->includeSaleNotesSalesLimit()))
                {
                    //obtener fecha inicio y fin
                    $start_end_date = DocumentHelper::getStartEndDateForFilterDocument($start_billing_cycle);
                    $start_date = $start_end_date['start_date']->format('Y-m-d');
                    $end_date = $start_end_date['end_date']->format('Y-m-d');
    
                    //obtener totales
                    $totals = $this->getTotalsDocumentSaleNote($start_date, $end_date, $plan->includeSaleNotesSalesLimit());
    
                    if($totals['total'] > $plan->sales_limit)
                    {
                        return $this->getResponse(true, 'Ha superado el límite de ventas permitido.');
                    }
                }
            }
        }

        return $this->getResponse(false);
    }

    
    /**
     *
     * @param  string $start_date
     * @param  string $end_date
     * @param  bool $include_sale_notes
     * @return array
     */
    public function getTotalsDocumentSaleNote($start_date, $end_date, $include_sale_notes = false)
    {
        $dashboard_data = new DashboardData();
                
        //total cpe
        $document_totals = $dashboard_data->document_totals_globals($start_date, $end_date);

        // total nota venta
        $sale_note_totals = 0;

        if($include_sale_notes)
        {
            $sale_note_totals = $dashboard_data->sale_note_totals_global($start_date, $end_date);
        }

        $total = $document_totals + $sale_note_totals;

        return [
            'sale_note_totals' => $sale_note_totals,
            'document_totals' => $document_totals,
            'total' => $total,
        ];
    }

    
    /**
     * 
     * Obtener cantidad de notas de venta
     * 
     * Usado en: 
     * App\Http\Controllers\System\ClientController - Lista de clientes
     * Modules\Document\Helpers\DocumentHelper
     *
     * @param  string $start_date
     * @param  string $end_date
     * @return int
     */
    public function getQuantitySaleNotesByDates($start_date, $end_date)
    {
        return SaleNote::whereBetween('date_of_issue', [$start_date, $end_date])->count();
    }


    /**
     *
     * @param  bool $success
     * @param  string $message
     * @return array
     */
    public function getResponse($success, $message = null)
    {
        return [
            'success' => $success,
            'message' => $message,
        ];
    }

}
