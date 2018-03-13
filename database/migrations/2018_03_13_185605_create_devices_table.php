<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('imei_number')->nullable(true);
            $table->string('phone_number')->nullable(true);
            $table->string('device_name')->nullable(true);
            $table->string('status')->default(Device::ACTIVE);
            $table->string('company_id')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('last_updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
