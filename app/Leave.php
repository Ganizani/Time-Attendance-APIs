<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:45 PM
 */

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Helpers;
use Illuminate\Support\Facades\DB;

class Leave extends Model
{
    use SoftDeletes;

    protected $table = 'leaves';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'attachment',
        'last_day_of_work',
        'from_date',
        'to_date',
        'comments',
        'leave_type',
        'address_on_leave',
        'email_on_leave',
        'phone_on_leave',
        'processed_by',
        'leave_type_text',
        'leave_days',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    //Functions
    public static function getLeaveByIdDate($user_id, $date){

        $query = "SELECT l.* 
                  FROM   leaves l
                  WHERE  l.deleted_at IS NULL AND l.user_id = '{$user_id}' AND '{$date}' BETWEEN l.from_date AND l.to_date";

        $result = DB::select($query);

        return (count($result) > 0) ? $result[0] : null;
    }

    public static function getLeaveTypes($leave_type){

        $result = LeaveType::where('leave_type',$leave_type)->first();

        return $result;
    }

    public static function getDaysDifference($from, $to){

        $to_date    = Carbon::parse($to);
        $from_date  = Carbon::parse($from);

        return abs($from_date->diffInDays($to_date)) + 1;
    }


    //Rules
    public static function createRules(){

        return [
            'user'             => 'required|exists:users,id',
            'from_date'        => 'required|date|date_format:"Y-m-d"',
            'to_date'          => 'required|date|date_format:"Y-m-d"',
            'leave_type'       => 'required|exists:leave_types,id',
            'attachment'       => 'sometimes|nullable',
            'comments'         => 'sometimes|nullable',
            'last_day_of_work' => 'sometimes|nullable|date_format:"Y-m-d"',
            'address_on_leave' => 'sometimes|nullable',
            'email_on_leave'   => 'sometimes|nullable|email',
            'phone_on_leave'   => 'sometimes|nullable',
            'specific_leave_type'  => 'sometimes|nullable'
        ];
    }

    public static function updateRules($id){

        return [
            'user'             => 'required|exists:users,id',
            'from_date'        => 'required|date|date_format:"Y-m-d"',
            'to_date'          => 'required|date|date_format:"Y-m-d"',
            'leave_type'       => 'required|exists:leave_types,id',
            'attachment'       => 'sometimes|nullable',
            'comments'         => 'sometimes|nullable',
            'last_day_of_work' => 'sometimes|nullable|date_format:"Y-m-d"',
            'address_on_leave' => 'sometimes|nullable',
            'email_on_leave'   => 'sometimes|nullable|email',
            'phone_on_leave'   => 'sometimes|nullable',
            'specific_leave_type'  => 'sometimes|nullable',
        ];
    }

    public static function leaveReport(){

        return [
            'from_date'    => 'required|date_format:"Y-m-d"',
            'to_date'      => 'required|date_format:"Y-m-d"',
            'department'   => 'sometimes|nullable|exists:departments,id',
            'leave_type'   => 'sometimes|nullable|exists:leave_types,id',
        ];
    }


    //Models
    public static function model($item){

        return  [
            'id'                => $item->id,
            'user_id'           => $item->user_id,
            'attachment'        => $item->attachment,
            'comments'          => $item->comments,
            'address_on_leave'  => $item->address_on_leave,
            'email_on_leave'    => $item->email_on_leave,
            'phone_on_leave'    => $item->phone_on_leave,
            'last_day_of_work'  => Helpers::formatDate($item->last_day_of_work, "Y-m-d"),
            'from_date'         => Helpers::formatDate($item->from_date, "Y-m-d"),
            'to_date'           => Helpers::formatDate($item->to_date, "Y-m-d"),
            'leave_type'        => LeaveType::info($item->leave_type),
            'leave_type_text'   => $item->leave_type_text,
            'leave_days'        => $item->leave_days,
            'user'              => User::info($item->user_id),
            'created_at'        => Helpers::formatDate($item->created_at),
            'updated_at'        => Helpers::formatDate($item->updated_at),
            'created_by'        => User::info($item->created_by),
            'updated_by'        => User::info($item->updated_by)
        ];
    }
}