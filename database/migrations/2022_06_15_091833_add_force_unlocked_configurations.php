<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\System\Configuration;
use App\Models\System\Client;
use Illuminate\Support\Facades\DB;
use Hyn\Tenancy\Environment;


class AddForceUnlockedConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        
        $configuration = Configuration::first();
        $configuration->locked_admin = false;
        $configuration->save();


        $clients = Client::get();

        foreach ($clients as $client) {

            $client->locked_tenant = false;
            $client->save();

            $tenancy = app(Environment::class);
            $tenancy->tenant($client->hostname->website);
            DB::connection('tenant')->table('configurations')->where('id', 1)->update(['locked_tenant' => false]);

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
