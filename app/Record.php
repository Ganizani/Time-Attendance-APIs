<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:57 PM
 */

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers;


class Record extends Model
{
    protected $table = 'records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'longitude',
        'latitude',
        'imei_number',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    //Functions
    public static function countClockingByIdDate($date = "", $user_id = ""){

        $records = Record::where('user_id', $user_id)
            ->where('date', $date)
            ->count();

        return $records;
    }

    public static function getClockingByIdDate($date = "", $user_id = "", $status = ""){

        $order = ($status != "" && $status == "IN")? "ASC" : "DESC";

        $record = Record::where('user_id', $user_id)
            ->where('date', $date)
            ->orderBy('time', $order)
            ->first();

        return $record;
    }

    public static function getDate($from = "", $to = ""){

        $records = Record::select('date')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date')
            ->get();
        return $records;
    }

    public static function getRecordsInDateExcludeHoliday($from = "", $to = "", $department ="" ,$weekends = ""){

        $query = "SELECT date 
                  FROM   records 
                  WHERE  date BETWEEN '{$from}' AND '{$to}' AND date NOT IN (SELECT date FROM holidays WHERE department_id = '{$department}') AND date NOT IN ({$weekends})
                  GROUP BY date ORDER BY date ASC";

        $records = DB::select($query);

        return $records;
    }



    //Rules
    public static function createRules(){

        return [
            'user'        => 'required|exists:users,id',
            'imei_number' => 'sometimes|nullable|exists:devices,imei_number',
            'date'        => 'required|date_format:"Y-m-d"',
            'time'        => 'required|date_format:"H:s:i"',
            'latitude'    => 'required',
            'longitude'   => 'required'
        ];
    }

    public static function indexRules(){

        return [
            'from_date'    => 'required|date_format:"Y-m-d"',
            'to_date'      => 'required|date_format:"Y-m-d"',
            'department'   => 'sometimes|nullable|exists:departments,id',
        ];
    }

    public static function absenteeReportRules(){

        return [
            'from_date'  => 'required|date_format:"Y-m-d"',
            'to_date'    => 'required|date_format:"Y-m-d"',
            'department' => 'sometimes|nullable|exists:departments,id',
        ];
    }

    public static function attendanceReportRules(){

        return [
            'from_date'  => 'required|date_format:"Y-m-d"',
            'to_date'    => 'required|date_format:"Y-m-d"',
            'department'   => 'sometimes|nullable|exists:departments,id',
        ];
    }

    public static function registrationReportRules(){

        return [
            'from_date'  => 'required|date_format:"Y-m-d"',
            'to_date'    => 'required|date_format:"Y-m-d"',
            'department'   => 'sometimes|nullable|exists:departments,id',
        ];
    }


    //Models
    public static function info($item){

        return  [
            'id'            => $item->id,
            'date'          => Helpers::formatDate($item->date, "Y-m-d"),
            'time'          => Helpers::formatDate($item->time, "H:i:s"),
            'latitude'      => $item->latitude,
            'longitude'     => $item->longitude,
            'status'        => $item->status,
            'user'          => User::info($item->user_id),
            'device'        => Device::info($item->imei_number),
        ];
    }

    public static function model($item){

        return  [
            'id'            => $item->id,
            'date'          => Helpers::formatDate($item->date, "Y-m-d"),
            'time'          => Helpers::formatDate($item->time, "H:i:s"),
            'latitude'      => $item->latitude,
            'longitude'     => $item->longitude,
            'status'        => $item->status,
            'user'          => User::info($item->user_id),
            'device'        => Device::info($item->imei_number),
            'created_at'    => Helpers::formatDate($item->created_at),
        ];
    }
}
