<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:50 PM
 */
namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers;


class Department extends Model
{
    use SoftDeletes;

    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'location',
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
    public static function employeeCount($id){

        return User::where('department_id', $id)->count();
    }

    public static function deviceCount($id){

        return Device::where('department_id', $id)->count();
    }



    public static function departmentHolidays($id)
    {
        $holidays = [];

        $result = Holiday::where('department_id', $id)->get();

        foreach($result as $item){
            $holidays [] = Holiday::model($item);
        }

        return $holidays;
    }

    public static function departmentEmployees($id)
    {
        $data = [];
        $result = User::where('department_id', $id)->get();
        foreach($result as $item){
            $data [] = User::info($item);
        }

        return $data;
    }

    public static function departmentIdFromName($name)
    {
        $result = Department::where('name', $name)->first();

        return $result['id'];
    }

    //Rules
    public static function createRules(){

        return [
            'name'          => 'required|unique:departments,name',
            'description'   => 'sometimes|nullable',
            'location'      => 'sometimes|nullable',
        ];
    }

    public static function updateRules($id){

        return [
            'name'          => 'required|unique:departments,name,'.$id,
            'description'   => 'sometimes|nullable',
            'location'      => 'sometimes|nullable',
        ];
    }

    //Models
    public static function info($id){

        $item = Department::where('id', $id)->first();

        if(!isset($item)) return null;

        return [
            'id'          => $item->id,
            'name'        => $item->name,
            'description' => $item->description,
            'location'    => $item->location,
        ];
    }

    public static function model($item){

        return  [
            'id'           => $item->id,
            'name'         => $item->name,
            'description'  => $item->description,
            'location'     => $item->location,
            'employees'    => Department::employeeCount($item->id),
            'devices'      => Department::deviceCount($item->id),
            'created_at'   => Helpers::formatDate($item->created_at),
            'updated_at'   => Helpers::formatDate($item->updated_at),
            'created_by'   => User::info($item->created_by),
            'updated_by'   => User::info($item->updated_by)
        ];
    }
}
