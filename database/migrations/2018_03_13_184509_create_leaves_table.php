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
            $table->date('last_day_of_work')->nullable(true);
            $table->date('from_date')->nullable(true);
            $table->date('to_date')->nullable(true);
            $table->string('leave_type')->nullable(true);
            $table->string('address_on_leave')->nullable(true);
            $table->string('email_on_leave')->nullable(true);
            $table->string('phone_on_leave')->nullable(true);
            $table->string('processed_by')->nullable(true);
            $table->string('leave_type_text')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            //Relationship
            //$table->foreign('employee_id')->references('id')->on('users');
            //$table->foreign('processed_by')->references('id')->on('users');

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
