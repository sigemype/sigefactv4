<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) 
{
    Route::domain($current_hostname->fqdn)->group(function () {

        Route::middleware(['auth', 'locked.tenant'])->group(function () {

            Route::prefix('authorized-discount-users')->group(function () {

                Route::post('', 'AuthorizedDiscountUserController@store');
                Route::post('validate-token', 'AuthorizedDiscountUserController@validateToken');

            });

        });
        
    });
}
