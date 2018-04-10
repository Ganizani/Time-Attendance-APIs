<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:07 PM
 */
namespace App\Http;

use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;

class Helpers
{

    public static function PasswordRegex($password, $confirm_password){


        $uppercase      = preg_match('@[A-Z]@', $password);
        $lowercase      = preg_match('@[a-z]@', $password);
        $number         = preg_match('@[0-9]@', $password);
        $special_symbol = preg_match('@[\W]@',  $password);

        if($password != $confirm_password){
            $response = "Passwords Do Not Match";
        }
        elseif(strlen($password) < 8){
            $response = "Password Must Contain At Least 8 Characters";
        }
        elseif(!$uppercase ) {
            $response = "Password Must Contain At Least 1 Upper Case Character [A-Z]";
        }
        elseif(!$lowercase){
            $response = "Password Must Contain At Least 1 Lower Case Character [a-z]";
        }
        elseif(!$number){
            $response = "Password Must Contain At Least 1 Number [0-9]";
        }
        elseif(!$special_symbol){
            $response = "Password Must Contain At Least 1 Special Character";
        }
        else{
            $response = "";
        }

        return $response;
    }

    public static function formatDate($date, $format = "Y-m-d H:i:s"){

        if($date != null){
            return date($format, strtotime($date));
        }
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
}