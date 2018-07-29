<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 2018/07/29
 * Time: 07:48
 */

namespace App;
use App\Http\Helpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserGroup extends Model
{
    use SoftDeletes;

    protected $table = 'user_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    //Functions

    //Rules
    public static function createRules(){

        return [
            'name'        => 'required|unique:user_groups',
            'description' => 'sometimes|nullable',
        ];
    }

    public static function updateRules($id){

        return [
            'name'        => 'required|unique:user_groups,name,'.$id,
            'description' => 'sometimes|nullable',
        ];
    }

    //Models
    public static function info($id){
        $item = UserGroup::where('id', $id)->first();

        if(!isset($item)) return null;

        return [
            'id'             => $item->id,
            'name'           => $item->name,
            'description'    => $item->description,
            'access_control' => AccessControl::info($item->id)
        ];
    }


    public static function model($item){

        return [
            'id'             => $item->id,
            'name'           => $item->name,
            'description'    => $item->description,
            'access_control' => AccessControl::info($item->id)
        ];
    }
}
