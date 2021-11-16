<?php

use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {
            Route::prefix('apiperudev')->group(function () {
                Route::get('massive_validate_cpe/tables', 'MassiveValidateController@tables');
                Route::post('massive_validate_cpe', 'MassiveValidateController@validate');
            });
            //ruta distinta a la version actual
            Route::prefix('service')->group(function () {
                Route::get('exchange/{date}', 'ServiceController@exchange');
                Route::get('{type}/{number}', 'ServiceController@service');
            });
        });
    });
} else {
    Route::domain(env('APP_URL_BASE'))->group(function () {
        Route::middleware('auth:admin')->group(function () {
            Route::prefix('service')->group(function () {
                Route::get('{type}/{number}', 'ServiceController@service');
            });
        });
    });
}
