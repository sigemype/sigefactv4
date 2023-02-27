<?php

use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {
            Route::prefix('drivers')->group(function () {
                Route::get('/', 'DriverController@index')->name('tenant.drivers.index');
                Route::get('columns', 'DriverController@columns');
                Route::get('records', 'DriverController@records');
                Route::get('record/{id}', 'DriverController@record');
                Route::get('tables', 'DriverController@tables');
                Route::post('/', 'DriverController@store');
                Route::delete('/{id}', 'DriverController@destroy');
                Route::get('get_options', 'DriverController@getOptions');
            });

            Route::prefix('dispatchers')->group(function () {
                Route::get('/', 'DispatcherController@index')->name('tenant.dispatchers.index');
                Route::get('columns', 'DispatcherController@columns');
                Route::get('records', 'DispatcherController@records');
                Route::get('record/{id}', 'DispatcherController@record');
                Route::get('tables', 'DispatcherController@tables');
                Route::post('/', 'DispatcherController@store');
                Route::delete('/{id}', 'DispatcherController@destroy');
                Route::get('get_options', 'DispatcherController@getOptions');
            });

            Route::prefix('transports')->group(function () {
                Route::get('/', 'TransportController@index')->name('tenant.transports.index');
                Route::get('columns', 'TransportController@columns');
                Route::get('records', 'TransportController@records');
                Route::get('record/{id}', 'TransportController@record');
                Route::get('tables', 'TransportController@tables');
                Route::post('/', 'TransportController@store');
                Route::delete('/{id}', 'TransportController@destroy');
                Route::get('get_options', 'TransportController@getOptions');
            });

            Route::prefix('origin_addresses')->group(function () {
                Route::get('/', 'OriginAddressController@index')->name('tenant.origin_addresses.index');
                Route::get('columns', 'OriginAddressController@columns');
                Route::get('records', 'OriginAddressController@records');
                Route::get('record/{id}', 'OriginAddressController@record');
                Route::get('tables', 'OriginAddressController@tables');
                Route::post('/', 'OriginAddressController@store');
                Route::delete('/{id}', 'OriginAddressController@destroy');
                Route::get('get_options', 'OriginAddressController@getOptions');
            });

            Route::prefix('delivery_addresses')->group(function () {
                Route::get('tables', 'DeliveryAddressController@tables');
                Route::post('/', 'DeliveryAddressController@store');
                Route::get('get_options', 'DeliveryAddressController@getOptions');
            });

            Route::prefix('dispatch_persons')->group(function () {
                Route::get('tables', 'DispatchPersonController@tables');
                Route::post('/', 'DispatchPersonController@store');
                Route::get('get_options', 'DispatchPersonController@getOptions');
                Route::post('get_filter_options', 'DispatchPersonController@getFilterOptions');
            });

            Route::prefix('dispatch_addresses')->group(function () {
                Route::get('tables', 'DispatchAddressController@tables');
                Route::post('/', 'DispatchAddressController@store');
                Route::delete('/{id}', 'DispatchAddressController@destroy');
                Route::get('get_options/{sender_id}', 'DispatchAddressController@getOptions');
                Route::get('search/{person_id}', 'DispatchAddressController@searchAddress');
            });
        });
    });
}
