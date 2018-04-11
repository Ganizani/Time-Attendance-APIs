<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:31 AM
 */

namespace App\Http\Controllers\Holiday;
use App\Department;
use App\Holiday;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;


/**
 * Class HolidayController
 *
 * @package App\Http\Controllers\Holiday
 */
class HolidayController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holidays = array();
        $result = Holiday::all();
        foreach($result as $item){
            $holidays [] = Holiday::model($item);
        }

        return $this->showList(collect($holidays));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Holiday::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $holiday = new Holiday();
        $holiday->name          = $request->name;
        $holiday->description   = $request->description;
        $holiday->date          = $request->date;
        $holiday->department_id = $request->department;
        $holiday->created_by    = $request->user()->id;
        $holiday->created_at    = Carbon::now('CAT');
        $holiday->updated_at    = null;
        $holiday->save();

        //return
        return $this->showList(collect(Holiday::model($holiday)));
    }

    public function store_upload(Request $request)
    {
        $validator = Validator::make($request->all(), Holiday::uploadRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $holiday = new Holiday();
        $holiday->name         = $request->name;
        $holiday->description  = $request->description;
        $holiday->date         = $request->date;
        $holiday->department_id = $request->department_id;
        $holiday->created_by   = $request->user()->id ;
        $holiday->created_at   = Carbon::now();
        $holiday->updated_at   = null;
        $holiday->save();

        //return
        return $this->showMessage("Holiday Successfully Created");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $holiday = Holiday::model(Holiday::where('id', $id)->firstOrFail());

        return $this->showOne(collect($holiday));
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
        $holiday = Holiday::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), Holiday::updateRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $holiday->name          = $request->name;
        $holiday->description   = $request->description;
        $holiday->date          = $request->date;
        $holiday->department_id = $request->department;
        if($holiday->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',422);
        }
        $holiday->updated_by = $request->user()->id;
        $holiday->updated_at = Carbon::now('CAT');
        $holiday->save();

        //return
        return $this->showList(collect(Holiday::model($holiday)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = Holiday::where('id', $id)->firstOrFail();
        $holiday->delete();

        return $this->showList(collect(Holiday::model($holiday)));
    }

    public function monthly_holidays(Request $request)
    {
        $holidays = Holiday::getMonthlyHolidays($request->date, $request->department);

        return $this->showList(collect($holidays));
    }
}