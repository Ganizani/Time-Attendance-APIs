<?php


namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens ,Notifiable, SoftDeletes;

    const VERIFIED          = 'VERIFIED';
    const UNVERIFIED        = 'UNVERIFIED';
    const ACTIVE            = 'ACTIVE';
    const DEACTIVATED       = 'DEACTIVATED';
    const SYSTEM_ADMIN      = '1';
    const EMPLOYEE          = '2';
    const MALE              = 'MALE';
    const FEMALE            = 'FEMALE';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_code',
        'title',
        'id_number',
        'nationality',
        'first_name',
        'last_name',
        'maiden_name',
        'middle_name',
        'preferred_name',
        'user_type',
        'department_id',
        'marital_status',
        'supervisor',
        'email',
        'gender',
        'status',
        'phone_number',
        'work_cell_phone',
        'work_phone',
        'work_location',
        'start_date',
        'job_title',
        'work_email',
        'home_phone',
        'verified',
        'verification_token',
        'profile_picture',
        'password',
        'spouse_id',
        'address_id',
        'next_of_kin_id',
        'uif_number',
        'payment_number',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'deleted_by', 'deleted_at'
    ];

    //Functions
    public static function getUsers($department = "", $from = "", $to = ""){

        $users = DB::table('users');
        if($department != "") $users->where('department_id', $department);
        if($from != "" && $to != "") $users->whereBetween('created_at', [$from, $to]);

        return $users->get();
    }

    public static function encryptPassword($password){
        return bcrypt($password);
    }

    public static function generateVerificationToken(){
        return str_random(55);
    }

    public static function isValidToken($token){

        $count = User::where('verification_token', $token)->count();

        $valid_token = ($count > 0) ? true : false;

        return $valid_token;
    }

    public static function isDeleted($id){

        $count = User::where('id', $id)->where('deleted_at', '!=', null)->count();

        $valid_token = ($count > 0) ? true : false;

        return $valid_token;
    }

    public static function isVerified($id){

        $count = User::where('id', $id)->where('verified', User::VERIFIED)->count();

        $valid_token = ($count > 0) ? true : false;

        return $valid_token;
    }

    public static function getUserTypeName($user_type){

        $type = ['OTHER','SYSTEM ADMIN', 'EMPLOYEE'];

        return $type[$user_type];
    }

    //Rules
    public static function updatePasswordRules(){

        return [
            'password' => 'required|confirmed|min:5',
        ];
    }


    public static function createRules(){

        return [
            'email'             => 'required|email|unique:users',
            'title'             => 'required',
            'status'            => 'required|in:' . User::ACTIVE . ',' . User::DEACTIVATED ,
            'first_name'        => 'required|max:255',
            'last_name'         => 'required|max:255',
            'user_type'         => 'required|exists:user_groups,id',
            'employee_code'     => 'sometimes|nullable|max:255',
            'department'        => 'required|exists:departments,id',
            'uif_number'        => 'sometimes|nullable',
            'payment_number'    => 'sometimes|nullable',
            'work_location'     => 'required',
            'job_title'         => 'required',
            'phone_number'      => 'required',
            'gender'            => 'sometimes|nullable|in:' . User::FEMALE . ',' .  User::MALE,
        ];
    }

    public static function updateRules($id){

        return [
            'email'             => 'email|unique:users,email,'.$id,
            'password'          => 'sometimes|min:5',
            'title'             => 'required',
            'status'            => 'required|in:' . User::ACTIVE . ',' . User::DEACTIVATED ,
            'first_name'        => 'required|max:255',
            'last_name'         => 'required|max:255',
            'employee_code'     => 'sometimes|nullable|max:255',
            'department'        => 'required|exists:departments,id',
            'user_type'         => 'required|exists:user_groups,id',
            'uif_number'        => 'sometimes|nullable',
            'payment_number'    => 'sometimes|nullable',
            'work_location'     => 'required',
            'job_title'         => 'required',
            'phone_number'      => 'required',
            'gender'            => 'sometimes|nullable|in:' . User::FEMALE . ',' .  User::MALE,
        ];
    }

    public static function resetPasswordRules(){
        return [
            'password'  => 'required|confirmed|min:8',
            'token'     => 'required|exists:password_resets,token'
        ];
    }

    public static function forgotPasswordRules(){
        return [
            'email'  => 'required|email|exists:users,email'
        ];
    }

    public static function loginRules(){
        return [
            'email'     => 'required|email',
            'password'  => 'required',
            'remember'  => 'sometimes|nullable|in:true,false'
        ];
    }

    //Models

    public static function supervisor_info($id){

        $item = User::where('id', $id)->first();

        if(!isset($item)) return null;

        return [
            'id'                   => $item->id,
            'employee_code'        => $item->employee_code,
            'id_number'            => $item->id_number,
            'title'                => $item->title,
            'first_name'           => $item->first_name,
            'last_name'            => $item->last_name,
            'user_type'            => $item->user_type,
            'email'                => $item->email,
            'status'               => $item->status,
            'department_id'        => $item->department_id,
            'work_cell_phone'      => $item->work_cell_phone,
            'work_phone'           => $item->work_phone,
            'work_location'        => $item->work_location,
            'start_date'           => Helpers::formatDate($item->start_date, "Y-m-d"),
            'job_title'            => $item->job_title,
            'work_email'           => $item->work_email,
            'home_phone'           => $item->home_phone,
            'department'           => Department::info($item->department_id),
        ];
        //'user_type_name'       => User::getUserTypeName($item->user_type),
    }

    public static function info($id){

        $item = User::where('id', $id)->first();

        if(!isset($item)) return null;

        return [
            'id'                   => $item->id,
            'employee_code'        => $item->employee_code,
            'id_number'            => $item->id_number,
            'nationality'          => $item->nationality,
            'title'                => $item->title,
            'first_name'           => $item->first_name,
            'last_name'            => $item->last_name,
            'maiden_name'          => $item->maiden_name,
            'middle_name'          => $item->middle_name,
            'preferred_name'       => $item->preferred_name,
            'marital_status'       => $item->marital_status,
            'name'                 => $item->title." ".$item->first_name. " ". $item->last_name,
            'gender'               => $item->gender,
            'phone_number'         => $item->phone_number,
            'user_type'            => $item->user_type,
            'email'                => $item->email,
            'status'               => $item->status,
            'department_id'        => $item->department_id,
            'work_cell_phone'      => $item->work_cell_phone,
            'work_phone'           => $item->work_phone,
            'work_location'        => $item->work_location,
            'start_date'           => Helpers::formatDate($item->start_date, "Y-m-d"),
            'job_title'            => $item->job_title,
            'work_email'           => $item->work_email,
            'home_phone'           => $item->home_phone,
            'verified'             => $item->verified,
            'user_group'           => UserGroup::info($item->user_type),
            'next_of_kin'          => NextOfKin::info($item->next_of_kin_id),
            'address'              => Address::info($item->address_id),
            'spouse'               => Spouse::info($item->spouse_id),
            'supervisor'           => User::info($item->supervisor_id),
            'department'           => Department::info($item->department_id),
            'profile_picture'      => $item->profile_picture,
        ];
        //'user_type_name'       => User::getUserTypeName($item->user_type),
    }

    public static function model($item, $token = ""){

        return  [
            'id'                   => $item->id,
            'employee_code'        => $item->employee_code,
            'id_number'            => $item->id_number,
            'nationality'          => $item->nationality,
            'title'                => $item->title,
            'first_name'           => $item->first_name,
            'last_name'            => $item->last_name,
            'maiden_name'          => $item->maiden_name,
            'middle_name'          => $item->middle_name,
            'preferred_name'       => $item->preferred_name,
            'marital_status'       => $item->marital_status,
            'name'                 => $item->title." ".$item->first_name. " ". $item->last_name,
            'gender'               => $item->gender,
            'phone_number'         => $item->phone_number,
            'user_type'            => $item->user_type,
            'email'                => $item->email,
            'status'               => $item->status,
            'department_id'        => $item->department_id,
            'work_cell_phone'      => $item->work_cell_phone,
            'work_phone'           => $item->work_phone,
            'work_location'        => $item->work_location,
            'start_date'           => Helpers::formatDate($item->start_date, "Y-m-d"),
            'job_title'            => $item->job_title,
            'work_email'           => $item->work_email,
            'home_phone'           => $item->home_phone,
            'verified'             => $item->verified,
            'uif_number'           => $item->uif_number,
            'payment_number'       => $item->payment_number,
            'user_group'           => UserGroup::info($item->user_type),
            'next_of_kin'          => NextOfKin::info($item->next_of_kin_id),
            'address'              => Address::info($item->address_id),
            'spouse'               => Spouse::info($item->spouse_id),
            'supervisor'           => User::supervisor_info($item->supervisor),
            'department'           => Department::info($item->department_id),
            'profile_picture'      => $item->profile_picture,
            'token'                => $token,
            'created_by'           => User::supervisor_info($item->created_by),
            'created_at'           => Helpers::formatDate($item->created_at),
            'updated_by'           => User::supervisor_info($item->updated_by),
            'updated_at'           => Helpers::formatDate($item->updated_at),
        ];
        // 'user_type_name'       => User::getUserTypeName($item->user_type),
    }
}

