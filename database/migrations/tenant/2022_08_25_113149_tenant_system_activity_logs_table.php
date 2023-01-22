<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantSystemActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_activity_logs', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('auth_transaction_type')->nullable()->index();

            $table->date('date')->index();
            $table->time('time')->index();
            
            $table->integer('origin_id')->nullable();
            $table->string('origin_type')->nullable();

            $table->string('ip')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable()->index();
            $table->string('platform_name')->nullable();
            $table->string('platform_version')->nullable();
            $table->string('browser_name')->nullable();
            $table->string('browser_version')->nullable();

            $table->index(['origin_id','origin_type'],'payment_index');
            
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_activity_logs');
    }

}
