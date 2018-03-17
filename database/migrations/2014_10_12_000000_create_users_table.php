<?php

use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_code')->nullable(true);
            $table->string('title')->nullable(true);
            $table->string('first_name')->nullable(true);
            $table->string('last_name')->nullable(true);
            $table->string('middle_name')->nullable(true);
            $table->string('maiden_name')->nullable(true);
            $table->string('preferred_name')->nullable(true);
            $table->string('id_number')->nullable(true);
            $table->string('nationality')->nullable(true);
            $table->string('marital_status')->nullable(true);
            $table->string('supervisor')->nullable(true);
            $table->string('work_phone')->nullable(true);
            $table->string('work_cell_phone')->nullable(true);
            $table->string('work_location')->nullable(true);
            $table->string('work_email')->nullable(true);
            $table->string('job_title')->nullable(true);
            $table->string('home_phone')->nullable(true);
            $table->string('user_type')->default(1);
            $table->string('gender')->nullable(true);
            $table->string('status')->default(User::ACTIVE);
            $table->string('phone_number')->nullable(true);
            $table->string('verified')->default(User::UNVERIFIED);
            $table->string('verification_token')->nullable(true);
            $table->string('profile_picture')->nullable(true);
            $table->date('start_date')->nullable(true);
            $table->string('spouse_id')->nullable(true);
            $table->string('department_id')->nullable(true);
            $table->string('address_id')->nullable(true);
            $table->string('next_of_kin_id')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();

            //Relationships
            //$table->foreign('department_id')->references('id')->on('departments');
            //$table->foreign('spouse_id')->references('id')->on('spouses');
            //$table->foreign('address_id')->references('id')->on('addresses');
            //$table->foreign('next_of_kin_id')->references('id')->on('next_of_kins');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

