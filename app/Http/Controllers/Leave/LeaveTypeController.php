<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:31 AM
 */

namespace App\Http\Controllers\Leave;
use App\Http\Controllers\ApiController;
use App\LeaveType;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;

/**
 * Class LeaveTypeController
 *
 * @package App\Http\Controllers\Leave
 */
class LeaveTypeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leave_types = array();
        $result = LeaveType::all();
        foreach($result as $item){
            $leave_types [] = LeaveType::model($item);
        }

        return $this->showList(collect($leave_types));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), LeaveType::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $leave_type = new LeaveType();
        $leave_type->name             = $request->name;
        $leave_type->description      = $request->description;
        $leave_type->created_by       = $request->user()->id ;
        $leave_type->created_at       = Carbon::now();
        $leave_type->save();

        //return
        return $this->showList(collect(LeaveType::model($leave_type)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leave_type = LeaveType::model(LeaveType::where('id', $id)->firstOrFail());

        return $this->showOne(collect($leave_type));
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
        $leave_type = LeaveType::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), LeaveType::updateRules($id));

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $leave_type->name             = $request->name;
        $leave_type->description      = $request->description;
        if($leave_type->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',400);
        }

        $leave_type->updated_by = $request->user()->id;
        $leave_type->updated_at = Carbon::now();
        $leave_type->save();

        //return
        return $this->showList(collect(LeaveType::model($leave_type)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leave_type = LeaveType::where('id', $id)->firstOrFail();
        $leave_type->delete();

        //return
        return $this->showList(collect(LeaveType::model($leave_type)));
    }
}
