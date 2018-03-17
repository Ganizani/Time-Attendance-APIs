<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id')->nullable(true);
            $table->date('date')->nullable(true);
            $table->time('time')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('latitude')->default('0');
            $table->string('longitude')->default('0');
            $table->string('imei_number')->nullable(true);
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
        Schema::dropIfExists('records');
    }
}
