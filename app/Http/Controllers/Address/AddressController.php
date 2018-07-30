<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:30 AM
 */

namespace App\Http\Controllers\Address;
use App\Address;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;


/**
 * Class DeviceController
 *
 * @package App\Http\Controllers\Device
 */
class AddressController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addresses = [];
        $result = Address::all();
        foreach($result as $item){
            $addresses [] = Address::model($item);
        }

        //return
        return $this->showList(collect($addresses));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Address::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $address = new Address();
        $address->house_no       = $request->house_number;
        $address->street_no      = $request->street_number;
        $address->street_name    = $request->street_name;
        $address->suburb         = $request->suburb;
        $address->city           = $request->city;
        $address->province       = $request->province;
        $address->country        = $request->country;
        $address->postal_code    = $request->postal_code;
        $address->created_by     = $request->user()->id;
        $address->created_at     = Carbon::now('CAT');
        $address->updated_at     = null;
        $address->save();

        //return
        return $this->showList(collect(Address::model($address)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $address = Address::model(Address::where('id', $id)->firstOrFail());

        return $this->showOne(collect($address));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $address = Address::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), Address::updateRules($id));

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $address->house_no       = $request->house_number;
        $address->street_no      = $request->street_number;
        $address->street_name    = $request->street_name;
        $address->suburb         = $request->suburb;
        $address->city           = $request->city;
        $address->province       = $request->province;
        $address->country        = $request->country;
        $address->postal_code    = $request->postal_code;

        if($address->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',400);
        }
        $address->updated_by = $request->user()->id;
        $address->updated_at = Carbon::now('CAT');
        $address->save();

        //return
        return $this->showOne(collect(Address::model($address)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $address = Address::where('id', $id)->firstOrFail();
        $address->deleted_by = $request->user()->id;
        $address->save();
        $address->delete();

        //return
        return $this->showOne(collect(Address::model($address)));
    }
}