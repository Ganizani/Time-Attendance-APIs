<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 9:53 AM
 */

namespace App\Http\Controllers\NextOfKin;
use App\Address;
use App\Http\Controllers\ApiController;
use App\NextOfKin;
use App\Spouse;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;


/**
 * Class NextOfKinController
 *
 * @package App\Http\Controllers\NextOfKin
 */
class NextOfKinController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $next_of_kins = [];
        $result = NextOfKin::all();
        foreach($result as $item){
            $next_of_kins [] = NextOfKin::model($item);
        }

        //return
        return $this->showList(collect($next_of_kins));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate Next of Kin
        $validator = Validator::make($request->all(), NextOfKin::createRules());
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        //Validate Address
        $validator = Validator::make($request->address, Address::createRules());
        if ($validator->fails())return $this->errorResponse($validator->errors(), 400);

        //Address
        $address = new Address();
        $address->house_no      = $request->address['house_no'];
        $address->street_no     = $request->address['street_no'];
        $address->street_name   = $request->address['street_name'];
        $address->suburb        = $request->address['suburb'];
        $address->city          = $request->address['city'];
        $address->province      = $request->address['province'];
        $address->country       = $request->address['country'];
        $address->province      = $request->address['province'];
        $address->postal_code   = $request->address['postal_code'];
        $address->created_at    = Carbon::now();
        $address->updated_at    = null;
        $address->save();

        $next_of_kin = new NextOfKin();
        $next_of_kin->title         = $request->title;
        $next_of_kin->first_name    = $request->first_name;
        $next_of_kin->last_name     = $request->last_name;
        $next_of_kin->middle_name   = $request->middle_name;
        $next_of_kin->email         = $request->email;
        $next_of_kin->cell_phone    = $request->cell_phone;
        $next_of_kin->home_phone    = $request->home_phone;
        $next_of_kin->relationship  = $request->relationship;
        $next_of_kin->address_id    = $request->address;
        $next_of_kin->created_by    = $request->created_by;
        $next_of_kin->created_at    = Carbon::now();
        $next_of_kin->updated_at    = null;
        $next_of_kin->save();

        //return
        return $this->showList(collect(NextOfKin::model($next_of_kin)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $next_of_kin = NextOfKin::model(NextOfKin::where('id', $id)->firstOrFail());

        return $this->showOne(collect($next_of_kin));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $next_of_kin = NextOfKin::where('id', $id)->firstOrFail();
        $address     = Address::where('id', $next_of_kin->address_id)->first();

        //Validate Next Of Kin
        $validator = Validator::make($request->all(), NextOfKin::updateRules($id));
        if ($validator->fails())return $this->errorResponse($validator->errors(), 400);

        //Validate Address
        $validator = Validator::make($request->address, Address::updateRules($id));
        if ($validator->fails())return $this->errorResponse($validator->errors(), 400);

        //Address
        $address->house_no      = $request->address['house_no'];
        $address->street_no     = $request->address['street_no'];
        $address->street_name   = $request->address['street_name'];
        $address->suburb        = $request->address['suburb'];
        $address->city          = $request->address['city'];
        $address->province      = $request->address['province'];
        $address->country       = $request->address['country'];
        $address->province      = $request->address['province'];
        $address->postal_code   = $request->address['postal_code'];

        //Next of Kin
        $next_of_kin->title         = $request->title;
        $next_of_kin->first_name    = $request->first_name;
        $next_of_kin->last_name     = $request->last_name;
        $next_of_kin->middle_name   = $request->middle_name;
        $next_of_kin->email         = $request->email;
        $next_of_kin->cell_phone    = $request->cell_phone;
        $next_of_kin->home_phone    = $request->home_phone;
        $next_of_kin->relationship  = $request->relationship;

        if($next_of_kin->isClean() && $address->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',400);
        }
        $next_of_kin->updated_by = $request->updated_by;
        $next_of_kin->updated_at = Carbon::now();
        $next_of_kin->save();

        $address->updated_by = $request->updated_by;
        $address->updated_at = Carbon::now();
        $address->save();

        //return
        return $this->showOne(collect(NextOfKin::model($next_of_kin)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $next_of_kin = NextOfKin::where('id', $id)->firstOrFail();
        $next_of_kin->delete();

        //return
        return $this->showOne(collect(NextOfKin::model($next_of_kin)));
    }
}