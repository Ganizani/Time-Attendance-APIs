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


class Company extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address_id',
        'contact',
        'login_id',
        'phone_number',
        'email',
        'website',
        'contact_person',
        'password',
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
    public static function userCount($id){

        return User::where('company_id', $id)->count();
    }

    public static function companyHolidays($id)
    {
        $holidays = [];

        $result = Holiday::where('company_id', $id)->get();

        foreach($result as $item){
            $holidays [] = Holiday::holidayModel($item);
        }

        return $holidays;
    }

    public static function companyUsers($id)
    {
        $data = [];

        $result = User::where('company_id', $id)->get();

        foreach($result as $item){
            $data [] = User::userInformation($item);
        }

        return $data;
    }

    public static function companyIdFromName($name)
    {
        $result = Company::where('name', $name)->first();

        return $result['id'];
    }

    //Rules
    public static function createRules(){

        return [
            'name'              => 'required',
            'contact_person'    => 'required',
            'phone_number'      => 'required',
            'email'             => 'required|email',
            'login_id'          => 'required|unique:companies,login_id',
            'password'          => 'required',
        ];
    }

    public static function updateRules($id){

        return [
            'name'              => 'required',
            'address'           => 'required',
            'contact_person'    => 'required',
            'phone_number'      => 'required',
            'email'             => 'required|email',
            'login_id'          => 'required|unique:companies,login_id,'.$id,
            'password'          => 'required',
        ];
    }

    //Models
    public static function companyInformation($id){

        $item = Company::where('id', $id)->first();

        if(count($item) <= 0) return null;

        return [
            'id'                => $item->id,
            'name'              => $item->name,
            'phone_number'      => $item->phone_number,
            'email'             => $item->email,
            'website'           => $item->website,
            'contact_person'    => $item->contact_person,
            'address'           => Address::addressModel($item->address_id),
        ];
    }

    public static function companyModel($item){

        return  [
            'id'                => $item->id,
            'name'              => $item->name,
            'login_id'          => $item->login_id,
            'password'          => $item->password,
            'phone_number'      => $item->phone_number,
            'email'             => $item->email,
            'website'           => $item->website,
            'contact_person'    => $item->contact_person,
            'users_count'       => Company::userCount($item->id),
            'address'           => Address::addressModel($item->address_id),
            'created_at'        => Helpers::formatDate($item->created_at),
            'updated_at'        => ($item->updated_at != null)? Helpers::formatDate($item->updated_at) : null,
            'created_by'        => User::userInformation($item->created_by),
            'last_updated_by'   => ($item->last_updated_by != null && $item->last_updated_by != "") ? User::userInformation($item->last_updated_by) : null,
        ];
    }
}
