<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:21 PM
 */
namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class PasswordReset extends Model
{
    use Notifiable, SoftDeletes;

    protected $table = 'password_resets';

    protected $fillable = [
        'email',
        'token',
        'created_at',
        'expire_at',
    ];

    //Functions
    public static function isValidToken($token, $email){

        $count = PasswordReset::where('token', $token)
            ->where('email', $email)
            ->where('expire_at', '>', Carbon::now())
            ->count();
        $valid_token = ($count > 0) ? "true" : "false";

        return $valid_token;
    }

    public static function hasValidTokenAlready($email){

        $count = PasswordReset::where('email', $email)->count();
        $valid_token = ($count > 0) ? "true" : "false";

        return $valid_token;
    }

    public static function destroyToken($token){

        $token = PasswordReset::where('token', $token)->firstOrFail();
        $token->delete();

        return true;
    }

}