<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:31 AM
 */

namespace App\Http\Controllers\Report;

use App\Holiday;
use App\Http\Controllers\ApiController;
use App\Leave;
use App\LeaveType;
use App\ReportLog;
use App\Stipend;
use App\User;
use App\Learner;
use App\Record;
use Carbon\Carbon;
use App\Http\Helpers;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;
use Validator;
use Illuminate\Http\Request;

/**
 * Class ReportController
 *
 * @package App\Http\Controllers\Report
 */
class ReportController extends ApiController
{

    /**
     * Return Absentee Report information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function absentee(Request $request){

        $data = [];
        $validator = Validator::make($request->all(), Record::absenteeReportRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        //Swap From and To
        $from = $request->from_date ;
        $to   = $request->to_date;
        Helpers::swapDates($from, $to);

        $dates = Helpers::getDatesArray($from, $to);
        $users = User::getUsers($request->department);

        foreach ($dates as $today){
            foreach ($users as $user_data) {
                $count = Record::countClockingByIdDate($today, $user_data->id);
                $check_holiday = Holiday::isHoliday($today, $user_data->department_id);

                if(!$count && !$check_holiday){
                    $leave_data = Leave::getLeaveByIdDate($user_data->id, $today);

                    //check if filled in leaveData
                    $reason = null;
                    if(count($leave_data) > 0){
                        $reason = LeaveType::info($leave_data->leave_type);
                        $reason = $reason['name'];
                    }

                    $data [] = [
                        'user'    => User::info($user_data->id),
                        'date'    => $today,
                        'weekday' => strtoupper(Helpers::formatDate($today, "l" )),
                        'reason'  => $reason
                    ];

                }
            }
        }

        //return
        return $this->showList(collect($data));
    }

    /**
     * Return Attendance information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request){

        $data = [];
        $validator = Validator::make($request->all(), Record::attendanceReportRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        //Swap From and To
        $from = $request->from_date ;
        $to   = $request->to_date;
        Helpers::swapDates($from, $to);

        $dates = Record::getDate($from, $to);
        $users = User::getUsers($request->department);
        foreach ($dates as $date){
            foreach ($users as $user_data) {
                $count = Record::countClockingByIdDate($date['date'], $user_data->id);

                if($count > 0){
                    $in  = Record::getClockingByIdDate($date['date'], $user_data->id, "IN");
                    $out = Record::getClockingByIdDate($date['date'], $user_data->id, "OUT");

                    $data [] = [
                        'user' => User::info($user_data->id),
                        'record'  => [
                            'in'  => Record::info($in),
                            'out' => Record::info($out)
                        ]
                    ];

                }
            }
        }

        return $this->showList(collect($data));

    }

    /**
     * Return Base Report information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function base(Request $request){

        $records = array();
        $validator = Validator::make($request->all(), Record::indexRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }
        $from = $request->from_date;
        $to   = $request->to_date;
        Helpers::swapDates($from,$to);

        $WHEREDepartment = "";
        if(isset($request->department) && $request->department != "") $WHEREDepartment = " AND u.department_id = '$request->department'";

        $results = DB::select("SELECT r.*
                               FROM   records r, users u
                               WHERE  r.user_id = u.id AND date BETWEEN '$from' AND '$to' {$WHEREDepartment}");

        foreach($results as $item){
            $records [] = Record::model($item);
        }

        return $this->showList(collect($records));
    }

    /**
     * Return Leave Report information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function leave(Request $request){

        $leaves = array();
        $validator = Validator::make($request->all(), Leave::leaveReport());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }
        $from = $request->from_date;
        $to   = $request->to_date;
        Helpers::swapDates($from,$to);

        $WHEREDepartment = $WHERELeaveType =  "";
        if(isset($request->department) && $request->department != "") $WHEREDepartment = " AND u.department_id = '$request->department'";
        if(isset($request->leave_type) && $request->leave_type != "") $WHERELeaveType = " AND le.leave_type = '$request->leave_type'";

        $results = DB::select("SELECT le.*
                               FROM   leaves le, users u
                               WHERE  le.user_id = u.id AND (le.from_date BETWEEN '$from' AND '$to' OR  le.to_date BETWEEN '$from' AND '$to') {$WHEREDepartment}{$WHERELeaveType}");

        foreach($results as $item){
            $leaves [] = Leave::model($item);
        }

        return $this->showList(collect($leaves));
    }


    /**
     * Return Map Report information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function map(Request $request){
        $data = [];

        return $this->showList(collect($data));
    }

    /**
     * Store Report Log information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report_log(Request $request){

        $report_log = new ReportLog();
        $report_log->report_name    = $request->report;
        $report_log->from_date      = $request->from_date;
        $report_log->to_date        = $request->to_date;
        $report_log->department_id  = $request->department;
        $report_log->user_id        = $request->user()->id;
        $report_log->created_at     = Carbon::now();
        $report_log->updated_at     = null;
        $report_log->save();

        return $this->showOne(collect($report_log), 201);
    }
}
