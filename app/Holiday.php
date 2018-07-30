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
        'department_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_by', 'deleted_at'];

    //Functions
    public static function isHoliday($date, $department){
        $count = Holiday::where('date', $date)
            ->where('department_id', $department)
            ->count();

        if($count > 0) $val = true;
        else $val = false;

        return $val;
    }

    public static function getMonthlyHolidays($date, $department = ""){
        $data = [];

        $result = Holiday::whereMonth('date','=', date('m', strtotime($date)))
            ->whereYear('date','=', date('Y', strtotime($date)))
            ->where('department_id', $department)
            ->get();

        foreach ($result as $item){
            $data [] = Holiday::model($item);
        }

        return collect($data);
    }

    //Rules
    public static function createRules(){

        return [
            'name'        => 'required',
            'date'        => 'required|date|date_format:"Y-m-d"',
            'department'  => 'required|exists:departments,id',
        ];
    }

    public static function updateRules(){

        return [
            'name'        => 'required',
            'date'        => 'required|date|date_format:"Y-m-d"',
            'department'  => 'required|exists:departments,id',
        ];
    }

    //Models
    public static function model($item){

        return  [
            'id'         => $item->id,
            'name'       => $item->name,
            'date'       => Helpers::formatDate($item->date, "Y-m-d"),
            'description'=> $item->description,
            'department' => Department::info($item->department_id),
            'created_at' => Helpers::formatDate($item->created_at),
            'updated_at' => Helpers::formatDate($item->updated_at),
            'created_by' => User::info($item->created_by),
            'updated_by' => User::info($item->updated_by),
        ];
    }
}