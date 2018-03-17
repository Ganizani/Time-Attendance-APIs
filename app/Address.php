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


class Address extends Model
{
    use SoftDeletes;

    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_no',
        'street_no',
        'street_name',
        'suburb',
        'city',
        'province',
        'country',
        'postal_code'
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
            'house_no'     => 'sometimes|nullable',
            'street_no'    => 'sometimes|nullable',
            'street_name'  => 'sometimes|nullable',
            'suburb'       => 'sometimes|nullable',
            'city'         => 'sometimes|nullable',
            'province'     => 'sometimes|nullable',
            'country'      => 'sometimes|nullable',
            'postal_code'  => 'sometimes|nullable',
        ];
    }

    public static function updateRules($id){

        return [
            'house_no'     => 'sometimes|nullable',
            'street_no'    => 'sometimes|nullable',
            'street_name'  => 'sometimes|nullable',
            'suburb'       => 'sometimes|nullable',
            'city'         => 'sometimes|nullable',
            'province'     => 'sometimes|nullable',
            'country'      => 'sometimes|nullable',
            'postal_code'  => 'sometimes|nullable',
        ];
    }

    //Models
    public static function info($id){
        $item = Address::where('id', $id)->first();

        if(!isset($item)){
            return null;
        }
        else {
            return [
                'id'            => $item->id,
                'house_no'      => $item->house_no,
                'street_no'     => $item->street_no,
                'street_name'   => $item->street_name,
                'suburb'        => $item->suburb,
                'city'          => $item->city,
                'province'      => $item->province,
                'country'       => $item->country,
                'postal_code'   => $item->postal_code
            ];
        }
    }


    public static function model($item){

        return [
            'id'            => $item->id,
            'house_no'      => $item->house_no,
            'street_no'     => $item->street_no,
            'street_name'   => $item->street_name,
            'suburb'        => $item->suburb,
            'city'          => $item->city,
            'province'      => $item->province,
            'country'       => $item->country,
            'postal_code'   => $item->postal_code,
            'created_at'    => Helpers::formatDate($item->created_at),
            'updated_at'    => Helpers::formatDate($item->updated_at),
            'created_by'    => User::info($item->created_by),
            'updated_by'    => User::info($item->updated_by),
        ];
    }
}
