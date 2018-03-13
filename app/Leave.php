<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:45 PM
 */

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Helpers;

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
        'from_date',
        'to_date',
        'comments',
        'reason',
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
    protected $hidden = [];

    //Functions
    public static function getLeaveByIdDate($user_id, $date){

        $result = Leave::where('user_id',$user_id)
            ->where('from_date', '>=', $date)
            ->where('to_date', '<=', $date)
            ->get();

        return $result;
    }

    public static function getLeaveTypes($leave_type){

        $result = LeaveType::where('leave_type',$leave_type)->first();

        return $result;
    }


    //Rules
    public static function createRules(){

        return [
            'id_number'    => 'required|exists:learners,id_number',
            'attachment'   => 'sometimes|nullable',
            'comments'      => 'sometimes|nullable',
            'from_date'    => 'required|date|date_format:"Y-m-d"',
            'to_date'      => 'required|date|date_format:"Y-m-d"',
            'reason'       => 'required|exists:leave_types,id'
        ];
    }

    public static function updateRules($id){

        return [
            'id_number'    => 'required|exists:learners,id_number',
            'attachment'   => 'sometimes|nullable',
            'comments'      => 'sometimes|nullable',
            'from_date'    => 'required|date|date_format:"Y-m-d"',
            'to_date'      => 'required|date|date_format:"Y-m-d"',
            'reason'       => 'required|exists:leave_types,id'
        ];
    }

    public static function leaveReport(){

        return [
            'from_date'    => 'required|date_format:"Y-m-d"',
            'to_date'      => 'required|date_format:"Y-m-d"',
            'company'      => 'sometimes|nullable|exists:companies,id',
            'site'         => 'sometimes|nullable|exists:sites,id',
            'reason'       => 'sometimes|nullable|exists:leave_types,id',
        ];
    }



    //Models
    public static function leaveModel($item){

        return  [
            'id'                => $item->id,
            'id_number'         => $item->id_number,
            'attachment'        => $item->attachment,
            'comments'          => $item->comments,
            'from_date'         => Helpers::formatDate($item->from_date, "Y-m-d"),
            'to_date'           => Helpers::formatDate($item->to_date, "Y-m-d"),
            'reason'            => LeaveType::leaveTypeInfo($item->reason),
            'learner'           => Learner::learnerInformation($item->id_number),
            'created_at'        => Helpers::formatDate($item->created_at),
            'updated_at'        => Helpers::formatDate($item->updated_at),
            'created_by'        => User::userInformation($item->created_by),
            'last_updated_by'   => ($item->last_updated_by != null && $item->last_updated_by != "") ? User::userInformation($item->last_updated_by) : null,
        ];
    }
}