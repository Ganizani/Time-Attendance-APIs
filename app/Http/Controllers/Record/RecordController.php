<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:31 AM
 */

namespace App\Http\Controllers\Record;
use App\Record;
use App\User;
use App\Http\Controllers\ApiController;
use App\Http\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Http\Request;

/**
 * Class RecordController
 *
 * @package App\Http\Controllers\Record
 */
class RecordController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $records = array();
        $validator = Validator::make($request->all(), Record::indexRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $from = $request->from_date;
        $to   = $request->to_date;
        Helpers::swapDates($from,$to);

        $WHEREDepartment = "";
        if(isset($request->department) && $request->department != ""){
            $WHEREDepartment = " AND u.department_id = '$request->department'";
        }

        //Recently Added
        $ORDERBY = "";
        if(isset($request->recently) && $request->recently == "true"){
            $ORDERBY = "ORDER BY r.time DESC LIMIT 20";
        }
        $results = DB::select("SELECT r.*
                               FROM   records r, users u
                               WHERE  r.user_id = u.id AND date BETWEEN '$from' AND '$to' {$WHEREDepartment}
                               {$ORDERBY}");

        foreach($results as $item){
            $records [] = Record::model($item);
        }

        return $this->showList(collect($records));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Record::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $record = new Record();
        $record->user_id       = $request->user;
        $record->imei_number   = $request->imei_number;
        $record->date          = $request->date;
        $record->time          = $request->time;
        $record->latitude      = $request->latitude;
        $record->longitude     = $request->longitude;
        $record->status        = $request->status;
        $record->created_at    = Carbon::now();
        $record->updated_at    = null;
        $record->save();

        //return
        return $this->showList(collect(Record::model($record)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $record = Record::model(Record::where('id', $id)->firstOrFail());

        return $this->showOne(collect($record));
    }
}