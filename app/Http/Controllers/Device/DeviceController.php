<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:30 AM
 */

namespace App\Http\Controllers\Device;
use App\Device;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;


/**
 * Class DeviceController
 *
 * @package App\Http\Controllers\Device
 */
class DeviceController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devices = array();

        $result = Device::all();

        foreach($result as $item){
            $devices [] = Device::model($item);
        }

        return $this->showList(collect($devices));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Device::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $device = new Device();
        $device->imei_number    = $request->imei_number;
        $device->serial_number  = $request->serial_number;
        $device->name           = $request->name;
        $device->phone_number   = $request->phone_number;
        $device->supervisor     = $request->supervisor;
        $device->status         = $request->status;
        $device->department_id  = $request->department;
        $device->created_by     = $request->created_by;
        $device->created_at     = Carbon::now();
        $device->updated_at     = null;
        $device->save();

        //return
        return $this->showList(collect(Device::model($device)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $device = Device::model(Device::where('id', $id)->firstOrFail());

        return $this->showOne(collect($device));
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
        $device = Device::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), Device::updateRules($id));

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $device->imei_number    = $request->imei_number;
        $device->serial_number  = $request->serial_number;
        $device->name           = $request->name;
        $device->phone_number   = $request->phone_number;
        $device->supervisor     = $request->supervisor;
        $device->status         = $request->status;
        $device->department_id  = $request->department;

        if($device->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',400);
        }
        $device->updated_by = $request->updated_by;
        $device->updated_at = Carbon::now();
        $device->save();

        //return
        return $this->showOne(collect(Device::model($device)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $device = Device::where('id', $id)->firstOrFail();
        $device->delete();

        //return
        return $this->showOne(collect(Device::model($device)));
    }
}