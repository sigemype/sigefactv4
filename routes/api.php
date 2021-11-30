<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);
if ($hostname) {
    Route::domain($hostname->fqdn)->group(function () {

        Route::post('login', 'Tenant\Api\MobileController@login');

        //reportes caja
        Route::get('cash/report/products/{cash}', 'Tenant\Api\MobileController@report_products');
        Route::get('cash/report/report-ticket/{cash}', 'Tenant\Api\MobileController@reportTicket');
        Route::get('cash/report/report-a4/{cash}', 'Tenant\Api\MobileController@reportA4');
        Route::get('cash/report/income-summary/{cash}', 'Tenant\Api\MobileController@pdf');
        
        Route::middleware(['auth:api', 'locked.tenant'])->group(function () {

            // caja
        Route::get('cash/open/{value}', 'Tenant\Api\MobileController@opencash');
        Route::get('cash/check', 'Tenant\Api\MobileController@opening_cash_check');
        Route::get('cash/records', 'Tenant\Api\MobileController@records');
        Route::get('cash/email', 'Tenant\Api\MobileController@cashemail');
        Route::get('cash/close/{cash}', 'Tenant\Api\MobileController@close');

        
        Route::post('cash/report/email', 'Tenant\Api\MobileController@email');





            //MOBILE 
            Route::get('document/series', 'Tenant\Api\MobileController@getSeries');
            Route::get('document/documents_count', 'Tenant\Api\MobileController@documents_count');
            Route::get('document/paymentmethod', 'Tenant\Api\MobileController@getPaymentmethod');
            Route::get('document/tables', 'Tenant\Api\MobileController@tables');
            Route::get('document/customers', 'Tenant\Api\MobileController@customers');
            Route::get('document/customers/{id}', 'Tenant\Api\MobileController@customers_details');
            Route::post('document/email', 'Tenant\Api\MobileController@document_email');
            Route::post('sale-note', 'Tenant\Api\SaleNoteController@store');
            Route::get('sale-note/series', 'Tenant\Api\SaleNoteController@series');
            Route::get('sale-note/lists', 'Tenant\Api\SaleNoteController@lists');
            Route::post('item', 'Tenant\Api\MobileController@item');
            Route::get('items/details/{id}', 'Tenant\Api\MobileController@item_details');
            Route::post('items/{id}/update', 'Tenant\Api\MobileController@updateItem');
            Route::post('item/upload', 'Tenant\Api\MobileController@upload');
            Route::post('person', 'Tenant\Api\MobileController@person');
            Route::get('document/search-items', 'Tenant\Api\MobileController@searchItems');
            Route::get('document/search-customers', 'Tenant\Api\MobileController@searchCustomers');
            Route::post('sale-note/email', 'Tenant\Api\SaleNoteController@email');
            Route::post('sale-note/{id}/generate-cpe', 'Tenant\Api\SaleNoteController@generateCPE');

            Route::get('report/{year}/{month}/{day}', 'Tenant\Api\MobileController@report');

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

    });

}
