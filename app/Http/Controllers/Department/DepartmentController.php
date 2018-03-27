<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/27/18
 * Time: 10:07 PM
 */

namespace App\Http\Controllers\Department;
use App\Department;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;


/**
 * Class DeviceController
 *
 * @package App\Http\Controllers\Department
 */
class DepartmentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = [];
        $result = Department::all();
        foreach($result as $item){
            $departments [] = Department::model($item);
        }

        //return
        return $this->showList(collect($departments));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Department::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $department = new Department();
        $department->name        = $request->name;
        $department->description = $request->description;
        $department->location    = $request->location;
        $department->created_by  = $request->created_by;
        $department->created_at  = Carbon::now();
        $department->updated_at  = null;
        $department->save();

        //return
        return $this->showList(collect(Department::model($department)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $department = Department::where('id', $id)->firstOrFail();

        //return
        return $this->showOne(collect(Department::model($department)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $department = Department::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), Department::updateRules($id));

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $department->name        = $request->name;
        $department->description = $request->description;
        $department->location    = $request->location;

        if($department->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',400);
        }
        $department->updated_by = $request->updated_by;
        $department->updated_at = Carbon::now();
        $department->save();

        //return
        return $this->showOne(collect(Department::model($department)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $department = Department::where('id', $id)->firstOrFail();
        $department->delete();

        //return
        return $this->showOne(collect(Department::model($department)));
    }
}