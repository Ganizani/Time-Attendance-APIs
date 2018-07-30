<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 2018/07/29
 * Time: 08:19
 */

namespace App\Http\Controllers\User;
use App\AccessControl;
use App\Http\Controllers\ApiController;
use App\UserGroup;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;


/**
 * Class UserGroupController
 *
 * @package App\Http\Controllers\User
 */
class UserGroupController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_groups = [];
        $result = UserGroup::all();
        foreach($result as $item){
            $user_groups [] = UserGroup::model($item);
        }

        //return
        return $this->showList(collect($user_groups));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate User Group
        $validator = Validator::make($request->all(), UserGroup::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        //Validate Access Control
        $validator = Validator::make($request->access_control, AccessControl::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);


        //Add User Group
        $user_group = new UserGroup();
        $user_group->name           = $request->name;
        $user_group->description    = $request->description;
        $user_group->created_by     = $request->user()->id;
        $user_group->created_at     = Carbon::now('CAT');
        $user_group->updated_at     = null;
        $user_group->save();

        //Add AccessControl
        AccessControl::map_data($user_group->id, $request);

        //return
        return $this->showList(collect(UserGroup::model($user_group)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_group = UserGroup::model(UserGroup::where('id', $id)->firstOrFail());

        return $this->showOne(collect($user_group));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $user_group = UserGroup::where('id', $id)->firstOrFail();

        //Validate User Group
        $validator = Validator::make($request->all(), UserGroup::updateRules($id));
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        //Validate Access Control
        $access_control_data = $request->access_control;
        $access_control_id   = isset($access_control_data['access_control_id']) ? $access_control_data['access_control_id'] : null;
        $validator = Validator::make($request->access_control, AccessControl::updateRules($access_control_id));
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        //Update User Group
        $user_group->name        = $request->name;
        $user_group->description = $request->description;

        //Update Access Control
        $access_control = AccessControl::map_data($user_group->id, $request, "edit", $access_control_id);

        /*if($user_group->isClean() && $access_control->isClean()){ //if the site has not changed
            return $this->errorResponse('You need to specify a different value to update',400);
        }*/
        $user_group->updated_by = $request->user()->id;
        $user_group->updated_at = Carbon::now('CAT');
        $user_group->save();

        //return
        return $this->showOne(collect(UserGroup::model($user_group)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_group = UserGroup::where('id', $id)->firstOrFail();
        $user_group->delete();

        //return
        return $this->showOne(collect(UserGroup::model($user_group)));
    }
}