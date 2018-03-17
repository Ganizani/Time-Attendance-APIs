<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:06 AM
 */
namespace App;
use App\Http\Helpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class NextOfKin extends Model
{
    use SoftDeletes;

    protected $table = 'next_of_kins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'cell_phone',
        'home_phone',
        'relationship',
        'address_id',
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
            'title'         => 'sometimes|nullable',
            'first_name'    => 'sometimes|nullable',
            'last_name'     => 'sometimes|nullable',
            'middle_name'   => 'sometimes|nullable',
            'email'         => 'sometimes|nullable|email',
            'cell_phone'    => 'sometimes|nullable',
            'home_phone'    => 'sometimes|nullable',
            'relationship'  => 'sometimes|nullable'
        ];
    }

    public static function updateRules($id){

        return [
            'title'         => 'sometimes|nullable',
            'first_name'    => 'sometimes|nullable',
            'last_name'     => 'sometimes|nullable',
            'middle_name'   => 'sometimes|nullable',
            'email'         => 'sometimes|nullable|email',
            'cell_phone'    => 'sometimes|nullable',
            'home_phone'    => 'sometimes|nullable',
            'relationship'  => 'sometimes|nullable'
        ];
    }

    //Models
    public static function info($id){
        $item = NextOfKin::where('id', $id)->first();

        if(!isset($item)){
            return null;
        }
        else {
            return [
                'id'            => $item->id,
                'title'         => $item->title,
                'first_name'    => $item->first_name,
                'last_name'     => $item->last_name,
                'middle_name'   => $item->middle_name,
                'email'         => $item->email,
                'cell_phone'    => $item->cell_phone,
                'home_phone'    => $item->home_phone,
                'relationship'  => $item->relationship,
            ];
        }
    }

    public static function model($item){

        return [
            'id'            => $item->id,
            'title'         => $item->title,
            'first_name'    => $item->first_name,
            'last_name'     => $item->last_name,
            'middle_name'   => $item->middle_name,
            'email'         => $item->email,
            'cell_phone'    => $item->cell_phone,
            'home_phone'    => $item->home_phone,
            'relationship'  => $item->relationship,
            'created_at'    => Helpers::formatDate($item->created_at),
            'updated_at'    => Helpers::formatDate($item->updated_at),
            'created_by'    => User::info($item->created_by),
            'updated_by'    => User::info($item->updated_by),
        ];
    }
}
