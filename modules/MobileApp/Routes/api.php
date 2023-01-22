<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname)
{
    Route::domain($hostname->fqdn)->group(function () {

        Route::middleware(['auth:api', 'locked.tenant'])->group(function () {

            Route::get('document-print-pdf/{model}/{external_id}/{format}/{extend_pdf_height?}', 'Api\DownloadController@documentPrintPdf');

            Route::get('document-print-pdf-text/{model}/{external_id}/{format}', 'Api\DownloadController@documentPrintText');
            // Route::post('document-print-pdf-upload', 'Api\DownloadController@documentPrintPdfUpload');

            Route::get('categories-records', 'Api\CategoryController@records');
            Route::get('brands-records', 'Api\BrandController@records');


            Route::prefix('app-configurations')->group(function () {

                Route::get('record', 'Api\AppConfigurationController@record');
                Route::post('', 'Api\AppConfigurationController@store');
                Route::get('initial-settings', 'Api\AppConfigurationController@getInitialSettings');

            });


            Route::prefix('items')->group(function () {

                Route::get('records-sale', 'Api\ItemController@recordsSale');
                Route::get('table/{table}', 'Api\ItemController@table');
                Route::get('tables', 'Api\ItemController@tables');
                Route::post('update', 'Api\ItemController@update');
                Route::get('records', 'Api\ItemController@records');
                Route::get('record/{id}', 'Api\ItemController@record');
                Route::post('upload-temp-image', 'Api\ItemController@uploadTempImage');
                Route::delete('{id}', 'Api\ItemController@destroy');
                Route::get('change-active/{id}/{active}', 'Api\ItemController@changeActive');
                Route::get('change-favorite/{id}/{favorite}', 'Api\ItemController@changeFavorite');

            });


            Route::prefix('documents')->group(function () {
                Route::post('validate-document', 'Api\ValidateDocumentController@validateDocument');
                Route::get('notifications', 'Api\DocumentController@getNotifications');
                Route::get('records', 'Api\DocumentController@records');
                Route::get('record/{id}', 'Api\DocumentController@record');
                Route::get('tables', 'Api\DocumentController@tables');
                Route::get('tables-sale-detail', 'Api\DocumentController@getTablesSaleDetail');
                Route::get('tables-sale-payment', 'Api\DocumentController@getTablesSalePayment');
                Route::get('table/{table}', 'Api\DocumentController@table');
                Route::post('send-document-to-whatsapp', 'Api\DocumentController@sendDocumentToWhatsapp');
            });


            Route::prefix('persons')->group(function () {

                Route::get('{type}/records', 'Api\PersonController@records');
                Route::get('default-customer/{document_type_id?}', 'Api\PersonController@getDefaultCustomer');
                Route::get('change-enabled/{id}/{enabled}', 'Api\PersonController@changeEnabled');
                Route::delete('{id}', 'Api\PersonController@destroy');
                Route::get('record/{id}', 'Api\PersonController@record');
                Route::post('update', 'Api\PersonController@update');

            });

            Route::prefix('cash')->group(function () {

                Route::get('records', 'Api\CashController@records');
                Route::get('check-open-cash', 'Api\CashController@checkOpenCash');
                Route::get('record/{id}', 'Api\CashController@record');
                Route::delete('{id}', 'Api\CashController@destroy');
                Route::post('', 'Api\CashController@store');
                Route::get('close/{cash}', 'Api\CashController@close');
                Route::post('email', 'Api\CashController@email');

                Route::get('general-report/{cash}/{format?}', 'Api\CashController@generalReport');
                Route::get('product-report/{cash}', 'Api\CashController@productReport');
                Route::get('income-egress-report/{cash}', 'Api\CashController@incomeEgressReport');
                Route::get('income-summary-report/{cash}', 'Api\CashController@incomeSummaryReport');

                Route::post('store-cash-document', 'Api\CashController@storeCashDocument');

            });


            Route::prefix('reports')->group(function () {
                Route::get('filters', 'Api\ReportController@filters');
                Route::post('general-sale', 'Api\ReportController@reportGeneralSale');
            });

        });
    });
}
