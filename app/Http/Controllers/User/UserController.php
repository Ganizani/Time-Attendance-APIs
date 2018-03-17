<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:09 PM
 */

namespace App\Http\Controllers\User;
;

use App\Address;
use App\Http\Controllers\ApiController;
use App\Login;
use App\NextOfKin;
use App\PasswordReset;
use App\Spouse;
use App\User;
use Carbon\Carbon;
use App\Http\Helpers;
use Illuminate\Support\Facades\Auth;
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users  = [];
        $result = User::all();
        foreach($result as $item){
            $users [] = User::model($item);
        }

        //return
        return $this->showList(collect($users));
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
        $address->house_no      = $request->address['house_no'];
        $address->street_no     = $request->address['street_no'];
        $address->street_name   = $request->address['street_name'];
        $address->suburb        = $request->address['suburb'];
        $address->city          = $request->address['city'];
        $address->province      = $request->address['province'];
        $address->country       = $request->address['country'];
        $address->postal_code   = $request->address['postal_code'];
        $address->created_at    = Carbon::now();
        $address->created_by    = $request->created_by;
        $address->updated_by    = null;
        $address->updated_at    = null;
        $address->save();

        //Insert Next of Kin
        $next_of_kin = new NextOfKin();
        $next_of_kin->house_no      = $request->next_of_kin['house_no'];
        $next_of_kin->street_no     = $request->next_of_kin['street_no'];
        $next_of_kin->street_name   = $request->next_of_kin['street_name'];
        $next_of_kin->suburb        = $request->next_of_kin['suburb'];
        $next_of_kin->city          = $request->next_of_kin['city'];
        $next_of_kin->province      = $request->next_of_kin['province'];
        $next_of_kin->country       = $request->next_of_kin['country'];
        $next_of_kin->postal_code   = $request->next_of_kin['postal_code'];
        $next_of_kin->created_at    = Carbon::now();
        $next_of_kin->created_by    = $request->created_by;
        $next_of_kin->updated_by    = null;
        $next_of_kin->updated_at    = null;
        $next_of_kin->save();

        //Insert Spouse
        $spouse = new Spouse();
        $spouse->name           = $request->spouse['name'];
        $spouse->employer       = $request->spouse['employer'];
        $spouse->work_location  = $request->spouse['work_location'];
        $spouse->cell_phone     = $request->spouse['cell_phone'];
        $spouse->work_phone     = $request->spouse['work_phone'];
        $spouse->created_by     = $request->created_by;
        $spouse->created_at     = Carbon::now();
        $spouse->updated_at     = null;
        $spouse->save();

        $user = new User();
        $user->employee_code        = $request->employee_code;
        $user->title                = $request->title;
        $user->first_name           = $request->first_name;
        $user->maiden_name          = $request->maiden_name;
        $user->middle_name          = $request->middle_name;
        $user->preferred_name       = $request->preferred_name;
        $user->id_number            = $request->id_number;
        $user->nationality          = $request->nationality;
        $user->supervisor           = $request->supervisor;
        $user->gender               = $request->gender;
        $user->user_type            = $request->user_type;
        $user->phone_number         = $request->phone_number;
        $user->email                = $request->email;
        $user->nationality          = $request->nationality;
        $user->supervisor           = $request->supervisor;
        $user->marital_status       = $request->marital_status;
        $user->department_id        = $request->department_id;
        $user->work_cell_phone      = $request->work_cell_phone;
        $user->work_phone           = $request->work_phone;
        $user->work_location        = $request->work_location;
        $user->start_date           = $request->start_date;
        $user->job_title            = $request->job_title;
        $user->work_email           = $request->work_email;
        $user->home_phone           = $request->home_phone;
        $user->work_email           = $request->work_email;
        $user->address_id           = $address->id;
        $user->spouse_id            = $spouse->id;
        $user->next_of_kin_id       = $next_of_kin->id;
        $user->profile_picture      = $request->profile_picture;
        $user->password             = User::encryptPassword($request->password);
        $user->status               = User::ACTIVE;
        $user->verified             = User::UNVERIFIED;
        $user->verification_token   = User::generateVerificationToken();
        $user->created_at           = Carbon::now();
        $user->created_by           = $request->created_by;
        $user->updated_by           = null;
        $user->updated_at           = null;
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
    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), User::updateRules($user->id));
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);


        $user->title        = $request->title;
        $user->first_name   = $request->first_name;
        $user->last_name    = $request->last_name;
        $user->gender       = $request->gender;
        $user->phone_number = $request->phone_number;

        if($user->isClean()){ //if the user has changed
            return $this->errorResponse('You need to specify a different value to update',422);
        }
        $user->updated_at      = Carbon::now();
        $user->last_updated_by = $request->updated_by;
        $user->save();

        return $this->showOne($user,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $user->delete();

        return $this->showOne($user);
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
        $login->created_at     = Carbon::now();
        $login->updated_at     = null;
        $login->attempt_number = Login::getAttemptNumber($request->email);

        $validator = Validator::make($request->all(), User::loginRules());
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 400);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            // Authentication passed...
            $user = Auth::user();
            $token =  $user->createToken('Ganizani - Time Attendance Password Grant Client')->accessToken;
            return $this->showOne(collect(User::model($user, $token)));
        }
        else{
            return $this->errorResponse( "Invalid Login Credentials", 401);
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
        $forgot_password->expire_at = Carbon::now()->addDay();
        $forgot_password->save();

        //Send Message
        $user = User::where('email', $request->email)->firstOrFail();
        //$forgot_password->notify(new ForgotPasswordNotification($user));

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
