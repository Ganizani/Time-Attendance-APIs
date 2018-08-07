<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:09 PM
 */

namespace App\Http\Controllers\User;
;

use App\AccessControl;
use App\Address;
use App\Http\Controllers\ApiController;
use App\Leave;
use App\Login;
use App\NextOfKin;
use App\Notifications\ForgotPasswordNotification;
use App\PasswordReset;
use App\Spouse;
use App\User;
use App\UserGroup;
use Carbon\Carbon;
use App\Http\Helpers;
use Illuminate\Support\Facades\Auth;
use PhpParser\ErrorHandler\Collecting;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\User
 */
class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users  = [];
        $WHEREDepartment = $WHERESearch = "";
        if(isset($request->department) && $request->department != "") {
            $WHEREDepartment = " AND u.department_id = '{$request->department}'";
        }

        $query = "SELECT u.* 
                  FROM  users u
                  WHERE u.deleted_at IS NULL {$WHEREDepartment}";

        $result = DB::select($query);

        foreach($result as $item){
            $users [] = User::model($item);
        }

        //return
        return $this->showList(collect($users));
    }

    public function list(Request $request)
    {
        $users  = [];
        $WHEREDepartment = $WHERESearch = "";
        if(isset($request->department) && $request->department != "") {
            $WHEREDepartment = " AND u.department_id = '{$request->department}'";
        }
//CONCAT(u.title, ' ', u.first_name, ' ', u.last_name) AS Name

        $query = "SELECT u.*, CONCAT_WS(' ', u.title, u.first_name, u.last_name) AS Name
                  FROM   users u
                  WHERE  u.deleted_at IS NULL {$WHEREDepartment}";

        $result = DB::select($query);

        foreach($result as $item){
            $users [] = [
                'id'   => $item->id,
                'name' => $item->Name,
            ];
        }

        //return
        return $this->showList(collect($users));
    }

    /**
     * Display a listing of the resource.
     *
     * \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recently(Request $request)
    {
        $users  = [];
        $result = User::orderBy('id', 'desc')->take(5)->get();

        foreach($result as $item){
            $users [] = User::model($item);
        }

        //return
        return $this->showList(collect($users));
    }

    /**
     * Display a listing of the resource.
     *
     * \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function leave_count(Request $request)
    {
        $count = 0;
        $date  = isset($request->date) ? Helpers::formatDate($request->date, "Y-m-d") : date("Y-m-d");
        $query = "SELECT COUNT(*) AS count
                  FROM   leaves l
                  WHERE  '$date' BETWEEN l.from_date AND l.to_date";

        $result = DB::select($query);

        foreach($result as $item){
            $count = $item->count;
        }

        //return
        return $this->showMessage($count);
    }


    /**
     * Display a listing of the resource.
     *
     * \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function absent_count(Request $request)
    {
        $count = 0;
        $date  = isset($request->date) ? Helpers::formatDate($request->date, "Y-m-d") : date("Y-m-d");
        $query = "SELECT COUNT(*) AS count
                  FROM   users u
                  WHERE  u.id NOT IN (SELECT user_id FROM records WHERE date = '$date') 
                         AND u.id NOT IN (SELECT user_id FROM leaves WHERE '$date' BETWEEN from_date AND to_date)";

        $result = DB::select($query);

        foreach($result as $item){
            $count = $item->count;
        }

        //return
        return $this->showMessage($count);
    }


    /**
     * Display a listing of the resource.
     *
     * \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function active_count(Request $request)
    {
        $count = User::where('status', 'Active')->count();

        //return
        return $this->showMessage($count);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate User Info
        $validator = Validator::make($request->all(), User::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 422);

        //Validate Address Info
        $validator = Validator::make($request->address, Address::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 422);

        //Validate Next of Kin Info
        $validator = Validator::make($request->next_of_kin, NextOfKin::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 422);

        //Validate Spouse Info
        $validator = Validator::make($request->spouse, Spouse::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 422);

        //Insert Address
        $address = new Address();
        $address->house_no      = $request->address['house_number'];
        $address->street_no     = $request->address['street_number'];
        $address->street_name   = $request->address['street_name'];
        $address->suburb        = $request->address['suburb'];
        $address->city          = $request->address['city'];
        $address->province      = $request->address['province'];
        $address->created_at    = Carbon::now('CAT');
        $address->created_by    = $request->user()->id;
        $address->save();


        //Insert Next of Kin Address
        $nok_address = new Address();
        $nok_address_data           = $request->next_of_kin['address'];
        $nok_address->house_no      = $nok_address_data['house_number'];
        $nok_address->street_no     = $nok_address_data['street_number'];
        $nok_address->street_name   = $nok_address_data['street_name'];
        $nok_address->suburb        = $nok_address_data['suburb'];
        $nok_address->city          = $nok_address_data['city'];
        $nok_address->province      = $nok_address_data['province'];
        $nok_address->created_at    = Carbon::now('CAT');
        $nok_address->created_by    = $request->user()->id;
        $nok_address->save();

        //Insert Next of Kin
        $next_of_kin = new NextOfKin();
        $next_of_kin->first_name    = $request->next_of_kin['first_name'];
        $next_of_kin->last_name     = $request->next_of_kin['last_name'];
        $next_of_kin->middle_name   = $request->next_of_kin['middle_name'];
        $next_of_kin->email         = $request->next_of_kin['email'];
        $next_of_kin->cell_phone    = $request->next_of_kin['cell_phone'];
        $next_of_kin->home_phone    = $request->next_of_kin['home_phone'];
        $next_of_kin->relationship  = $request->next_of_kin['relationship'];
        $next_of_kin->address_id    = $nok_address->id;
        $next_of_kin->created_at    = Carbon::now('CAT');
        $next_of_kin->created_by    = $request->user()->id;
        $next_of_kin->save();

        //Insert Spouse
        $spouse = new Spouse();
        $spouse->name           = $request->spouse['name'];
        $spouse->employer       = $request->spouse['employer'];
        $spouse->work_location  = $request->spouse['work_location'];
        $spouse->cell_phone     = $request->spouse['cell_phone'];
        $spouse->work_phone     = $request->spouse['work_phone'];
        $spouse->created_by     = $request->user()->id;
        $spouse->created_at     = Carbon::now('CAT');
        $spouse->save();

        //Password
        $password = isset($request->password) ? $request->password : ucfirst(strtolower(str_replace(" ", "", $request->last_name)))."@123";
        $user = new User();
        $user->employee_code        = $request->employee_code;
        $user->title                = $request->title;
        $user->first_name           = $request->first_name;
        $user->last_name            = $request->last_name;
        $user->maiden_name          = $request->maiden_name;
        $user->middle_name          = $request->middle_name;
        $user->preferred_name       = $request->preferred_name;
        $user->id_number            = $request->id_number;
        $user->nationality          = $request->nationality;
        $user->supervisor           = $request->supervisor;
        $user->gender               = $request->gender;
        $user->phone_number         = $request->phone_number;
        $user->email                = $request->email;
        $user->nationality          = $request->nationality;
        $user->supervisor           = $request->supervisor;
        $user->marital_status       = $request->marital_status;
        $user->department_id        = $request->department;
        $user->work_cell_phone      = $request->work_cell_phone;
        $user->work_phone           = $request->work_phone;
        $user->work_location        = $request->work_location;
        $user->start_date           = $request->start_date;
        $user->job_title            = $request->job_title;
        $user->work_email           = $request->work_email;
        $user->home_phone           = $request->home_phone;
        $user->work_email           = $request->work_email;
        $user->uif_number           = $request->uif_number;
        $user->payment_number       = $request->payment_number;
        $user->address_id           = $address->id;
        $user->spouse_id            = $spouse->id;
        $user->next_of_kin_id       = $next_of_kin->id;
        //Only update User Type if it's set
        if(isset($request->user_type) && $request->user_type != ""){
            $user->user_type = $request->user_type;
        }
        $user->profile_picture      = $request->profile_picture;
        $user->password             = User::encryptPassword($password);
        $user->status               = User::ACTIVE;
        $user->verified             = User::UNVERIFIED;
        $user->verification_token   = User::generateVerificationToken();
        $user->created_at           = Carbon::now('CAT');
        $user->created_by           = $request->user()->id;
        $user->save();

        return $this->showList(collect(User::model($user)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::model(User::where('id', $id)->firstOrFail());

        return $this->showOne(collect($user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_password(Request $request, $id)
    {
        $user = User::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), User::updatePasswordRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        //Update User
        $user->password   = User::encryptPassword($request->password);
        $user->updated_by = $request->user()->id;
        $user->updated_at = Carbon::now('CAT');
        $user->save();

        //return
        return $this->showOne(Collect(User::model($user)),200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), User::updateRules($id));
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        //Validate Address Info
        $validator = Validator::make($request->address, Address::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 422);

        //Validate Next of Kin Info
        $validator = Validator::make($request->next_of_kin, NextOfKin::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 422);

        //Validate Spouse Info
        $validator = Validator::make($request->spouse, Spouse::createRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 422);

        //Update User
        $user->employee_code        = $request->employee_code;
        $user->title                = $request->title;
        $user->first_name           = $request->first_name;
        $user->last_name            = $request->last_name;
        $user->maiden_name          = $request->maiden_name;
        $user->middle_name          = $request->middle_name;
        $user->preferred_name       = $request->preferred_name;
        $user->id_number            = $request->id_number;
        $user->nationality          = $request->nationality;
        $user->supervisor           = $request->supervisor;
        $user->gender               = $request->gender;
        $user->phone_number         = $request->phone_number;
        $user->email                = $request->email;
        $user->nationality          = $request->nationality;
        $user->supervisor           = $request->supervisor;
        $user->marital_status       = $request->marital_status;
        $user->department_id        = $request->department;
        $user->work_cell_phone      = $request->work_cell_phone;
        $user->work_phone           = $request->work_phone;
        $user->work_location        = $request->work_location;
        $user->start_date           = $request->start_date;
        $user->job_title            = $request->job_title;
        $user->work_email           = $request->work_email;
        $user->home_phone           = $request->home_phone;
        $user->work_email           = $request->work_email;
        $user->uif_number           = $request->uif_number;
        $user->payment_number       = $request->payment_number;
        $user->updated_at           = Carbon::now('CAT');
        $user->updated_by           = $request->user()->id;
        //Only update User Type if it's set
        if(isset($request->user_type) && $request->user_type != ""){
            $user->user_type = $request->user_type;
        }

        if($request->has('password') && $request->password != ""){
            $user->password = User::encryptPassword($request->password);
        }

        //Update Address
        $address = Address::where('id', $user->address_id)->first();
        if(!$address){
            $address = new Address();
        }
        $address->house_no      = $request->address['house_number'];
        $address->street_no     = $request->address['street_number'];
        $address->street_name   = $request->address['street_name'];
        $address->suburb        = $request->address['suburb'];
        $address->city          = $request->address['city'];
        $address->province      = $request->address['province'];
        $address->updated_at    = Carbon::now('CAT');
        $address->updated_by    = $request->user()->id;

        //Update Next of Kin
        $next_of_kin = NextOfKin::where('id', $user->next_of_kin_id)->first();
        if(!$next_of_kin){
            $next_of_kin = new NextOfKin();
        }
        $next_of_kin->first_name    = $request->next_of_kin['first_name'];
        $next_of_kin->last_name     = $request->next_of_kin['last_name'];
        $next_of_kin->middle_name   = $request->next_of_kin['middle_name'];
        $next_of_kin->email         = $request->next_of_kin['email'];
        $next_of_kin->cell_phone    = $request->next_of_kin['cell_phone'];
        $next_of_kin->home_phone    = $request->next_of_kin['home_phone'];
        $next_of_kin->relationship  = $request->next_of_kin['relationship'];
        $next_of_kin->updated_at    = Carbon::now('CAT');
        $next_of_kin->updated_by    = $request->user()->id;

        //Update Next of Kin Address
        $nok_address = NextOfKin::where('id', $next_of_kin->next_of_kin_id)->first();
        if(!$nok_address){
            $nok_address = new Address();
        }
        $nok_address_data           = $request->next_of_kin['address'];
        $nok_address->house_no      = $nok_address_data['house_number'];
        $nok_address->street_no     = $nok_address_data['street_number'];
        $nok_address->street_name   = $nok_address_data['street_name'];
        $nok_address->suburb        = $nok_address_data['suburb'];
        $nok_address->city          = $nok_address_data['city'];
        $nok_address->province      = $nok_address_data['province'];
        $nok_address->updated_at    = Carbon::now('CAT');
        $nok_address->updated_by    = $request->user()->id;

        //Insert Spouse
        $spouse = Spouse::where('id', $user->spouse_id)->first();
        if(!$spouse){
            $spouse = new Spouse();
        }
        $spouse->name           = $request->spouse['name'];
        $spouse->employer       = $request->spouse['employer'];
        $spouse->work_location  = $request->spouse['work_location'];
        $spouse->cell_phone     = $request->spouse['cell_phone'];
        $spouse->work_phone     = $request->spouse['work_phone'];
        $spouse->updated_at     = Carbon::now('CAT');
        $spouse->updated_by     = $request->user()->id;

        //Saves
        $spouse->save();
        $nok_address->save();
        $next_of_kin->save();
        $address->save();

        if($address->id != $user->address_id)            $user->address_id        = $address->id;
        if($spouse->id != $user->spouse_id)              $user->spouse_id         = $spouse->id;
        if($next_of_kin->id != $user->next_of_kin_id)    $user->next_of_kin_id    = $next_of_kin->id;
        if($nok_address->id != $next_of_kin->address_id) $next_of_kin->address_id = $nok_address->id;
        $user->save();

        return $this->showOne(Collect(User::model($user)),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $user->deleted_by = $request->user()->id;
        $user->save();
        $user->delete();

        //return
        return $this->showOne(collect(User::model($user)));
    }

    public function verify($token)
    {
        if(User::isValidToken($token)) {
            $user = User::where('verification_token', $token)->firstOrFail();
            $user->verification_token = "";
            $user->verified           = User::VERIFIED;
            $user->save();

            return $this->showMessage("The Account has been successfully verified");
        }
        else return $this->errorResponse("Invalid Verification Token", 400);
    }

    public function login(Request $request)
    {
        $login = new Login();
        $login->username       = $request->email;
        $login->created_at     = Carbon::now('CAT');
        $login->updated_at     = null;
        $login->attempt_number = Login::getAttemptNumber($request->email);

        $validator = Validator::make($request->all(), User::loginRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            // Authentication passed...
            $user = Auth::user();
            $permissions = AccessControl::info($user->user_type);

            //Only If the access group has login permission
            if(isset($permissions['login']) && $permissions['login'] == 1) {
                $token = $user->createToken('Ganizani - Time Attendance Password Grant Client')->accessToken;
                return $this->showOne(collect(User::model($user, $token)));
            }
            else return $this->errorResponse( "Please contact System Admin", 401);
        }
        else return $this->errorResponse( "Invalid Login Credentials", 401);
    }

    public function mobile_login(Request $request)
    {
        if(!isset($request->email) || !isset($request->password)  || ($request->password == "" || $request-> email == "")){
            $export = [
                'success' => 0,
                'email'   => $request->email,
                'message' => "Please Enter All Required Fields",
            ];
            return response()->json($export, 200);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            // Authentication passed...
            $user = Auth::user();
            $token =  $user->createToken('Ganizani - Time Attendance Password Grant Client')->accessToken;

            $export = [
                'success' => 1,
                'id'      => $user->id,
                'email'   => $request->email,
                'compID'  => 1 ,
                'deptID'  => $user->department_id,
                'name'    => $user->title. " ". $user->first_name . " ".$user->last_name,
                'message' => "Login Successful",
            ];

            return response()->json($export, 200);
        }
        else{
            $export = [
                'success' => 0,
                'email'   => $request->email,
                'message' => "Invalid Details",
            ];
            return response()->json($export, 200);
        }
    }

    public function forgotPassword(Request $request){

        $validator = Validator::make($request->all(), User::forgotPasswordRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        if(PasswordReset::hasValidTokenAlready($request->email) == "true") {
            $token = PasswordReset::where('email', $request->email)->firstOrFail();
            $token->delete();
        }

        $forgot_password = new PasswordReset();
        $forgot_password->email     = $request->email;
        $forgot_password->token     = User::generateVerificationToken();
        $forgot_password->expire_at = Carbon::now('CAT')->addDay();
        $forgot_password->save();

        //Send Message
        $user = User::where('email', $request->email)->firstOrFail();
        $forgot_password->notify(new ForgotPasswordNotification($user, $forgot_password->token));

        return $this->showMessage("Password reset Link has been sent to {$request->email}, The link is valid for 24 Hours.", 200);
    }

    public function resetPassword(Request $request) {

        $validator = Validator::make($request->all(), User::resetPasswordRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        $regex = Helpers::PasswordRegex($request->password, $request->password_confirmation);
        if($regex != "") return $this->errorResponse($regex,400);

        if(PasswordReset::isValidToken($request->token, $request->email) == "true") {
            $user           = User::where('email', $request->email)->firstOrFail();
            $user->password = User::encryptPassword($request->password);
            $user->save();

            PasswordReset::destroyToken($request->token);

            return $this->showMessage("Password has been successfully updated.", 200);
        }
        else return $this->errorResponse("Invalid Security Token", 400);
    }

    public function sendVerificationEmail(Request $request){

        if($request->has('email') && $request->email != ""){

            $user = User::where('email', $request->email)->firstOrFail();

            //$user->notify(new UserRegistration());

            return $this->showMessage('Verification Email Successfully Sent.', 200);
        }
        else{
            return $this->errorResponse('Please Provide a Valid email address.', 404);
        }
    }

    public function show_from_token($token){

        $user = PasswordReset::where('token', $token)->firstOrFail();

        return $this->showOne(collect($user));
    }
}
