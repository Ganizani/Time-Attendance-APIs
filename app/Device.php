<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:57 PM
 */


namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Helpers;

class Device extends Model
{
    use SoftDeletes;

    const ACTIVE = 'ACTIVE';
    const DEACTIVATED = 'DEACTIVATED';

    protected $table = 'devices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'imei_number',
        'phone_number',
        'supervisor',
        'status',
        'department_id',
        'serial_number',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    //Functions
    public static function deviceLastSync($id){

        $items = Record::where('imei_number', $id)->orderBy('id', 'desc')->first();

        return isset($items->created_at) ? $items->created_at : null;
    }

    //Rules
    public static function createRules()
    {
        return [
            'name'          => 'required|unique:devices,name',
            'imei_number'   => 'required|unique:devices,imei_number',
            'serial_number' => 'sometimes|nullable',
            'status'        => 'sometimes|nullable|in:' .Device::ACTIVE . ',' .Device::DEACTIVATED,
            'department' => 'required|exists:departments,id',
            'supervisor'    => 'required|exists:users,id',
        ];
    }

    public static function updateRules($id)
    {
        return [
            'name'          => 'required|unique:devices,name,'.$id,
            'imei_number'   => 'required|unique:devices,imei_number,'.$id,
            'serial_number' => 'sometimes|nullable',
            'status'        => 'sometimes|nullable|in:' .Device::ACTIVE . ',' .Device::DEACTIVATED,
            'department' => 'required|exists:departments,id',
            'supervisor'    => 'required|exists:users,id',
        ];
    }


    //Models
    public static function info($id){

        $item = Device::where('id', $id)->orWhere('imei_number', $id)->first();

        if(count($item) <= 0) return null;

        return  [
            'id'            => $item->id,
            'name'          => $item->name,
            'imei_number'   => $item->imei_number,
            'serial_number' => $item->serial_number,
            'status'        => $item->status,
            'phone_number'  => $item->phone_number,
            'supervisor'    => User::info($item->supervisor),
            'last_sync'     => Helpers::formatDate(Device::deviceLastSync($item->id)),
            'department'    => Department::info($item->department_id)
        ];
    }

    public static function model($item){

        return  [
            'id'            => $item->id,
            'imei_number'   => $item->imei_number,
            'name'          => $item->name,
            'status'        => $item->status,
            'serial_number' => $item->serial_number,
            'phone_number'  => $item->phone_number,
            'department'    => Department::info($item->department_id),
            'supervisor'    => User::info($item->supervisor),
            'last_sync'     => Helpers::formatDate(Device::deviceLastSync($item->imei_number)),
            'created_at'    => Helpers::formatDate($item->created_at),
            'updated_at'    => Helpers::formatDate($item->updated_at),
            'created_by'    => User::info($item->created_by),
            'updated_by'    => User::info($item->updated_by)
        ];
    }
}