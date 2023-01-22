<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddImageDefaultToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('items', function (Blueprint $table) {
        //     //
        // });
        DB::connection('tenant')->table('items')->where('image', '')->update(['image' => 'imagen-no-disponible.jpg']);
        DB::connection('tenant')->table('items')->where('image_medium', '')->update(['image_medium' => 'imagen-no-disponible.jpg']);
        DB::connection('tenant')->table('items')->where('image_small', '')->update(['image_small' => 'imagen-no-disponible.jpg']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('items', function (Blueprint $table) {
        //     //
        // });
    }
}
