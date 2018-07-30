<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_control', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_group_id')->nullable(true);
            //SYSTEM ADMIN
            $table->integer('system_admin')->nullable(true)->default(0);
            $table->integer('login')->nullable(true)->default(0);
            $table->integer('update_user_type')->nullable(true)->default(0);
            //REPORTS
            $table->integer('view_reports')->nullable(true)->default(0);
            $table->integer('print_reports')->nullable(true)->default(0);
            //DEPARTMENTS
            $table->integer('add_departments')->nullable(true)->default(0);
            $table->integer('edit_departments')->nullable(true)->default(0);
            $table->integer('list_departments')->nullable(true)->default(0);
            $table->integer('print_departments')->nullable(true)->default(0);
            $table->integer('delete_departments')->nullable(true)->default(0);
            //DEVICES
            $table->integer('add_devices')->nullable(true)->default(0);
            $table->integer('edit_devices')->nullable(true)->default(0);
            $table->integer('list_devices')->nullable(true)->default(0);
            $table->integer('print_devices')->nullable(true)->default(0);
            $table->integer('delete_devices')->nullable(true)->default(0);
            //LEAVES
            $table->integer('add_leaves')->nullable(true)->default(0);
            $table->integer('edit_leaves')->nullable(true)->default(0);
            $table->integer('list_leaves')->nullable(true)->default(0);
            $table->integer('print_leaves')->nullable(true)->default(0);
            $table->integer('upload_leaves')->nullable(true)->default(0);
            $table->integer('delete_leaves')->nullable(true)->default(0);
            //LEAVE TYPES
            $table->integer('add_leave_types')->nullable(true)->default(0);
            $table->integer('edit_leave_types')->nullable(true)->default(0);
            $table->integer('list_leave_types')->nullable(true)->default(0);
            $table->integer('print_leave_types')->nullable(true)->default(0);
            $table->integer('delete_leave_types')->nullable(true)->default(0);
            //HOLIDAYS
            $table->integer('add_holidays')->nullable(true)->default(0);
            $table->integer('edit_holidays')->nullable(true)->default(0);
            $table->integer('list_holidays')->nullable(true)->default(0);
            $table->integer('print_holidays')->nullable(true)->default(0);
            $table->integer('delete_holidays')->nullable(true)->default(0);
            //USERS
            $table->integer('add_users')->nullable(true)->default(0);
            $table->integer('edit_users')->nullable(true)->default(0);
            $table->integer('list_users')->nullable(true)->default(0);
            $table->integer('print_users')->nullable(true)->default(0);
            $table->integer('delete_users')->nullable(true)->default(0);
            //USER GROUPS
            $table->integer('add_user_groups')->nullable(true)->default(0);
            $table->integer('edit_user_groups')->nullable(true)->default(0);
            $table->integer('list_user_groups')->nullable(true)->default(0);
            $table->integer('print_user_groups')->nullable(true)->default(0);
            $table->integer('delete_user_groups')->nullable(true)->default(0);
            //
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);
            $table->integer('deleted_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            //Relationships
            //$table->foreign('created_by')->references('id')->on('users');
            //$table->foreign('updated_by')->references('id')->on('users');
            //$table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_control');
    }
}
