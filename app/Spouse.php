<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:42 PM
 */

namespace App;
use App\Http\Helpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Spouse extends Model
{
    use SoftDeletes;

    protected $table = 'spouses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'employer',
        'work_location',
        'cell_phone',
        'work_phone',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];

    //Functions

    //Rules
    public static function createRules(){

        return [
            'name'          => 'sometimes|nullable',
            'employer'      => 'sometimes|nullable',
            'work_location' => 'sometimes|nullable',
            'cell_phone'    => 'sometimes|nullable',
            'work_phone'    => 'sometimes|nullable',
        ];
    }

    public static function updateRules($id){

        return [
            'name'          => 'required',
            'employer'      => 'sometimes|nullable',
            'work_location' => 'sometimes|nullable',
            'cell_phone'    => 'sometimes',
            'work_phone'    => 'sometimes|nullable',
        ];
    }

    //Models
    public static function info($id){
        $item = Spouse::where('id', $id)->first();

        if(!isset($item)) return null;

        return [
            'id'            => $item->id,
            'name'          => $item->name,
            'employer'      => $item->employer,
            'work_location' => $item->work_location,
            'cell_phone'    => $item->cell_phone,
            'work_phone'    => $item->work_phone
        ];
    }

    public static function model($item){

        return [
            'id'            => $item->id,
            'name'          => $item->name,
            'employer'      => $item->employer,
            'work_location' => $item->work_location,
            'cell_phone'    => $item->cell_phone,
            'work_phone'    => $item->work_phone,
            'created_at'    => Helpers::formatDate($item->created_at),
            'updated_at'    => Helpers::formatDate($item->updated_at),
            'created_by'    => User::info($item->created_by),
            'updated_by'    => User::info($item->updated_by),
        ];
    }
}
