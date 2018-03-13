<?php


namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers;


class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

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
        'title',
        'first_name',
        'last_name',
        'user_type',
        'department',
        'employee_code',
        'email',
        'gender',
        'status',
        'phone_number',
        'alt_phone_number',
        'verified',
        'verification_token',
        'profile_picture',
        'address_id',
        'company_id',
        'password',
        'created_at',
        'updated_at',
        'created_by',
        'last_updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    //Functions
    public static function encryptPassword($password){

        return hash('sha256', $password);
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

        $count = User::where('id', $id)
            ->where('deleted_at', '!=', null)
            ->count();

        $valid_token = ($count > 0) ? true : false;

        return $valid_token;
    }

    public static function isVerified($id){

        $count = User::where('id', $id)
            ->where('verified', User::VERIFIED)
            ->count();

        $valid_token = ($count > 0) ? true : false;

        return $valid_token;
    }

    public static function getUserTypeName($user_type){

        $type = ['OTHER','SYSTEM ADMIN', 'EMPLOYEE'];

        return $type[$user_type];
    }

    //Rules
    public static function createRules(){

        return [
            'email'             => 'required|email|unique:users',
            'password'          => 'required|confirmed|min:8',
            'user_type'         => 'required|in:' . User::SYSTEM_ADMIN . ',' . User::EMPLOYEE,
            'title'             => 'required',
            'status'            => 'required|in:' . User::ACTIVE . ',' . User::DEACTIVATED ,
            'gender'            => 'sometimes|nullable|in:' . User::FEMALE . ',' .  User::MALE,
            'first_name'        => 'required|max:255',
            'last_name'         => 'required|max:255',
            'employee_code'     => 'required|max:255',
        ];
    }

    public static function updateRules($id){

        return [
            'email'         => 'email|unique:users,email,'.$id,
            'password'      => 'min:8',
            'gender'        => 'sometimes|nullable|in:' . User::FEMALE . ',' .  User::MALE,
            'user_type'     => 'required|in:' . User::SYSTEM_ADMIN . ',' . User::EMPLOYEE,
            'first_name'    => 'max:255',
            'last_name'     => 'max:255',
            'employee_code'     => 'required|max:255',
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
    public static function userInformation($id){

        $item = User::where('id', $id)->first();

        if(count($item) <= 0) return null;

        return [
            'id'                   => $item->id,
            'employee_code'        => $item->employee_code,
            'title'                => $item->title,
            'first_name'           => $item->first_name,
            'last_name'            => $item->last_name,
            'name'                 => $item->title." ".$item->first_name. " ". $item->last_name,
            'department'           => $item->department,
            'gender'               => $item->gender,
            'phone_number'         => $item->phone_number,
            'user_type'            => $item->user_type,
            'user_type_name'       => User::getUserTypeName($item->user_type),
            'verified'             => $item->verified,
            'email'                => $item->email,
            'profile_picture'      => $item->profile_picture
        ];
    }

    public static function userModel($item){

        return  [
            'id'                   => $item->id,
            'title'                => $item->title,
            'first_name'           => $item->first_name,
            'last_name'            => $item->last_name,
            'name'                 => $item->title." ".$item->first_name. " ". $item->last_name,
            'gender'               => $item->gender,
            'phone_number'         => $item->phone_number,
            'user_type'            => $item->user_type,
            'company'              => Company::companyInformation($item->company_id),
            'user_type_name'       => User::getUserTypeName($item->user_type),
            'verified'             => $item->verified,
            'email'                => $item->email,
            'status'               => $item->status,
            'created_by'           => User::userInformation($item->created_by),
            'created_at'           => Helpers::formatDate($item->created_at),
            'updated_at'           => Helpers::formatDate($item->updated_at),
            'profile_picture'      => $item->profile_picture,
        ];
    }

    public static function userLoginModel($item){

        return  [
            'id'                   => $item->id,
            'title'                => $item->title,
            'first_name'           => $item->first_name,
            'last_name'            => $item->last_name,
            'name'                 => $item->title." ".$item->first_name. " ". $item->last_name,
            'gender'               => $item->gender,
            'phone_number'         => $item->phone_number,
            'user_type'            => $item->user_type,
            'user_type_name'       => User::getUserTypeName($item->user_type),
            'verified'             => $item->verified,
            'email'                => $item->email,
            'status'               => $item->status,
            'access_token'         => AccessToken::getAccessToken($item->id),
            'created_by'           => User::userInformation($item->created_by),
            'created_at'           => Helpers::formatDate($item->created_at),
            'updated_at'           => Helpers::formatDate($item->updated_at),
            'profile_picture'      => $item->profile_picture,
        ];
    }
}

