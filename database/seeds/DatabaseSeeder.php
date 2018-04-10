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
        $dates       = ['2018-04-01', '2018-04-02', '2018-04-03', '2018-04-04', '2018-04-05', '2018-04-06', '2018-04-07',
                        '2018-04-08', '2018-04-09', '2018-04-10'];
        $leave_types = ['Absent Leave','Annual Leave','Sick Leave', 'Family Responsibility Leave',
                        'Maternity Leave', 'Special Unpaid Leave', 'Absent With Permission Leave',
                        'Injury On Duty Leave', 'Study Leave', 'Suspended With Pay Leave', 'Other',
                        'Suspended Without Pay Leave'];
        $devices = ['862037029010294', '354082061038709', '354082061038691', '864121017029017', '862037029010518',
                    '862124029010294', '354082032430389', '354032456103869', '864810170290123', '869037029010555',
                    '832142324310294', '332432432423433', '323421676588679', '325453645657657', '989078981232124'];

        $departments = ["Accounting/Finance", 'Human Resources', 'Sales', 'Security', 'Tech Development', 'Operations Planning'];

        factory(User::class, 10)->create();
        factory(Address::class, 20)->create();
        factory(NextOfKin::class, 10)->create();
        factory(Spouse::class, 10)->create();

        foreach ($departments as $department) {
            $created_by = User::all()->random();

            DB::table('departments')->insert([
                'description' => "",
                'name'        => $department,
                'created_by'  => $created_by->id,
                'created_at'  => Carbon::now()
            ]);
        }

        $departments = Department::all();
        foreach ($departments as $department) {
            $user       = User::all()->random();
            $created_by = User::all()->random();


            DB::table('devices')->insert([
                'imei_number'   => $devices[$department->id],
                'serial_number' => rand(900000000, 99999999),
                'phone_number'  => "0".rand(900000000, 99999999),
                'name'          => "GNZ-".rand(900000000, 999999999),
                'supervisor'    => $user->id,
                'department_id' => $department->id,
                'status'        => array_rand([User::ACTIVE, User::DEACTIVATED]),
                'created_by'    => $created_by->id,
                'created_at'    => Carbon::now(),
            ]);
        }
        foreach ($leave_types as $leave_type) {
            $created_by = User::all()->random();
            DB::table('leave_types')->insert([
                'description' => "",
                'name'        => $leave_type,
                'created_by'  => $created_by->id,
                'created_at'  => Carbon::now()
            ]);
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
