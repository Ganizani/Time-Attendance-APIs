<?php

use Faker\Generator as Faker;
use App\User;
use App\Device;
use App\Department;
use App\LeaveType;
use App\Leave;
use App\Address;
use App\Holiday;
use App\Record;
use App\NextOfKin;
use App\Spouse;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    //$supervisor = User::all()->random();
    //$supervisor = Spouse::all()->random();

    return [
        'employee_code'      => "GNZ-".$faker->randomNumber(3),
        'title'              => $faker->title,//$faker->randomElement(['Mr', 'Mrs', 'Miss', 'Dr', 'Prof']),
        'first_name'         => $faker->firstName(),
        'last_name'          => $faker->lastName(),
        'middle_name'        => $faker->randomElement([$faker->lastName(), '']),
        'maiden_name'        => $faker->randomElement([$faker->lastName(), '']),
        'preferred_name'     => $faker->randomElement([$faker->lastName(), '']),
        'id_number'          => str_random(13),
        'nationality'        => $faker->country(),
        'marital_status'     => $faker->randomElement(['Single', 'Married', 'Widowed']),
        'supervisor'         => rand(1,10),
        'work_phone'         => '',
        'work_cell_phone'    => '',
        'work_location'      => '',
        'work_email'         => '',
        'job_title'          => $faker->jobTitle(),
        'home_phone'         => '',
        'gender'             => $faker->randomElement([User::MALE, User::FEMALE]),
        'status'             => User::ACTIVE,
        'phone_number'       => "0".$faker->randomNumber(9),
        'verified'           => $verified = $faker->randomElement([User::VERIFIED , User::UNVERIFIED]),
        'verification_token' => ($verified == User::UNVERIFIED) ? str_random(50) : "" ,
        'user_type'          => $faker->randomElement([1 , 2, 3]),
        'email'              => $faker->unique()->safeEmail,
        'password'           => bcrypt('12345'), // secret
        'profile_picture'    => null,
        'start_date'         => Carbon::now(),
        'spouse_id'          => rand(1,10),
        'department_id'      => rand(1,5),
        'address_id'         => rand(1,10),
        'next_of_kin_id'     => rand(1,10),
        'created_at'         => Carbon::now()
    ];
});

$factory->define(Address::class, function (Faker $faker) {

    $created_by = User::all()->random();

    return [
        'house_no'      => $faker->randomNumber(3),
        'street_no'     => $faker->randomNumber(3),
        'street_name'   => $faker->streetName(),
        'suburb'        => $faker->city(),
        'city'          => $faker->city(),
        'province'      => $faker->city(),
        'country'       => "South Africa",
        'postal_code'   => $faker->randomNumber(4),
        'created_by'    => $created_by->id,
        'created_at'    => Carbon::now()
    ];
});


$factory->define(NextOfKin::class, function (Faker $faker) {

    $created_by = User::all()->random();

    return [
        'title'         => $faker->title(),
        'first_name'    => $faker->firstName(),
        'last_name'     => $faker->lastName(),
        'middle_name'   => $faker->randomElement([$faker->firstName(), '']),
        'email'         => $faker->safeEmail(),
        'cell_phone'    => "",
        'home_phone'    => "",
        'address_id'    => rand(10,20),
        'relationship'  => $faker->randomElement(['Spouse', 'Children', 'Parent', 'Sibling', 'Niece/Nephew', 'Aunt/Uncle', 'Cousin', 'Grand Parent', 'Other' ]),
        'created_by'    => $created_by->id,
        'created_at'    => Carbon::now()
    ];
});

$factory->define(Spouse::class, function (Faker $faker) {

    $created_by = User::all()->random();

    return [
        'name'          => $faker->firstName() . " " .$faker->lastName(),
        'employer'      => $faker->company(),
        'work_location' => $faker->city(),
        'work_phone'    => "",
        'cell_phone'    => "0".$faker->randomNumber(9),
        'created_by'    => $created_by->id,
        'created_at'    => Carbon::now()
    ];
});

$factory->define(Department::class, function (Faker $faker) {

    $created_by = User::all()->random();

    return [
        'description' => $faker->sentences(3),
        'name'        => "GNX",
        'created_by'  => $created_by->id,
        'created_at'  => Carbon::now()
    ];
});

$factory->define(LeaveType::class, function (Faker $faker) {

    $created_by = User::all()->random();

    return [
        'description' => $faker->sentences(3),
        'name'        => "",
        'created_by'  => $created_by->id,
        'created_at'  => Carbon::now()
    ];
});

$factory->define(Leave::class, function (Faker $faker) {

    $user         = User::all()->random();
    $processed_by = User::all()->random();
    $created_by   = User::all()->random();
    $leave_type   = LeaveType::all()->random();

    return [
        'user_id'           => $user->id,
        'attachment'        => null,
        'comments'          => $faker->sentences(3),
        'last_day_of_work'  => $faker->randomElement([Carbon::now()->addDays(rand(1,3)), Carbon::now()->subDays(rand(1,3))]),
        'from_date'         => $from = $faker->randomElement([Carbon::now()->addDays(rand(1,3)), Carbon::now()->subDays(rand(1,3))]),
        'to_date'           => $from->addDays(5),
        'leave_type'        => $leave_type->id,
        'address_on_leave'  => $faker->address,
        'email_on_leave'    => $faker->safeEmail,
        'phone_on_leave'    => "0".$faker->randomNumber(9),
        'processed_by'      => $processed_by->id,
        'created_by'        => $created_by->id,
        'created_at'        => Carbon::now()
    ];
});

$factory->define(Device::class, function (Faker $faker) {

    $user       = User::all()->random();
    $created_by = User::all()->random();
    $dept       = Department::all()->random();

    return [
        'imei_number'   => $faker->randomNumber(9),
        'serial_number' => $faker->randomNumber(9),
        'phone_number'  => "0".$faker->randomNumber(9),
        'name'          => "GNZ-" . $faker->randomNumber(4),
        'supervisor'    => $user->id,
        'department'    => $dept->id,
        'created_by'    => $created_by->id,
        'status'        => $faker->randomElement([User::ACTIVE, User::DEACTIVATED]),
        'created_at'         => Carbon::now()
    ];
});

$factory->define(Record::class, function (Faker $faker) {

    $user   = User::all()->random();
    $device = Device::all()->random();

    return [
        'user_id'     => $user->id,
        'date'        => $faker->randomElement([Carbon::now()->addDays(rand(1,3)), Carbon::now()->subDays(rand(1,3))]),
        'time'        => $faker->time('H:s:i'),
        'latitude'    => 0,
        'longitude'   => 0,
        'imei_number' => $device->imei_number,
        'status'      => $faker->randomElement(['IN', 'OUT'])
    ];

});
