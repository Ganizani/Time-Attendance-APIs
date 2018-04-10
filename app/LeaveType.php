<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:47 PM
 */

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Helpers;

class LeaveType extends Model
{
    use SoftDeletes;

    protected $table = 'leave_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'created_at',
        'updated_at',
        'created_by',
        'last_updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    //Functions
    public static function getLeaveTypes($leave_type){

        $result = LeaveType::where('name',$leave_type)->first();

        return $result;
    }

    //Rules
    public static function createRules(){

        return [
            'name'          => 'required|unique:leave_types,name',
            'description'   => 'sometimes|nullable'
        ];
    }

    public static function updateRules($id){

        return [
            'name'          => 'required|unique:leave_types,name,'.$id,
            'description'   => 'sometimes|nullable'
        ];
    }

    //Models
    public static function info($id){

        $item = LeaveType::find($id);

        return  [
            'id'           => $item->id,
            'name'         => $item->name,
            'description'  => $item->description,
        ];
    }

    public static function model($item){

        return  [
            'id'          => $item->id,
            'name'        => $item->name,
            'description' => $item->description,
            'created_at'  => Helpers::formatDate($item->created_at),
            'updated_at'  => Helpers::formatDate($item->updated_at),
            'created_by'  => User::info($item->created_by),
            'updated_by'  => User::info($item->updated_by),
        ];
    }
}