<?php

use App\Models\Tenant\Establishment;
use App\Models\Tenant\Series;
use Illuminate\Database\Migrations\Migration;

class TenantCreateNumberSeriesToGuides extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $establishments = Establishment::query()->get();

        foreach ($establishments as $establishment)
        {
            $record = Series::query()
                ->where('document_type_id', '01')
                ->where('establishment_id', $establishment->id)
                ->first();

            if($record) {
                $number = substr($record->number,3, 1);
                Series::query()
                    ->updateOrCreate([
                        'document_type_id' => 'U2',
                        'establishment_id' => $establishment->id
                    ], [
                        'number' => 'NIA'.$number
                    ]);

                Series::query()
                    ->updateOrCreate([
                        'document_type_id' => 'U3',
                        'establishment_id' => $establishment->id
                    ], [
                        'number' => 'NSA'.$number
                    ]);

                Series::query()
                    ->updateOrCreate([
                        'document_type_id' => 'U4',
                        'establishment_id' => $establishment->id
                    ], [
                        'number' => 'NTA'.$number
                    ]);
            }
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
