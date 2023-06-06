<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tenant\Document;
use App\Models\Tenant\User;

use App\Models\Tenant\Configuration;
use Exception;
use Modules\Document\Helpers\DocumentHelper;
use Illuminate\Support\Facades\Log;

use App\Models\Tenant\SaleNote;
use App\Models\Tenant\{
    Establishment
};
use App\Traits\LockedEmissionTrait;


class LockedEmissionProvider extends ServiceProvider
{

    use LockedEmissionTrait;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->locked_emission();
        $this->locked_users();
        $this->update_quantity_documents();

        $this->lockedCreateEstablishments();

        $this->lockedEmissionSaleNotes();
    }


    private function update_quantity_documents()
    {
        Document::created(function ($document) {
            
            $configuration = Configuration::first();
            $configuration->quantity_documents++; 
            $configuration->save();
        
        }); 
    }
    
    
    /**
     * 
     * Controlar limite de ventas y cantidad de documentos mensual
     *
     * @return void
     */
    private function locked_emission()
    {

        Document::created(function ($document) {

            $configuration = Configuration::firstOrFail();

            if($configuration->locked_emission)
            {
                $exceed_limit = (new DocumentHelper)->exceedLimitDocuments();
                if($exceed_limit['success']) throw new Exception($exceed_limit['message']);
            }


            //limite de ventas mensual
            if($configuration->isRestrictSalesLimit())
            {
                $exceed_sales_limit = $this->exceedSalesLimit();
                if($exceed_sales_limit['success']) $this->throwException($exceed_sales_limit['message']);
            }

            // $configuration = Configuration::first();
            // // $quantity_documents = Document::count();
            // $quantity_documents = $configuration->quantity_documents;

            // if($configuration->locked_emission && $configuration->limit_documents !== 0){
            //     if($quantity_documents >= $configuration->limit_documents)
            //         throw new Exception("Ha superado el límite permitido para la emisión de comprobantes");

            // }

        });

    }

    
    /**
     * 
     * Controlar limite de ventas y cantidad de documentos mensual
     *
     * @return void
     */
    private function lockedEmissionSaleNotes()
    {
        SaleNote::created(function ($sale_note) {
            
            if($this->getConfigurationColumn('locked_emission'))
            {
                $exceed_limit = (new DocumentHelper)->exceedLimitDocuments('sale-note');
                if($exceed_limit['success']) $this->throwException($exceed_limit['message']);
            }

            if($this->getConfigurationColumn('restrict_sales_limit'))
            {
                $exceed_sales_limit = $this->exceedSalesLimit('sale-note');
                if($exceed_sales_limit['success']) $this->throwException($exceed_sales_limit['message']);
            }

        });
    }


    private function locked_users()
    {

        User::creating(function ($document) {
            
            
            $configuration = Configuration::first();

            $quantity_users = User::count();

            if($configuration->locked_users &&  $configuration->plan->limit_users !== 0){

                if($quantity_users >= $configuration->plan->limit_users )
                {
                    throw new Exception("Ha superado el límite permitido para la creación de usuarios");
                }
            }

        });
    }

    private function update_quantity_sales_notes()
    {
        SaleNote::created(function ($document) {
            
            $configuration = Configuration::first();
            $configuration->quantity_sales_notes++; 
            $configuration->save();
        
        }); 
    }
    
    
    /**
     * 
     * Validar creacion de establecimientos en base al plan
     *
     * @return void
     */
    private function lockedCreateEstablishments()
    {
        Establishment::creating(function ($establishment) {
            
            $locked_create_establishments = $this->getConfigurationColumn('locked_create_establishments');

            if($locked_create_establishments)
            {
                $plan = $this->getClientPlan(['id', 'name', 'establishments_limit', 'establishments_unlimited']);

                if(!$plan->isEstablishmentsUnlimited())
                {
                    $establishments_quantity = $this->getQuantityByModel(Establishment::class);

                    if($establishments_quantity >= $plan->establishments_limit) $this->throwException('Ha superado el límite permitido para la creación de establecimientos');
                }
            }
        });
    }


}
