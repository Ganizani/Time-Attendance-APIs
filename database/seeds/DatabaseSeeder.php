<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Address;
use App\Department;
use App\Device;
use App\Holiday;
use App\Leave;
use App\LeaveType;
use App\Login;
use App\NextOfKin;
use App\PasswordReset;
use App\Record;
use App\ReportLog;
use App\Spouse;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        //disable foreign key checks
       DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        //Wipe Out Database Tables
        Address::truncate();
        Department::truncate();
        Device::truncate();
        Holiday::truncate();
        LeaveType::truncate();
        Leave::truncate();
        Login::truncate();
        NextOfKin::truncate();
        PasswordReset::truncate();
        Record::truncate();
        ReportLog::truncate();
        Spouse::truncate();
        User::truncate();
    }
}
