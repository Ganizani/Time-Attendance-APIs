<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:07 PM
 */
namespace App\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\Storage;

class Helpers
{

    public static function PasswordRegex($password, $confirm_password){

        if($password != $confirm_password) $response = "Passwords Do Not Match";
        elseif(strlen($password) < 8)      $response = "Password Must Contain At Least 8 Characters";
        elseif(!preg_match('@[A-Z]@', $password)) $response = "Password Must Contain At Least 1 Upper Case Character [A-Z]";
        elseif(!preg_match('@[a-z]@', $password)) $response = "Password Must Contain At Least 1 Lower Case Character [a-z]";
        elseif(!preg_match('@[0-9]@', $password)) $response = "Password Must Contain At Least 1 Number [0-9]";
        elseif(!preg_match('@[\W]@',  $password)) $response = "Password Must Contain At Least 1 Special Character";
        else $response = "";

        return $response;
    }

    public static function formatDate($date, $format = "Y-m-d H:i:s"){
        if($date != null) return date($format, strtotime($date));
        else return null;
    }

    public static function swapDates(&$from, &$to){
        $from = strtotime($from);
        $to   = strtotime($to);

        //check to see the greatest date
        if($from > $to){
            $t_fr = $from;
            $from = $to;
            $to   = $t_fr;
        }

        $from = date('Y-m-d',$from);
        $to   = date('Y-m-d',$to);
    }

    public static function getDateDiff($from, $to){
        $from = date("Y-m-d" , strtotime($from));
        $to   = date("Y-m-d" , strtotime($to));

        $result = DB::select("SELECT DATEDIFF('$from', '$to') AS Difference");

        return abs($result[0]->Difference);
    }

    public static function getDatesArray($from, $to){
        $difference = Helpers::getDateDiff($from, $to);
        $x_date     = $from;
        $dates      = [];

        for($x = 0; $x <= $difference; $x++){
            $dates [] = Helpers::formatDate("Y-m-d", $x_date);
            $x_date = Helpers::formatDate("Y-m-d", date("Y-m-d", strtotime("1 days", strtotime($x_date))));
        }

        return $dates;
    }

    public static function getWeekendDates($from, $to){
        $string = "";
        $begin  = new DateTime( $from );
        $end    = new DateTime( $to );
        $end    = $end->modify( '+1 day' );

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);

        foreach($daterange as $date){
            $day_num = $date->format("N"); /* 'N' number days 1 (mon) to 7 (sun) */

            if($day_num == 6 || $day_num == 7){
                $string = $string . "'".$date->format("Y-m-d")."',";
            }
        }

        return rtrim($string,",");
    }

    public static function saveBase64FileToDisk($disk, $file_data, $file_name){

        $file  = "";
        @list($type, $file_data) = explode(';', $file_data);
        @list(, $file_data) = explode(',', $file_data);

        if($file_data!="") $file = Storage::disk($disk)->put($file_name, base64_decode($file_data));

        return $file;
    }

    public static function saveFile(Request $request, $disk){
        $path = null;
        //Save the attachment if it was uploaded
        if ($request->has('attachment') &&  $request->attachment != '') {
            $info      = $request->attachment;
            $file_data = $info['data_url'];
            $file_name = Helpers::generateFileName().'.'.$info['extension']; //generating unique file name;
            $file      = Helpers::saveBase64FileToDisk($disk,$file_data, $file_name);

            if($file) $path = env('API_URL')."/storage/{$disk}/{$file_name}";
        }

        return $path;
    }

    public static function  generateFileName(){
        return str_random(20). "-" . strtotime(date("Y-m-d H:i:s")) . "-" . rand(1000, 10000);
    }
}