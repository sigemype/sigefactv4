<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function() {

            Route::prefix('system-activity-logs')->group(function () {

                Route::prefix('generals')->group(function () {

                    Route::get('', 'SystemActivityLogGeneralController@index')->name('tenant.system_activity_logs.generals.index');
                    Route::get('records', 'SystemActivityLogGeneralController@records');
                    Route::get('columns', 'SystemActivityLogGeneralController@columns');
                    Route::post('check-last-password-update', 'SystemActivityLogGeneralController@checkLastPasswordUpdate');
                    Route::get('report/{type}', 'SystemActivityLogGeneralController@exportReport');
                });

                Route::prefix('transactions')->group(function () {

                    Route::get('', 'SystemActivityLogTransactionController@index')->name('tenant.system_activity_logs.transactions.index');
                    Route::get('records', 'SystemActivityLogTransactionController@records');
                    Route::get('columns', 'SystemActivityLogTransactionController@columns');
                    Route::get('report/{type}', 'SystemActivityLogTransactionController@exportReport');
                });
            });

            
            Route::prefix('authorized-discount-users')->group(function () {

                Route::post('', 'AuthorizedDiscountUserController@store');
                Route::post('validate-token', 'AuthorizedDiscountUserController@validateToken');

            });

        });
        
    });
}
