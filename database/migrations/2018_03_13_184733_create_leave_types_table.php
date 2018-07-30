<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
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
        Schema::dropIfExists('leave_types');
    }
}
