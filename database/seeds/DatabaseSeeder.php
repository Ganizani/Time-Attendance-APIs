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
use Carbon\Carbon;
use Faker\Generator as Faker;

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
        //Faker
        $faker = new Faker();
        //Data
        $users = [
            [
               'first_name' => 'Caroline',
               'last_name'  => 'Tachiona',
               'email'      => 'caroline@nessgroup.co.za',
            ],
            [
                'first_name' => 'Wandisa',
                'last_name'  => 'Vitsha',
                'email'      => 'wandisa@nessgroup.co.za',
            ],
            [
                'first_name' => 'Thabang',
                'last_name'  => 'Motea',
                'email'      => 'thabang@nessgroup.co.za',
            ],
            [
                'first_name' => 'Nolwazi',
                'last_name'  => 'Mbewu',
                'email'      => 'nolwazi@nessgroup.co.za',
            ],
            [
                'first_name' => 'Lungile',
                'last_name'  => 'Ndyibithi',
                'email'      => 'lungile@nessgroup.co.za',
            ]
        ];
        $dates       = ['2018-04-01', '2018-04-02', '2018-04-03', '2018-04-04', '2018-04-05', '2018-04-06', '2018-04-07',
                        '2018-04-08', '2018-04-09', '2018-04-10', '2018-04-11', '2018-04-12', '2018-04-13', '2018-04-16',
                        '2018-04-17', '2018-04-18', '2018-04-19', '2018-04-20', '2018-04-23', '2018-04-24', '2018-04-25'];
        $leave_types = ['Annual Leave/Vacation','Sick Leave', 'Family Responsibility Leave', 'Maternity Leave', 'Study Leave', 'Other'];
        $devices = ['862037029010294', '354082061038709', '354082061038691', '864121017029017', '862037029010518'];

        $departments = ['Sales'];

        //factory(User::class, 10)->create();
        //factory(Address::class, 20)->create();
        //factory(NextOfKin::class, 10)->create();
        //factory(Spouse::class, 10)->create();

        foreach ($users as $user){
            $insert = new User();
            $insert->first_name = $user['first_name'];
            $insert->last_name  = $user['last_name'];
            $insert->email      = $user['email'];
            $insert->department_id = 1;
            $insert->password   = User::encryptPassword('Test@123');
            $insert->verified   = 'VERIFIED';
            $insert->created_at = Carbon::now('CAT');
            $insert->save();
        }

        foreach ($departments as $department) {
            $insert = new Department();
            $insert->name       = $department;
            $insert->created_at = Carbon::now('CAT');
            $insert->save();
        }

        foreach ($devices as $device) {
            $insert = new Device();
            $insert->name          = "GNZ-".rand(900000000, 999999999);
            $insert->imei_number   = $device;
            $insert->department_id = 1;
            $insert->status        = User::ACTIVE;
            $insert->created_at    = Carbon::now('CAT');
            $insert->save();

        }

        foreach ($leave_types as $leave_type) {
            $insert = new LeaveType();
            $insert->name = $leave_type;
            $insert->created_at = Carbon::now('CAT');
            $insert->save();
        }

        $users = User::all();
        foreach ($dates as $date)  {
            foreach  ($users as $user){
                $device = Device::where('department_id', $user->department_id)->get()->random();
                //IN
                $in  = array_rand(['06:03:01', '06:30:32', '07:12:01', '07:43:09']);
                $out = array_rand(['16:03:01', '16:30:32', '17:12:01', '17:43:09']);
                DB::table('records')->insert([
                    'user_id'       => $user->id,
                    'status'        => 'IN',
                    'date'          => $date,
                    'time'          => '07:43:09',
                    'imei_number'   => $device->imei_number,
                    'latitude'      => "0.0",
                    'longitude'     => "0.0",
                    'created_at'    => Carbon::now()
                ]);

                //OUT
                DB::table('records')->insert([
                    'user_id'       => $user->id,
                    'status'        => 'OUT',
                    'date'          => $date,
                    'time'          => '17:43:09',
                    'imei_number'   => $device->imei_number,
                    'latitude'      => "0.0",
                    'longitude'     => "0.0",
                    'created_at'    => Carbon::now()
                ]);
            }
        }
    }
}
