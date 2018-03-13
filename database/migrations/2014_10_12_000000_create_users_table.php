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
            $table->string('user_type')->default(1);
            $table->string('department')->nullable(true);
            $table->string('company_id')->nullable(true);
            $table->string('gender')->nullable(true);
            $table->string('status')->default(User::ACTIVE);
            $table->string('phone_number')->nullable(true);
            $table->string('verified')->default(User::UNVERIFIED);
            $table->string('verification_token')->nullable(true);
            $table->string('profile_picture')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('last_updated_by')->nullable(true);
            $table->string('email')->unique();
            $table->string('password');
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
        Schema::dropIfExists('users');
    }
}

