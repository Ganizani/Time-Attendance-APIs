<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:31 AM
 */

namespace App\Http\Controllers\Leave;
use App\Address;
use App\Http\Helpers;
use App\Leave;
use App\LeaveType;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;


/**
 * Class LeaveController
 *
 * @package App\Http\Controllers\Leave
 */
class LeaveController extends ApiController
{
    private $helper;

    function __construct(){

        $this->helper = new Helpers();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leaves = array();
        $result = Leave::all();
        foreach($result as $item){
            $leaves [] = Leave::model($item);
        }

        return $this->showList(collect($leaves));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), Leave::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $leave = new Leave();
        $leave->user_id           = $request->user;
        $leave->attachment        = Helpers::saveFile($request, "leave_attachment");
        $leave->last_day_of_work  = $request->last_day_of_work;
        $leave->from_date         = $request->from_date;
        $leave->to_date           = $request->to_date;
        $leave->comments          = $request->comments;
        $leave->leave_type        = $request->leave_type;
        $leave->address_on_leave  = $request->address_on_leave;
        $leave->email_on_leave    = $request->email_on_leave;
        $leave->phone_on_leave    = $request->phone_on_leave;
        $leave->created_by        = $request->user()->id;
        $leave->created_at        = Carbon::now('CAT');
        $leave->updated_at        = null;
        $leave->save();

        //return
        return $this->showList(collect(Leave::model($leave)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leave = Leave::model(Leave::where('id', $id)->firstOrFail());

        return $this->showOne(collect($leave));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $leave = Leave::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), Leave::updateRules($id));

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }
        $file = isset($request->attachment) ? $request->attachment : [];
        $path = isset($request->old_attachment) ? $request->old_attachment : null;
        if(isset($file['extension']) && isset($file['data_url']) && $file['extension'] != "" && $file['data_url'] != ""){
            $path = Helpers::saveFile($request, "leave_attachment");
        }

        $leave->attachment        = $path;
        $leave->last_day_of_work  = $request->last_day_of_work;
        $leave->from_date         = $request->from_date;
        $leave->to_date           = $request->to_date;
        $leave->comments          = $request->comments;
        $leave->leave_type        = $request->leave_type;
        $leave->address_on_leave  = $request->address_on_leave;
        $leave->email_on_leave    = $request->email_on_leave;
        $leave->phone_on_leave    = $request->phone_on_leave;

        if($leave->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',400);
        }
        $leave->updated_by = $request->user()->id;
        $leave->updated_at = Carbon::now('CAT');
        $leave->save();

        //return
        return $this->showOne(collect(Leave::model($leave)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leave = Leave::where('id', $id)->firstOrFail();
        $leave->delete();

        //return
        return $this->showOne(collect(Leave::model($leave)));
    }
}