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
        'imei_number',
        'device_name',
        'supervisor',
        'site_id',
        'status',
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
    public static function deviceLastSync($id){

        $items = Record::where('device_id', $id)->latest()->first();

        return  isset($items->created_at)? $items->created_at : null;
    }

    //Rules
    public static function createRules()
    {
        return [
            'device_name'  => 'required|unique:devices,device_name',
            'status'       => 'sometimes|nullable|in:' .Device::ACTIVE . ',' .Device::DEACTIVATED,
            'company_id'   => 'required|exists:companies,id',
        ];
    }

    public static function updateRules($id)
    {
        return [
            'device_name'  => 'required|unique:devices,device_name',
            'status'       => 'sometimes|nullable|in:' .Device::ACTIVE . ',' .Device::DEACTIVATED,
            'company_id'   => 'required|exists:companies,id',
        ];
    }


    //Models
    public static function deviceInformation($id){

        $item = Device::where('id', $id)->first();

        if(count($item) <= 0) return null;

        return  [
            'id'                => $item->id,
            'device_name'       => $item->device_name,
            'phone_number'      => $item->phone_number,
            'status'            => $item->status,
            'last_sync'         => Helpers::formatDate(Device::deviceLastSync($item->id)),
            'company'           => Company::companyInformation($item->company_id),
        ];

    }
    public static function deviceModel($item){

        return  [
            'id'                => $item->id,
            'imei_number'       => $item->imei_number,
            'device_name'       => $item->device_name,
            'status'            => $item->status,
            'supervisor'        => $item->supervisor,
            'serial_number'     => $item->serial_number,
            'phone_number'      => $item->phone_number,
            'allocation_date'   => Helpers::formatDate($item->allocation_date, "Y-m-d"),
            'last_sync'         => Helpers::formatDate(Device::deviceLastSync($item->imei_number)),
            'site'              => Site::siteInformation($item->site_id),
            'created_at'        => Helpers::formatDate($item->created_at),
            'updated_at'        => Helpers::formatDate($item->updated_at),
            'created_by'        => User::userInformation($item->created_by),
            'last_updated_by'  => ($item->last_updated_by != null && $item->last_updated_by != "") ? User::userInformation($item->last_updated_by) : null,
        ];
    }
}