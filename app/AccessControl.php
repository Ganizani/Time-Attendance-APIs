<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 2018/07/29
 * Time: 07:35
 */

namespace App;
use App\Http\Helpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AccessControl extends Model
{
    use SoftDeletes;

    protected $table = 'access_control';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_group_id',
        'login',
        'system_admin',
        'add_report',
        'edit_report',
        'list_report',
        'print_report',
        'add_departments',
        'edit_departments',
        'list_departments',
        'print_departments',
        'add_devices',
        'edit_devices',
        'list_devices',
        'print_devices',
        'add_leaves',
        'edit_leaves',
        'print_leaves',
        'list_leaves',
        'upload_leaves',
        'add_leave_type',
        'edit_leave_type',
        'list_leave_type',
        'print_leave_type',
        'add_holidays',
        'edit_holidays',
        'list_holidays',
        'edit_holidays',
        'print_holidays',
        'add_users',
        'list_users',
        'edit_users',
        'print_users',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    //Functions
    public static function map_data($user_group_id, $request, $action = "add", $access_control_id = ""){

        $data = isset($request->access_control) ? $request->access_control : [];

        if($action == "add"){
            $access_control = new AccessControl();
        }
        else{
            $access_control = AccessControl::where('id', $access_control_id)->first();
            if(!$access_control) $access_control = new AccessControl();
        }

        $access_control->user_group_id       = ($user_group_id != "") ? $user_group_id : null;
        //Admin
        $access_control->system_admin        = (isset($data['system_admin']) && $data['system_admin'] != "") ? $data['system_admin'] : 0;
        $access_control->login               = (isset($data['login']) && $data['login'] != "") ? $data['login'] : 0;
        //Report
        $access_control->add_report          = (isset($data['add_report'])   && $data['add_report'] != "")   ? $data['add_report']   : 0;
        $access_control->edit_report         = (isset($data['edit_report'])  && $data['edit_report'] != "")  ? $data['edit_report']  : 0;
        $access_control->list_report         = (isset($data['list_report'])  && $data['list_report'] != "")  ? $data['list_report']  : 0;
        $access_control->print_report        = (isset($data['print_report']) && $data['print_report'] != "") ? $data['print_report'] : 0;
        //Department
        $access_control->add_departments     = (isset($data['add_departments']) && $data['add_departments'] != "") ? $data['add_departments'] : 0;
        $access_control->edit_departments    = (isset($data['edit_departments']) && $data['edit_departments'] != "") ? $data['edit_departments'] : 0;
        $access_control->print_departments   = (isset($data['print_departments']) && $data['print_departments'] != "") ? $data['print_departments'] : 0;
        $access_control->list_departments    = (isset($data['list_departments']) && $data['list_departments'] != "") ? $data['list_departments'] : 0;
        //Devices
        $access_control->add_devices         = (isset($data['add_devices']) && $data['add_devices'] != "") ? $data['add_devices'] : 0;
        $access_control->edit_devices        = (isset($data['edit_devices']) && $data['edit_devices'] != "") ? $data['edit_devices'] : 0;
        $access_control->print_devices       = (isset($data['print_devices']) && $data['print_devices'] != "") ? $data['print_devices'] : 0;
        $access_control->list_devices        = (isset($data['list_devices']) && $data['list_devices'] != "") ? $data['list_devices'] : 0;
        //Leaves
        $access_control->add_leaves          = (isset($data['add_leaves']) && $data['add_leaves'] != "") ? $data['add_leaves'] : 0;
        $access_control->edit_leaves         = (isset($data['edit_leaves']) && $data['edit_leaves'] != "") ? $data['edit_leaves'] : 0;
        $access_control->print_leaves        = (isset($data['print_leaves']) && $data['print_leaves'] != "") ? $data['print_leaves'] : 0;
        $access_control->list_leaves         = (isset($data['list_leaves']) && $data['list_leaves'] != "") ? $data['list_leaves'] : 0;
        //Leaves Types
        $access_control->add_leave_types     = (isset($data['add_leave_types']) && $data['add_leave_types'] != "") ? $data['add_leave_types'] : 0;
        $access_control->edit_leave_types    = (isset($data['edit_leave_types']) && $data['edit_leave_types'] != "") ? $data['edit_leave_types'] : 0;
        $access_control->print_leave_types   = (isset($data['print_leave_types']) && $data['print_leave_types'] != "") ? $data['print_leave_types'] : 0;
        $access_control->list_leave_types    = (isset($data['list_leave_types']) && $data['list_leave_types'] != "") ? $data['list_leave_types'] : 0;
        //Holiday
        $access_control->add_holidays        = (isset($data['add_holidays']) && $data['add_holidays'] != "") ? $data['add_holidays'] : 0;
        $access_control->edit_holidays       = (isset($data['edit_holidays']) && $data['edit_holidays'] != "") ? $data['edit_holidays'] : 0;
        $access_control->print_holidays      = (isset($data['print_holidays']) && $data['print_holidays'] != "") ? $data['print_holidays'] : 0;
        $access_control->list_holidays       = (isset($data['list_holidays']) && $data['list_holidays'] != "") ? $data['list_holidays'] : 0;
        //User
        $access_control->add_users          = (isset($data['add_users']) && $data['add_users'] != "") ? $data['add_holidays'] : 0;
        $access_control->edit_users         = (isset($data['edit_users']) && $data['edit_users'] != "") ? $data['edit_holidays'] : 0;
        $access_control->print_users        = (isset($data['print_users']) && $data['print_users'] != "") ? $data['print_holidays'] : 0;
        $access_control->list_users         = (isset($data['list_users']) && $data['list_users'] != "") ? $data['list_holidays'] : 0;

        //Save
        $access_control->created_by = $request->user()->id;
        $access_control->updated_by = $request->user()->id;
        $access_control->created_at = Carbon::now('CAT');
        $access_control->updated_at = Carbon::now('CAT');
        $access_control->save();

        return $access_control;
    }
    //Rules
    public static function createRules(){

        return [
            'user_group_id'     => 'required|nullable',
            'login'             => 'required|nullable',
            'system_admin'      => 'required|nullable',
            'add_report'        => 'required|nullable',
            'edit_report'       => 'required|nullable',
            'list_report'       => 'required|nullable',
            'print_report'      => 'required|nullable',
            'add_departments'   => 'required|nullable',
            'list_departments'  => 'required|nullable',
            'edit_departments'  => 'required|nullable',
            'print_departments' => 'required|nullable',
            'add_devices'       => 'required|nullable',
            'edit_devices'      => 'required|nullable',
            'list_devices'      => 'required|nullable',
            'print_devices'     => 'required|nullable',
            'add_leaves'        => 'required|nullable',
            'edit_leaves'       => 'required|nullable',
            'print_leaves'      => 'required|nullable',
            'list_leaves'       => 'required|nullable',
            'upload_leaves'     => 'required|nullable',
            'add_leave_type'    => 'required|nullable',
            'edit_leave_type'   => 'required|nullable',
            'list_leave_type'   => 'required|nullable',
            'print_leave_type'  => 'required|nullable',
            'add_holidays'      => 'required|nullable',
            'edit_holidays'     => 'required|nullable',
            'list_holidays'     => 'required|nullable',
            'print_holidays'    => 'required|nullable',
            'add_users'         => 'required|nullable',
            'list_users'        => 'required|nullable',
            'edit_users'        => 'required|nullable',
            'print_users'       => 'required|nullable',
        ];
    }

    public static function updateRules($id){

        return [
            'user_group_id'     => 'required|unique:users,user_group_id,'.$id,
            'login'             => 'required|nullable',
            'system_admin'      => 'required|nullable',
            'add_report'        => 'required|nullable',
            'edit_report'       => 'required|nullable',
            'list_report'       => 'required|nullable',
            'print_report'      => 'required|nullable',
            'add_departments'   => 'required|nullable',
            'list_departments'  => 'required|nullable',
            'edit_departments'  => 'required|nullable',
            'print_departments' => 'required|nullable',
            'add_devices'       => 'required|nullable',
            'edit_devices'      => 'required|nullable',
            'list_devices'      => 'required|nullable',
            'print_devices'     => 'required|nullable',
            'add_leaves'        => 'required|nullable',
            'edit_leaves'       => 'required|nullable',
            'print_leaves'      => 'required|nullable',
            'list_leaves'       => 'required|nullable',
            'upload_leaves'     => 'required|nullable',
            'add_leave_type'    => 'required|nullable',
            'edit_leave_type'   => 'required|nullable',
            'list_leave_type'   => 'required|nullable',
            'print_leave_type'  => 'required|nullable',
            'add_holidays'      => 'required|nullable',
            'edit_holidays'     => 'required|nullable',
            'list_holidays'     => 'required|nullable',
            'print_holidays'    => 'required|nullable',
            'add_users'         => 'required|nullable',
            'list_users'        => 'required|nullable',
            'edit_users'        => 'required|nullable',
            'print_users'       => 'required|nullable',
        ];
    }

    //Models
    public static function info($id){
        $item = AccessControl::where('id', $id)->first();

        if(!isset($item)) return null;

        return [
            'user_group_id'     => $item->user_group_id,
            'login'             => $item->login,
            'system_admin'      => $item->system_admin,
            'add_report'        => $item->add_report,
            'edit_report'       => $item->edit_report,
            'list_report'       => $item->list_report,
            'print_report'      => $item->print_report,
            'add_departments'   => $item->add_departments,
            'list_departments'  => $item->list_departments,
            'edit_departments'  => $item->edit_departments,
            'print_departments' => $item->print_departments,
            'add_devices'       => $item->add_devices,
            'edit_devices'      => $item->edit_devices,
            'list_devices'      => $item->list_devices,
            'print_devices'     => $item->print_devices,
            'add_leaves'        => $item->add_leaves,
            'edit_leaves'       => $item->edit_leaves,
            'print_leaves'      => $item->print_leaves,
            'list_leaves'       => $item->list_leaves,
            'upload_leaves'     => $item->upload_leaves,
            'add_leave_type'    => $item->add_leave_type,
            'edit_leave_type'   => $item->edit_leave_type,
            'list_leave_type'   => $item->list_leave_type,
            'print_leave_type'  => $item->print_leave_type,
            'add_holidays'      => $item->add_holidays,
            'edit_holidays'     => $item->edit_holidays,
            'list_holidays'     => $item->list_holidays,
            'print_holidays'    => $item->print_holidays,
            'add_users'         => $item->add_users,
            'list_users'        => $item->list_users,
            'edit_users'        => $item->edit_users,
            'print_users'       => $item->print_users
        ];
    }


    public static function model($item){

        return [
            'user_group_id'     => $item->user_group_id,
            'login'             => $item->login,
            'system_admin'      => $item->system_admin,
            'add_report'        => $item->add_report,
            'edit_report'       => $item->edit_report,
            'list_report'       => $item->list_report,
            'print_report'      => $item->print_report,
            'add_departments'   => $item->add_departments,
            'list_departments'  => $item->list_departments,
            'edit_departments'  => $item->edit_departments,
            'print_departments' => $item->print_departments,
            'add_devices'       => $item->add_devices,
            'edit_devices'      => $item->edit_devices,
            'list_devices'      => $item->list_devices,
            'print_devices'     => $item->print_devices,
            'add_leaves'        => $item->add_leaves,
            'edit_leaves'       => $item->edit_leaves,
            'print_leaves'      => $item->print_leaves,
            'list_leaves'       => $item->list_leaves,
            'upload_leaves'     => $item->upload_leaves,
            'add_leave_type'    => $item->add_leave_type,
            'edit_leave_type'   => $item->edit_leave_type,
            'list_leave_type'   => $item->list_leave_type,
            'print_leave_type'  => $item->print_leave_type,
            'add_holidays'      => $item->add_holidays,
            'edit_holidays'     => $item->edit_holidays,
            'list_holidays'     => $item->list_holidays,
            'print_holidays'    => $item->print_holidays,
            'add_users'         => $item->add_users,
            'list_users'        => $item->list_users,
            'edit_users'        => $item->edit_users,
            'print_users'       => $item->print_users,
            'created_at'        => Helpers::formatDate($item->created_at),
            'updated_at'        => Helpers::formatDate($item->updated_at),
            'created_by'        => User::info($item->created_by),
            'updated_by'        => User::info($item->updated_by),
        ];
    }
}
