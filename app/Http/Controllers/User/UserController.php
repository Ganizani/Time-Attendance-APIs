<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:09 PM
 */

namespace App\Http\Controllers\User;
;
use App\Http\Controllers\ApiController;
use App\Login;
use App\PasswordReset;
use App\User;
use Carbon\Carbon;
use App\Http\Helpers;
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
        $result = User::all();

        $users = array();

        foreach($result as $item){
            $users [] = User::userModel($item);
        }

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
        $validator = Validator::make($request->all(), User::createRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $regex = Helpers::PasswordRegex($request->password,  $request->password_confirmation);

        if($regex != ""){
            return $this->errorResponse($regex,422);
        }

        $user = new User();
        $user->title              = $request->title;
        $user->first_name         = $request->first_name;
        $user->last_name          = $request->last_name;
        $user->gender             = $request->gender;
        $user->user_type          = $request->user_type;
        $user->phone_number       = $request->phone_number;
        $user->email              = $request->email;
        $user->password           = User::encryptPassword($request->password);
        $user->status             = User::ACTIVE;
        $user->verified           = User::UNVERIFIED;
        $user->created_at         = Carbon::now();
        $user->verification_token = User::generateVerificationToken();
        $user->save();
        //$user->notify(new UserRegistration());

        //generate Access Token
        //AccessToken::generateAccessToken($user_id);

        //return user information
        //return $this->showOne(collect(User::userModel($user)));

        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::userModel(User::where('id', $id)->firstOrFail());

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

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        if($request->has('email') && $user->email != $request->email){
            $user->verified           = User::UNVERIFIED;
            $user->verification_token = User::generateVerificationToken();
            $user->email              = $request->email;
        }

        if($request->has('password')) {
            $user->password = User::encryptPassword($request->password) ;
        }

        $user->title        = $request->title;
        $user->first_name   = $request->first_name;
        $user->last_name    = $request->last_name;
        $user->gender       = $request->gender;
        $user->phone_number = $request->phone_number;

        if($user->isClean()){ //if the user has changed
            return $this->errorResponse('You need to specify a different value to update',422);
        }
        $user->updated_at      = Carbon::now();
        $user->last_updated_by = isset($request->updated_by)? $request->updated_by : "";
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

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $email    = $request->email;
        $password = User::encryptPassword($request->password);
        $user     = User::where("email", $email)->where("password", $password)->where('status', User::ACTIVE)->first();

        if ($user) {
            $login->successful = 1;
            $login->save();

            //generate Access Token
            //AccessToken::generateAccessToken($user->id);

            //return user information
            return $this->showOne(collect(User::userLoginModel($user)));
        }
        else {
            $login->successful = 0;
            $login->save();
            return $this->errorResponse("Invalid Login Credentials", 400);
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

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $regex = Helpers::PasswordRegex($request->password, $request->password_confirmation);

        if($regex != ""){
            return $this->errorResponse($regex,400);
        }

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
