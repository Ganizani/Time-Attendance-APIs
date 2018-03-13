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
        'device_id',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

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

    public static function getRecordsInDateExcludeHoliday($from = "", $to = "", $company ="" ,$weekends = ""){

        $query = "SELECT date 
                  FROM   records 
                  WHERE  date BETWEEN '{$from}' AND '{$to}' AND date NOT IN (SELECT date FROM holidays WHERE company_id = '{$company}') AND date NOT IN ({$weekends})
                  GROUP BY date ORDER BY date ASC";

        $records = DB::select($query);

        return $records;
    }



    //Rules
    public static function createRules(){

        return [
            'user_id'     => 'required|exists:learners,id',
            'device_id'   => 'required|exists:devices,id',
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
            'company'      => 'sometimes|nullable|exists:companies,id',
        ];
    }

    public static function absenteeReportRules(){

        return [
            'from_date'  => 'required|date_format:"Y-m-d"',
            'to_date'    => 'required|date_format:"Y-m-d"',
            'company'    => 'sometimes|nullable|exists:companies,id'
        ];
    }

    public static function attendanceReportRules(){

        return [
            'from_date'  => 'required|date_format:"Y-m-d"',
            'to_date'    => 'required|date_format:"Y-m-d"',
            'company'    => 'sometimes|nullable|exists:companies,id'
        ];
    }

    public static function registrationReportRules(){

        return [
            'from_date'  => 'required|date_format:"Y-m-d"',
            'to_date'    => 'required|date_format:"Y-m-d"',
            'company'    => 'sometimes|nullable|exists:companies,id',
        ];
    }

    public static function stipendReportRules(){

        return [
            'from_date'  => 'required|date_format:"Y-m-d"',
            'to_date'    => 'required|date_format:"Y-m-d"',
            'company'    => 'sometimes|nullable|exists:companies,id'
        ];
    }


    //Models
    public static function recordInfo($item){

        return  [
            'id'            => $item->id,
            'date'          => Helpers::formatDate($item->date, "Y-m-d"),
            'time'          => Helpers::formatDate($item->time, "H:i:s"),
            'latitude'      => $item->latitude,
            'longitude'     => $item->longitude,
            'status'        => $item->status,
            'user_id'       => $item->user_id,
            'device_id'     => $item->device_id,
            'user'          => User::userInformation($item->user_id),
            'device'        => Device::deviceInformation($item->device_id),
        ];
    }

    public static function recordModel($item){

        return  [
            'id'            => $item->id,
            'date'          => Helpers::formatDate($item->date, "Y-m-d"),
            'time'          => Helpers::formatDate($item->time, "H:i:s"),
            'latitude'      => $item->latitude,
            'longitude'     => $item->longitude,
            'status'        => $item->status,
            'user_id'       => $item->user_id,
            'device_id'     => $item->device_id,
            'user'          => User::userInformation($item->user_id),
            'device'        => Device::deviceInformation($item->device_id),
            'created_at'    => Helpers::formatDate($item->created_at),
        ];
    }
}
