<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->nullable(true);
            $table->string('report_name')->nullable(true);
            $table->string('from_date')->nullable(true);
            $table->string('to_date')->nullable(true);
            $table->string('department_id')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            //Relationships
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_logs');
    }
}
