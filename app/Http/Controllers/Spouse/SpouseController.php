<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:31 AM
 */

namespace App\Http\Controllers\Spouse;
use App\Address;
use App\Http\Controllers\ApiController;
use App\Spouse;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;


/**
 * Class SpouseController
 *
 * @package App\Http\Controllers\Spouse
 */
class SpouseController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $spouses = [];
        $result = Spouse::all();
        foreach($result as $item){
            $spouses [] = Spouse::model($item);
        }

        //return
        return $this->showList(collect($spouses));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Spouse::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $spouse = new Spouse();
        $spouse->name           = $request->name;
        $spouse->employer       = $request->employer;
        $spouse->work_location  = $request->work_location;
        $spouse->cell_phone     = $request->cell_phone;
        $spouse->work_phone     = $request->work_phone;
        $spouse->created_by     = $request->user()->id;
        $spouse->created_at     = Carbon::now('CAT');
        $spouse->updated_at     = null;
        $spouse->save();

        //return
        return $this->showList(collect(Spouse::model($spouse)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $spouse = Spouse::model(Spouse::where('id', $id)->firstOrFail());

        return $this->showOne(collect($spouse));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $spouse = Spouse::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), Spouse::updateRules($id));

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $spouse->name           = $request->name;
        $spouse->employer       = $request->employer;
        $spouse->work_location  = $request->work_location;
        $spouse->cell_phone     = $request->cell_phone;
        $spouse->work_phone     = $request->work_phone;

        if($spouse->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',400);
        }
        $spouse->updated_by = $request->user()->id;
        $spouse->updated_at = Carbon::now('CAT');
        $spouse->save();

        //return
        return $this->showOne(collect(Spouse::model($spouse)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $spouse = Spouse::where('id', $id)->firstOrFail();
        $spouse->delete();

        //return
        return $this->showOne(collect(Spouse::model($spouse)));
    }
}