<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:48 PM
 */


namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class Holiday extends Model
{
    use SoftDeletes;

    protected $table = 'holidays';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'date',
        'company_id',
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
    public static function isHoliday($date, $site){
        $count = Holiday::where('date', $date)
            ->where('site_id', $site)
            ->count();
        if($count > 0) $val = true;
        else $val = false;

        return $val;
    }

    public static function getMonthlyHolidays($date, $site = ""){
        $data = [];

        $result = Holiday::whereMonth('date','=', date('m', strtotime($date)))
            ->whereYear('date','=', date('Y', strtotime($date)))
            ->where('site_id', $site)
            ->get();

        foreach ($result as $item){
            $data [] = Holiday::holidayModel($item);
        }

        return collect($data);
    }

    //Rules
    public static function createRules(){

        return [
            'date'     => 'required|date',
            'company'  => 'required|exists:companies,id',
            'site'     => 'required|exists:sites,id',
        ];
    }

    public static function uploadRules(){

        return [
            'date'       => 'required|date|date_format:"Y-m-d"',
            'company'    => 'required|exists:companies,name',
            'site'       => 'required|exists:sites,name',
        ];
    }

    public static function updateRules(){

        return [
            'date'       => 'required|date|date_format:"Y-m-d"',
            'company'    => 'required|exists:companies,id',
            'site'       => 'required|exists:sites,id',
        ];
    }

    //Models
    public static function holidayModel($item){

        return  [
            'id'                => $item->id,
            'name'              => $item->name,
            'date'              => Helpers::formatDate($item->date, "Y-m-d"),
            'description'       => $item->description,
            'company'           => Company::companyInformation($item->company_id),
            'site'              => Site::siteInformation($item->site_id),
            'created_at'        => Helpers::formatDate($item->created_at),
            'updated_at'        => ($item->updated_at != null)? Helpers::formatDate($item->updated_at) : null,
            'created_by'        => User::userInformation($item->created_by),
            'last_updated_by'  => ($item->last_updated_by != null && $item->last_updated_by != "") ? User::userInformation($item->last_updated_by) : null,
        ];
    }
}