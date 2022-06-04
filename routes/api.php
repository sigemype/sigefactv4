<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);
if ($hostname) {
    Route::domain($hostname->fqdn)->group(function () {

        Route::post('login', 'Tenant\Api\AppController@login');

        //reportes caja  
        Route::get('cash/report/products/{cash}', 'Tenant\Api\AppController@report_products');
        Route::get('cash/report/report-ticket/{cash}', 'Tenant\Api\AppController@reportTicket');
        Route::get('cash/report/report-a4/{cash}', 'Tenant\Api\AppController@reportA4');
        Route::get('cash/report/income-summary/{cash}', 'Tenant\Api\AppController@pdf');
        
        Route::middleware(['auth:api', 'locked.tenant'])->group(function () {

        //conteo de documentos
        Route::get('document/documents_count', 'Tenant\Api\AppController@documents_count');

        //listar vendedores
        Route::get('sellers/list', 'Tenant\Api\AppController@sellers');

        //detlles de clientes
        Route::get('document/customers/{id}', 'Tenant\Api\AppController@customers_details');

        //detalles de productos 
        Route::get('items/details/{id}', 'Tenant\Api\AppController@item_details');

            // caja
        Route::get('cash/open/{value}', 'Tenant\Api\AppController@opencash');
        Route::get('cash/check', 'Tenant\Api\AppController@opening_cash_check');
        Route::get('cash/records', 'Tenant\Api\AppController@records');
        Route::get('cash/email', 'Tenant\Api\AppController@cashemail');
        Route::get('cash/close/{cash}', 'Tenant\Api\AppController@close');

        //anular / eliminar productos
        Route::delete('item/delete/{item}', 'Tenant\Api\AppController@destroy_item');
        Route::get('item/disable/{item}', 'Tenant\Api\AppController@disable');
        Route::get('item/enable/{item}', 'Tenant\Api\AppController@enable');
        Route::get('items', 'Tenant\Api\AppController@items');
        Route::get('items/search-items', 'Tenant\Api\AppController@ItemsSearch');

        //anular / eliminar clientes
        Route::get('customers', 'Tenant\Api\AppController@customersAdmin');
        Route::get('customer/search', 'Tenant\Api\AppController@CustomersSearch');
        Route::get('customer/enabled/{type}/{person}', 'Tenant\Api\AppController@CustomerEnable');
        Route::delete('customer/delete/{person}', 'Tenant\Api\AppController@destroy_customer');

        Route::get('documents/type_status', 'Tenant\Api\AppController@typeStatus');        
        Route::get('documents/filter/{state}/{type_doc}', 'Tenant\Api\AppController@filterCPE');
        Route::post('cash/report/email', 'Tenant\Api\AppController@email');

        //validador de cpe nuevo loretosoft
        Route::post('services/validate_cpe_loretosoft', 'Tenant\Api\AppController@validateCpe_2');

        //rporte por mes y año
        Route::get('report/{year}/{month}/{day}/{method}/{type_user}/{user_id}', 'Tenant\Api\AppController@report');

            //MOBILE 
            Route::get('document/series', 'Tenant\Api\AppController@getSeries');
            Route::get('document/paymentmethod', 'Tenant\Api\AppController@getPaymentmethod');
            Route::get('document/tables', 'Tenant\Api\AppController@tables');
            Route::get('document/customers', 'Tenant\Api\AppController@customers');
            Route::post('document/email', 'Tenant\Api\AppController@document_email');
            Route::post('sale-note', 'Tenant\Api\SaleNoteController@store');
            Route::get('sale-note/series', 'Tenant\Api\SaleNoteController@series');
            Route::get('sale-note/lists', 'Tenant\Api\SaleNoteController@lists');
            Route::post('item', 'Tenant\Api\AppController@item');
            Route::post('items/{id}/update', 'Tenant\Api\AppController@updateItem');
            Route::post('item/upload', 'Tenant\Api\AppController@upload');
            Route::post('person', 'Tenant\Api\AppController@person');
            Route::get('document/search-items', 'Tenant\Api\AppController@searchItems');
            Route::get('document/search-customers', 'Tenant\Api\AppController@searchCustomers');
            Route::post('sale-note/email', 'Tenant\Api\SaleNoteController@email');
            Route::post('sale-note/{id}/generate-cpe', 'Tenant\Api\SaleNoteController@generateCPE');

            Route::get('report', 'Tenant\Api\AppController@report');

            Route::post('documents', 'Tenant\Api\DocumentController@store');
            Route::get('documents/lists', 'Tenant\Api\DocumentController@lists');
            Route::get('documents/lists/{startDate}/{endDate}', 'Tenant\Api\DocumentController@lists');
            Route::post('documents/updatedocumentstatus', 'Tenant\Api\DocumentController@updatestatus');
            Route::post('summaries', 'Tenant\Api\SummaryController@store');
            Route::post('voided', 'Tenant\Api\VoidedController@store');
            Route::post('retentions', 'Tenant\Api\RetentionController@store');
            Route::post('dispatches', 'Tenant\Api\DispatchController@store');
            Route::post('documents/send', 'Tenant\Api\DocumentController@send');
            Route::post('summaries/status', 'Tenant\Api\SummaryController@status');
            Route::post('voided/status', 'Tenant\Api\VoidedController@status');
            Route::get('services/ruc/{number}', 'Tenant\Api\ServiceController@ruc');
            Route::get('services/dni/{number}', 'Tenant\Api\ServiceController@dni');
            Route::post('services/consult_cdr_status', 'Tenant\Api\ServiceController@consultCdrStatus');
            Route::post('services/validate_cpe', 'Tenant\Api\ServiceController@validateCpe');
            Route::post('perceptions', 'Tenant\Api\PerceptionController@store');

            Route::post('documents_server', 'Tenant\Api\DocumentController@storeServer');
            Route::get('document_check_server/{external_id}', 'Tenant\Api\DocumentController@documentCheckServer');

            //liquidacion de compra
            Route::post('purchase-settlements', 'Tenant\Api\PurchaseSettlementController@store');

            //Pedidos
            Route::get('orders', 'Tenant\Api\OrderController@records');
            Route::post('orders', 'Tenant\Api\OrderController@store');

            //Company
            Route::get('company', 'Tenant\Api\CompanyController@record');

            // Cotizaciones
            Route::get('quotations/list', 'Tenant\Api\QuotationController@list');
            Route::post('quotations', 'Tenant\Api\QuotationController@store');

            //Caja
            Route::post('cash/restaurant', 'Tenant\Api\CashController@storeRestaurant');

        });
        Route::get('documents/search/customers', 'Tenant\DocumentController@searchCustomers');

        // Route::post('services/consult_status', 'Tenant\Api\ServiceController@consultStatus');
        Route::post('documents/status', 'Tenant\Api\ServiceController@documentStatus');

        Route::get('sendserver/{document_id}/{query?}', 'Tenant\DocumentController@sendServer');
        Route::post('configurations/generateDispatch', 'Tenant\ConfigurationController@generateDispatch');
    });
} else {
    Route::domain(env('APP_URL_BASE'))->group(function () {

        //reseller
        Route::post('reseller/detail', 'System\Api\ResellerController@resellerDetail');
        Route::post('reseller/lockedAdmin', 'System\Api\ResellerController@lockedAdmin');
        Route::post('reseller/lockedTenant', 'System\Api\ResellerController@lockedTenant');

        Route::middleware(['auth:system_api'])->group(function () {
            Route::get('restaurant/partner/list', 'System\Api\RestaurantPartnerController@list');
            Route::post('restaurant/partner/store', 'System\Api\RestaurantPartnerController@store');
            Route::post('restaurant/partner/search', 'System\Api\RestaurantPartnerController@search');
        });
    });

}
