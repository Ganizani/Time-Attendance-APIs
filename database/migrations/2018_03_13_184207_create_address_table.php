<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('street')->nullable(true);
            $table->string('city')->nullable(true);
            $table->string('province')->nullable(true);
            $table->string('country')->nullable(true);
            $table->string('postal_code')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('last_updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            //Relationships
            //$table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
