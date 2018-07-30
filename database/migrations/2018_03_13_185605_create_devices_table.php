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
            $table->string('serial_number')->nullable(true);
            $table->string('phone_number')->nullable(true);
            $table->string('name')->nullable(true);
            $table->string('supervisor')->nullable(true);
            $table->string('status')->default(\App\Device::ACTIVE);
            $table->string('department_id')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            //Relationships
            //$table->foreign('department_id')->references('id')->on('departments');
            //$table->foreign('supervisor')->references('id')->on('users');
            //$table->foreign('created_by')->references('id')->on('users');
            //$table->foreign('updated_by')->references('id')->on('users');
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
