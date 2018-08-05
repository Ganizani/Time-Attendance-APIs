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
        'update_user_type',
        'manual_clocking',
        'apply_for_leave',
        'view_reports',
        'print_reports',
        'add_departments',
        'edit_departments',
        'list_departments',
        'print_departments',
        'delete_departments',
        'add_devices',
        'edit_devices',
        'list_devices',
        'print_devices',
        'delete_devices',
        'add_leaves',
        'edit_leaves',
        'print_leaves',
        'list_leaves',
        'upload_leaves',
        'delete_leaves',
        'add_leave_types',
        'edit_leave_types',
        'list_leave_types',
        'print_leave_types',
        'delete_leave_type',
        'add_holidays',
        'edit_holidays',
        'list_holidays',
        'edit_holidays',
        'print_holidays',
        'delete_holidays',
        'add_users',
        'list_users',
        'edit_users',
        'print_users',
        'delete_users',
        'add_user_groups',
        'edit_user_groups',
        'list_user_groups' ,
        'print_user_groups',
        'delete_user_groups',
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
        $access_control->update_user_type    = (isset($data['update_user_type']) && $data['update_user_type'] != "") ? $data['update_user_type'] : 0;
        $access_control->manual_clocking     = (isset($data['manual_clocking']) && $data['manual_clocking'] != "") ? $data['manual_clocking'] : 0;
        $access_control->apply_for_leave     = (isset($data['apply_for_leave']) && $data['apply_for_leave'] != "") ? $data['apply_for_leave'] : 0;
        //Report
        $access_control->view_reports        = (isset($data['view_reports'])   && $data['view_reports'] != "")   ? $data['view_reports']   : 0;
        $access_control->print_reports       = (isset($data['print_reports'])  && $data['print_reports'] != "")  ? $data['print_reports']  : 0;
        //Department
        $access_control->add_departments     = (isset($data['add_departments']) && $data['add_departments'] != "") ? $data['add_departments'] : 0;
        $access_control->edit_departments    = (isset($data['edit_departments']) && $data['edit_departments'] != "") ? $data['edit_departments'] : 0;
        $access_control->print_departments   = (isset($data['print_departments']) && $data['print_departments'] != "") ? $data['print_departments'] : 0;
        $access_control->list_departments    = (isset($data['list_departments']) && $data['list_departments'] != "") ? $data['list_departments'] : 0;
        $access_control->delete_departments  = (isset($data['delete_departments']) && $data['delete_departments'] != "") ? $data['delete_departments'] : 0;
        //Devices
        $access_control->add_devices         = (isset($data['add_devices']) && $data['add_devices'] != "") ? $data['add_devices'] : 0;
        $access_control->edit_devices        = (isset($data['edit_devices']) && $data['edit_devices'] != "") ? $data['edit_devices'] : 0;
        $access_control->print_devices       = (isset($data['print_devices']) && $data['print_devices'] != "") ? $data['print_devices'] : 0;
        $access_control->list_devices        = (isset($data['list_devices']) && $data['list_devices'] != "") ? $data['list_devices'] : 0;
        $access_control->delete_devices        = (isset($data['delete_devices']) && $data['delete_devices'] != "") ? $data['delete_devices'] : 0;
        //Leaves
        $access_control->add_leaves          = (isset($data['add_leaves']) && $data['add_leaves'] != "") ? $data['add_leaves'] : 0;
        $access_control->edit_leaves         = (isset($data['edit_leaves']) && $data['edit_leaves'] != "") ? $data['edit_leaves'] : 0;
        $access_control->print_leaves        = (isset($data['print_leaves']) && $data['print_leaves'] != "") ? $data['print_leaves'] : 0;
        $access_control->list_leaves         = (isset($data['list_leaves']) && $data['list_leaves'] != "") ? $data['list_leaves'] : 0;
        $access_control->delete_leaves       = (isset($data['delete_leaves']) && $data['delete_leaves'] != "") ? $data['delete_leaves'] : 0;
        //Leaves Types
        $access_control->add_leave_types     = (isset($data['add_leave_types']) && $data['add_leave_types'] != "") ? $data['add_leave_types'] : 0;
        $access_control->edit_leave_types    = (isset($data['edit_leave_types']) && $data['edit_leave_types'] != "") ? $data['edit_leave_types'] : 0;
        $access_control->print_leave_types   = (isset($data['print_leave_types']) && $data['print_leave_types'] != "") ? $data['print_leave_types'] : 0;
        $access_control->list_leave_types    = (isset($data['list_leave_types']) && $data['list_leave_types'] != "") ? $data['list_leave_types'] : 0;
        $access_control->delete_leave_types  = (isset($data['delete_leave_types']) && $data['delete_leave_types'] != "") ? $data['delete_leave_types'] : 0;
        //Holiday
        $access_control->add_holidays        = (isset($data['add_holidays']) && $data['add_holidays'] != "") ? $data['add_holidays'] : 0;
        $access_control->edit_holidays       = (isset($data['edit_holidays']) && $data['edit_holidays'] != "") ? $data['edit_holidays'] : 0;
        $access_control->print_holidays      = (isset($data['print_holidays']) && $data['print_holidays'] != "") ? $data['print_holidays'] : 0;
        $access_control->list_holidays       = (isset($data['list_holidays']) && $data['list_holidays'] != "") ? $data['list_holidays'] : 0;
        $access_control->delete_holidays     = (isset($data['delete_holidays']) && $data['delete_holidays'] != "") ? $data['delete_holidays'] : 0;
        //User
        $access_control->add_users          = (isset($data['add_users']) && $data['add_users'] != "") ? $data['add_users'] : 0;
        $access_control->edit_users         = (isset($data['edit_users']) && $data['edit_users'] != "") ? $data['edit_users'] : 0;
        $access_control->print_users        = (isset($data['print_users']) && $data['print_users'] != "") ? $data['print_users'] : 0;
        $access_control->list_users         = (isset($data['list_users']) && $data['list_users'] != "") ? $data['list_users'] : 0;
        $access_control->delete_users       = (isset($data['delete_users']) && $data['delete_users'] != "") ? $data['delete_users'] : 0;
        //User Groups
        $access_control->add_user_groups    = (isset($data['add_user_groups']) && $data['add_user_groups'] != "") ? $data['add_user_groups'] : 0;
        $access_control->edit_user_groups   = (isset($data['edit_user_groups']) && $data['edit_user_groups'] != "") ? $data['edit_user_groups'] : 0;
        $access_control->print_user_groups  = (isset($data['print_user_groups']) && $data['print_user_groups'] != "") ? $data['print_user_groups'] : 0;
        $access_control->list_user_groups   = (isset($data['list_user_groups']) && $data['list_user_groups'] != "") ? $data['list_user_groups'] : 0;
        $access_control->delete_user_groups = (isset($data['delete_user_groups']) && $data['delete_user_groups'] != "") ? $data['delete_user_groups'] : 0;

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
            'login'             => 'required|nullable',
            'system_admin'      => 'required|nullable',
            'update_user_type'   => 'required|nullable',
            'manual_clocking'   => 'required|nullable',
            'apply_for_leave'   => 'required|nullable',
            'view_reports'      => 'required|nullable',
            'print_reports'     => 'required|nullable',
            'add_departments'   => 'required|nullable',
            'list_departments'  => 'required|nullable',
            'edit_departments'  => 'required|nullable',
            'print_departments' => 'required|nullable',
            'delete_departments'=> 'required|nullable',
            'add_devices'       => 'required|nullable',
            'edit_devices'      => 'required|nullable',
            'list_devices'      => 'required|nullable',
            'print_devices'     => 'required|nullable',
            'delete_devices'    => 'required|nullable',
            'add_leaves'        => 'required|nullable',
            'edit_leaves'       => 'required|nullable',
            'print_leaves'      => 'required|nullable',
            'list_leaves'       => 'required|nullable',
            'delete_leaves'     => 'required|nullable',
            'upload_leaves'     => 'required|nullable',
            'add_leave_types'    => 'required|nullable',
            'edit_leave_types'   => 'required|nullable',
            'list_leave_types'   => 'required|nullable',
            'print_leave_types'  => 'required|nullable',
            'delete_leave_types' => 'required|nullable',
            'add_holidays'      => 'required|nullable',
            'edit_holidays'     => 'required|nullable',
            'list_holidays'     => 'required|nullable',
            'print_holidays'    => 'required|nullable',
            'delete_holidays'   => 'required|nullable',
            'add_users'         => 'required|nullable',
            'list_users'        => 'required|nullable',
            'edit_users'        => 'required|nullable',
            'print_users'       => 'required|nullable',
            'delete_users'      => 'required|nullable',
            'add_user_groups'     => 'required|nullable',
            'edit_user_groups'    => 'required|nullable',
            'list_user_groups'    => 'required|nullable',
            'print_user_groups'   => 'required|nullable',
            'delete_user_groups'  => 'required|nullable',
        ];
    }

    public static function updateRules($id){

        return [
            'login'             => 'required|nullable',
            'system_admin'      => 'required|nullable',
            'view_reports'      => 'required|nullable',
            'update_user_type'  => 'required|nullable',
            'manual_clocking'   => 'required|nullable',
            'apply_for_leave'   => 'required|nullable',
            'print_reports'     => 'required|nullable',
            'add_departments'   => 'required|nullable',
            'list_departments'  => 'required|nullable',
            'edit_departments'  => 'required|nullable',
            'print_departments' => 'required|nullable',
            'delete_departments'=> 'required|nullable',
            'add_devices'       => 'required|nullable',
            'edit_devices'      => 'required|nullable',
            'list_devices'      => 'required|nullable',
            'print_devices'     => 'required|nullable',
            'delete_devices'    => 'required|nullable',
            'add_leaves'        => 'required|nullable',
            'edit_leaves'       => 'required|nullable',
            'print_leaves'      => 'required|nullable',
            'list_leaves'       => 'required|nullable',
            'delete_leaves'     => 'required|nullable',
            'upload_leaves'     => 'required|nullable',
            'add_leave_types'    => 'required|nullable',
            'edit_leave_types'   => 'required|nullable',
            'list_leave_types'   => 'required|nullable',
            'print_leave_types'  => 'required|nullable',
            'delete_leave_types' => 'required|nullable',
            'add_holidays'      => 'required|nullable',
            'edit_holidays'     => 'required|nullable',
            'list_holidays'     => 'required|nullable',
            'print_holidays'    => 'required|nullable',
            'delete_holidays'   => 'required|nullable',
            'add_users'         => 'required|nullable',
            'list_users'        => 'required|nullable',
            'edit_users'        => 'required|nullable',
            'print_users'       => 'required|nullable',
            'delete_users'      => 'required|nullable',
            'add_user_groups'     => 'required|nullable',
            'edit_user_groups'    => 'required|nullable',
            'list_user_groups'    => 'required|nullable',
            'print_user_groups'   => 'required|nullable',
            'delete_user_groups'  => 'required|nullable',
        ];
    }

    //Models
    public static function info($id){
        $item = AccessControl::where('user_group_id', $id)->first();

        if(!isset($item)) return null;

        return [
            'id'                => $item->id,
            'user_group_id'     => $item->user_group_id,
            'login'             => $item->login,
            'system_admin'      => $item->system_admin,
            'update_user_type'  => $item->update_user_type,
            'manual_clocking'   => $item->manual_clocking,
            'apply_for_leave'   => $item->apply_for_leave,
            'view_reports'      => $item->view_reports,
            'print_reports'     => $item->print_reports,
            'add_departments'   => $item->add_departments,
            'list_departments'  => $item->list_departments,
            'edit_departments'  => $item->edit_departments,
            'print_departments' => $item->print_departments,
            'delete_departments'=> $item->delete_departments,
            'add_devices'       => $item->add_devices,
            'edit_devices'      => $item->edit_devices,
            'list_devices'      => $item->list_devices,
            'print_devices'     => $item->print_devices,
            'delete_devices'    => $item->delete_devices,
            'add_leaves'        => $item->add_leaves,
            'edit_leaves'       => $item->edit_leaves,
            'print_leaves'      => $item->print_leaves,
            'list_leaves'       => $item->list_leaves,
            'delete_leaves'     => $item->delete_leaves,
            'upload_leaves'     => $item->upload_leaves,
            'add_leave_types'   => $item->add_leave_types,
            'edit_leave_types'  => $item->edit_leave_types,
            'list_leave_types'  => $item->list_leave_types,
            'print_leave_types' => $item->print_leave_types,
            'delete_leave_types'=> $item->delete_leave_types,
            'add_holidays'      => $item->add_holidays,
            'edit_holidays'     => $item->edit_holidays,
            'list_holidays'     => $item->list_holidays,
            'print_holidays'    => $item->print_holidays,
            'delete_holidays'   => $item->delete_holidays,
            'add_users'         => $item->add_users,
            'list_users'        => $item->list_users,
            'edit_users'        => $item->edit_users,
            'print_users'       => $item->print_users,
            'delete_users'      => $item->delete_users,
            'add_user_groups'     => $item->add_user_groups,
            'edit_user_groups'    => $item->edit_user_groups,
            'list_user_groups'    => $item->list_user_groups,
            'print_user_groups'   => $item->print_user_groups,
            'delete_user_groups'  => $item->delete_user_groups,
        ];
    }


    public static function model($item){

        return [
            'id'                => $item->id,
            'user_group_id'     => $item->user_group_id,
            'system_admin'      => $item->system_admin,
            'login'             => $item->login,
            'update_user_type'  => $item->update_user_type,
            'manual_clocking'   => $item->manual_clocking,
            'apply_for_leave'   => $item->apply_for_leave,
            'view_reports'      => $item->view_reports,
            'print_reports'     => $item->print_reports,
            'add_departments'   => $item->add_departments,
            'list_departments'  => $item->list_departments,
            'edit_departments'  => $item->edit_departments,
            'print_departments' => $item->print_departments,
            'delete_departments'=> $item->delete_departments,
            'add_devices'       => $item->add_devices,
            'edit_devices'      => $item->edit_devices,
            'list_devices'      => $item->list_devices,
            'print_devices'     => $item->print_devices,
            'delete_devices'    => $item->delete_devices,
            'add_leaves'        => $item->add_leaves,
            'edit_leaves'       => $item->edit_leaves,
            'print_leaves'      => $item->print_leaves,
            'list_leaves'       => $item->list_leaves,
            'delete_leaves'     => $item->delete_leaves,
            'upload_leaves'     => $item->upload_leaves,
            'add_leave_types'   => $item->add_leave_types,
            'edit_leave_types'  => $item->edit_leave_types,
            'list_leave_types'  => $item->list_leave_types,
            'print_leave_types' => $item->print_leave_types,
            'delete_leave_types'=> $item->delete_leave_types,
            'add_holidays'      => $item->add_holidays,
            'edit_holidays'     => $item->edit_holidays,
            'list_holidays'     => $item->list_holidays,
            'print_holidays'    => $item->print_holidays,
            'delete_holidays'   => $item->delete_holidays,
            'add_users'         => $item->add_users,
            'list_users'        => $item->list_users,
            'edit_users'        => $item->edit_users,
            'print_users'       => $item->print_users,
            'delete_users'      => $item->delete_users,
            'add_user_groups'     => $item->add_user_groups,
            'edit_user_groups'    => $item->edit_user_groups,
            'list_user_groups'    => $item->list_user_groups,
            'print_user_groups'   => $item->print_user_groups,
            'delete_user_groups'  => $item->delete_user_groups,
            'created_at'        => Helpers::formatDate($item->created_at),
            'updated_at'        => Helpers::formatDate($item->updated_at),
            'created_by'        => User::info($item->created_by),
            'updated_by'        => User::info($item->updated_by),
        ];
    }
}
