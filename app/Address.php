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
        'street',
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
            'street'       => 'required',
            'city'         => 'required',
            'province'     => 'required',
            'country'      => 'required',
            'postal_code'  => 'required',
        ];
    }

    public static function updateRules($id){

        return [
            'street'       => 'required',
            'city'         => 'required',
            'province'     => 'required',
            'country'      => 'required',
            'postal_code'  => 'required',
        ];
    }

    //Models
    public static function addressModel($id){
        $item = Address::where('id', $id)->first();

        if(!isset($item)){
            return null;
        }
        else {
            return [
                'id'            => $item->id,
                'street'        => $item->street,
                'city'          => $item->city,
                'province'      => $item->province,
                'country'       => $item->country,
                'postal_code'   => $item->postal_code
            ];
        }
    }
}
