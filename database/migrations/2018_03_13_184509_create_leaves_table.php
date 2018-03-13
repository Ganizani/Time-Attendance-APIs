<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->nullable(true);
            $table->text('attachment')->nullable(true);
            $table->text('comments')->nullable(true);
            $table->date('from_date')->default(\Carbon\Carbon::now());
            $table->date('to_date')->default(\Carbon\Carbon::now());
            $table->string('reason');
            $table->string('created_by')->nullable(true);
            $table->string('last_updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            //Relationship

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaves');
    }
}
