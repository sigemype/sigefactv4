<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Tenant\Person;


class TenantAddDataDefaultVariousClientsToPersons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $number = '99999999';
        $name = 'Clientes - Varios';
        $identity_document_type_id = '0';
        $type = 'customers';

        $person = Person::where('number', $number)
                            ->where('type', $type)
                            ->first();

        if(!$person)
        {
            Person::create([
                'number' => $number,
                'name' => $name,
                'identity_document_type_id' => $identity_document_type_id,
                'type' => $type,
                'country_id' => 'PE',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
