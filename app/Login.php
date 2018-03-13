<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:20 PM
 */
namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Login extends Model
{
    protected $table = 'logins';

    protected $fillable = [
        'username',
        'attempt_number',
        'successful'
    ];

    //Functions
    public static function getAttemptNumber($username){

        $count = Login::where('username', $username)
            ->where('successful', '0')
            ->whereBetween('created_at', [Carbon::now()->subMinutes(5),  Carbon::now()])
            ->count();

        return $count+1;

    }
}