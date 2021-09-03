<?php

    use Illuminate\Support\Facades\Route;

    $current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

    if ($current_hostname) {
        Route::domain($current_hostname->fqdn)->group(function () {
            Route::middleware(['auth', 'redirect.module', 'locked.tenant'])->group(function () {


               Route::prefix('reports')->group(function () {

                    Route::get('data-table/persons/{type}', 'ReportController@dataTablePerson');
                    Route::get('data-table/items', 'ReportController@dataTableItem');


                    Route::prefix('purchases')->group(function () {
                        /**
                         * reports/purchases/
                         * reports/purchases/items
                         * reports/purchases/pdf
                         * reports/purchases/excel
                         * reports/purchases/filter
                         * reports/purchases/records
                         */
                        Route::get('/', 'ReportPurchaseController@index')->name('tenant.reports.purchases.index');
                        /** Nuevo */

                        Route::get('items', 'ReportPurchaseItemController@index')->name('tenant.reports.purchases.items.index');

                        /*
                         * reports/purchases/general_items/
                         * reports/purchases/general_items/records
                         * reports/purchases/general_items/excel
                         * reports/purchases/general_items/pdf
                         * reports/purchases/general_items/filter
                         */
                        Route::prefix('general_items')->group(function () {
                            Route::get('/', 'ReportPurchaseItemController@general_items')->name('tenant.reports.purchases.general_items.index');
                            Route::get('records', 'ReportPurchaseItemController@records');
                            Route::get('excel', 'ReportPurchaseItemController@general_items');
                            Route::get('pdf', 'ReportPurchaseItemController@general_items');
                            Route::get('filter', 'ReportPurchaseItemController@filter');

                        });
                        /** Nuevo */


                        Route::get('pdf', 'ReportPurchaseController@pdf')->name('tenant.reports.purchases.pdf');
                        Route::get('excel', 'ReportPurchaseController@excel')->name('tenant.reports.purchases.excel');
                        Route::get('filter', 'ReportPurchaseController@filter')->name('tenant.reports.purchases.filter');
                        Route::get('records', 'ReportPurchaseController@records')->name('tenant.reports.purchases.records');

                    });


                   /**
                    * /reports/sales
                    * /reports/sales/pdf
                    * /reports/sales/filter
                    * /reports/sales/excel
                    * /reports/sales/records
                    */
                   Route::prefix('sales')->group(function () {
                       Route::get('', 'ReportDocumentController@index')
                           ->name('tenant.reports.sales.index')
                            ->middleware('tenant.internal.mode');
                       Route::get('/pdf', 'ReportDocumentController@pdf')
                           ->name('tenant.reports.sales.pdf');
                       Route::get('/excel', 'ReportDocumentController@excel')
                           ->name('tenant.reports.sales.excel');
                       Route::get('/filter', 'ReportDocumentController@filter')
                            ->name('tenant.reports.sales.filter');
                       Route::get('/records', 'ReportDocumentController@records')
                            ->name('tenant.reports.sales.records');
                   });
                   /**
                    * /reports/sale-notes
                    * /reports/sale-notes/pdf
                    * /reports/sale-notes/excel
                    * /reports/sale-notes/filter
                    * /reports/sale-notes/records
                    */
                   Route::prefix('sale-notes')->group(function () {
                       Route::get('', 'ReportSaleNoteController@index')->name('tenant.reports.sale_notes.index');
                       Route::get('/pdf', 'ReportSaleNoteController@pdf')->name('tenant.reports.sale_notes.pdf');
                       Route::get('/excel', 'ReportSaleNoteController@excel')
                           ->name('tenant.reports.sale_notes.excel');
                       Route::get('/filter', 'ReportSaleNoteController@filter')
                           ->name('tenant.reports.sale_notes.filter');
                       Route::get('/records', 'ReportSaleNoteController@records')
                           ->name('tenant.reports.sale_notes.records');
                   });

                    Route::get('quotations', 'ReportQuotationController@index')
                         ->name('tenant.reports.quotations.index');
                    Route::get('quotations/pdf', 'ReportQuotationController@pdf')
                         ->name('tenant.reports.quotations.pdf');
                    Route::get('quotations/excel', 'ReportQuotationController@excel')
                         ->name('tenant.reports.quotations.excel');
                    Route::get('quotations/filter', 'ReportQuotationController@filter')
                         ->name('tenant.reports.quotations.filter');
                    Route::get('quotations/records', 'ReportQuotationController@records')
                         ->name('tenant.reports.quotations.records');

                    Route::get('cash', 'ReportCashController@index')->name('tenant.reports.cash.index');
                    Route::get('cash/pdf', 'ReportCashController@pdf')->name('tenant.reports.cash.pdf');
                    Route::get('cash/excel', 'ReportCashController@excel')->name('tenant.reports.cash.excel');
                    Route::get('cash/filter', 'ReportCashController@filter')->name('tenant.reports.cash.filter');
                    Route::get('cash/records', 'ReportCashController@records')->name('tenant.reports.cash.records');


                    Route::get('document-hotels', 'ReportDocumentHotelController@index')
                         ->name('tenant.reports.document_hotels.index');
                    Route::get('document-hotels/pdf', 'ReportDocumentHotelController@pdf')
                         ->name('tenant.reports.document_hotels.pdf');
                    Route::get('document-hotels/excel', 'ReportDocumentHotelController@excel')
                         ->name('tenant.reports.document_hotels.excel');
                    Route::get('document-hotels/filter', 'ReportDocumentHotelController@filter')
                         ->name('tenant.reports.document_hotels.filter');
                    Route::get('document-hotels/records', 'ReportDocumentHotelController@records')
                         ->name('tenant.reports.document_hotels.records');


                    Route::get('commercial-analysis', 'ReportCommercialAnalysisController@index')
                         ->name('tenant.reports.commercial_analysis.index');
                    Route::get('commercial-analysis/pdf', 'ReportCommercialAnalysisController@pdf')
                         ->name('tenant.reports.commercial_analysis.pdf');
                    Route::get('commercial-analysis/excel', 'ReportCommercialAnalysisController@excel')
                         ->name('tenant.reports.commercial_analysis.excel');
                    Route::get('commercial-analysis/filter', 'ReportCommercialAnalysisController@filter')
                         ->name('tenant.reports.commercial_analysis.filter');
                    Route::get('commercial-analysis/records', 'ReportCommercialAnalysisController@records')
                         ->name('tenant.reports.commercial_analysis.records');
                    Route::get('commercial-analysis/data_table', 'ReportCommercialAnalysisController@data_table');
                    Route::get('commercial-analysis/columns', 'ReportCommercialAnalysisController@columns');
                    Route::get('no_paid/excel', 'ReportUnpaidController@excel')->name('tenant.reports.no_paid.excel');

                    Route::get('document-detractions', 'ReportDocumentDetractionController@index')
                         ->name('tenant.reports.document_detractions.index');
                    Route::get('document-detractions/pdf', 'ReportDocumentDetractionController@pdf')
                         ->name('tenant.reports.document_detractions.pdf');
                    Route::get('document-detractions/excel', 'ReportDocumentDetractionController@excel')
                         ->name('tenant.reports.document_detractions.excel');
                    Route::get('document-detractions/filter', 'ReportDocumentDetractionController@filter')
                         ->name('tenant.reports.document_detractions.filter');
                    Route::get('document-detractions/records', 'ReportDocumentDetractionController@records')
                         ->name('tenant.reports.document_hotels.records');


                    Route::get('commissions', 'ReportCommissionController@index')
                         ->name('tenant.reports.commissions.index');
                    Route::get('commissions/pdf', 'ReportCommissionController@pdf')
                         ->name('tenant.reports.commissions.pdf');
                    Route::get('commissions/excel', 'ReportCommissionController@excel')
                         ->name('tenant.reports.commissions.excel');
                    Route::get('commissions/filter', 'ReportCommissionController@filter')
                         ->name('tenant.reports.commissions.filter');
                    Route::get('commissions/records', 'ReportCommissionController@records')
                         ->name('tenant.reports.commissions.records');

                    Route::get('customers', 'ReportCustomerController@index')->name('tenant.reports.customers.index');
                    Route::get('customers/excel', 'ReportCustomerController@excel')
                         ->name('tenant.reports.customers.excel');
                    Route::get('customers/filter', 'ReportCustomerController@filter')
                         ->name('tenant.reports.customers.filter');
                    Route::get('customers/records', 'ReportCustomerController@records')
                         ->name('tenant.reports.customers.records');

                    Route::get('items', 'ReportItemController@index')->name('tenant.reports.items.index');
                    Route::get('items/excel', 'ReportItemController@excel')->name('tenant.reports.items.excel');
                    Route::get('items/filter', 'ReportItemController@filter')->name('tenant.reports.items.filter');
                    Route::get('items/records', 'ReportItemController@records')->name('tenant.reports.items.records');

                    Route::prefix('order-notes-consolidated')->group(function () {
                        Route::get('', 'ReportOrderNoteConsolidatedController@index')
                             ->name('tenant.reports.order_notes_consolidated.index');
                        Route::get('pdf', 'ReportOrderNoteConsolidatedController@pdf');
                        // Route::get('/excel', 'ReportOrderNoteConsolidatedController@excel');
                        Route::get('filter', 'ReportOrderNoteConsolidatedController@filter');
                        Route::get('records', 'ReportOrderNoteConsolidatedController@records');
                        Route::get('totals-by-item', 'ReportOrderNoteConsolidatedController@totalsByItem');
                        Route::get('pdf-totals', 'ReportOrderNoteConsolidatedController@pdfTotals');
                        Route::get('excel-totals', 'ReportOrderNoteConsolidatedController@excelTotals');
                    });
                    Route::prefix('guides')->group(function () {
                        Route::get('general-items', 'ReportGuideController@index')->name('tenant.reports.guides.index');
                        Route::post('filter', 'ReportGuideController@filter');
                        Route::get('records', 'ReportGuideController@records');
                        Route::get('totals-by-item', 'ReportGuideController@totalsByItem');
                        Route::get('pdf', 'ReportGuideController@pdf');
                        Route::get('pdf-totals', 'ReportGuideController@pdfTotals');
                        Route::get('excel', 'ReportGuideController@excel');
                        Route::get('excel-totals', 'ReportGuideController@excelTotals');
                    });
                   /**
                    * reports/general-items/
                    * reports/general-items/excel
                    * reports/general-items/pdf
                    * reports/general-items/filter
                    * reports/general-items/records
                    */
                   Route::prefix('general-items')->group(function () {
                       Route::get('', 'ReportGeneralItemController@index')->name('tenant.reports.general_items.index');
                       Route::get('/excel', 'ReportGeneralItemController@excel');
                       Route::get('/pdf', 'ReportGeneralItemController@pdf');
                       Route::get('/filter', 'ReportGeneralItemController@filter');
                       Route::get('/records', 'ReportGeneralItemController@records');
                   });

                    Route::get('order-notes-general', 'ReportOrderNoteGeneralController@index')
                         ->name('tenant.reports.order_notes_general.index');
                    Route::get('order-notes-general/pdf', 'ReportOrderNoteGeneralController@pdf');
                    Route::get('order-notes-general/filter', 'ReportOrderNoteGeneralController@filter');
                    Route::get('order-notes-general/records', 'ReportOrderNoteGeneralController@records');

                    Route::get('sales-consolidated', 'ReportSaleConsolidatedController@index')
                         ->name('tenant.reports.sales_consolidated.index');
                    Route::get('sales-consolidated/pdf', 'ReportSaleConsolidatedController@pdf');
                    Route::get('sales-consolidated/excel', 'ReportSaleConsolidatedController@excel');
                    Route::get('sales-consolidated/filter', 'ReportSaleConsolidatedController@filter');
                    Route::get('sales-consolidated/records', 'ReportSaleConsolidatedController@records');
                    Route::get('sales-consolidated/totals-by-item', 'ReportSaleConsolidatedController@totalsByItem');
                    Route::get('sales-consolidated/pdf-totals', 'ReportSaleConsolidatedController@pdfTotals');
                    Route::get('sales-consolidated/excel-totals', 'ReportSaleConsolidatedController@excelTotals');


                    Route::prefix('user-commissions')->group(function () {

                        Route::get('', 'ReportUserCommissionController@index')
                             ->name('tenant.reports.user_commissions.index');
                        Route::get('/pdf', 'ReportUserCommissionController@pdf')
                             ->name('tenant.reports.user_commissions.pdf');
                        Route::get('/excel', 'ReportUserCommissionController@excel')
                             ->name('tenant.reports.user_commissions.excel');
                        Route::get('/filter', 'ReportUserCommissionController@filter')
                             ->name('tenant.reports.user_commissions.filter');
                        Route::get('/records', 'ReportUserCommissionController@records')
                             ->name('tenant.reports.user_commissions.records');

                    });


                    Route::prefix('fixed-asset-purchases')->group(function () {

                        Route::get('', 'ReportFixedAssetPurchaseController@index')
                             ->name('tenant.reports.fixed-asset-purchases.index');
                        Route::get('pdf', 'ReportFixedAssetPurchaseController@pdf');
                        Route::get('excel', 'ReportFixedAssetPurchaseController@excel');
                        Route::get('filter', 'ReportFixedAssetPurchaseController@filter');
                        Route::get('records', 'ReportFixedAssetPurchaseController@records');

                    });

                    Route::prefix('massive-downloads')->group(function () {

                        Route::get('', 'ReportMassiveDownloadController@index')
                             ->name('tenant.reports.massive-downloads.index');
                        Route::get('filter', 'ReportMassiveDownloadController@filter');
                        Route::get('pdf', 'ReportMassiveDownloadController@pdf');
                        Route::get('records', 'ReportMassiveDownloadController@records');

                    });

                });

                Route::get('cash/report/income-summary/{cash}', 'ReportIncomeSummaryController@pdf')
                     ->name('tenant.reports.income_summary.pdf');


            });
        });
    }
