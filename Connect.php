<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 4/25/18
 * Time: 11:14 AM
 */
$host     = '127.0.0.1';
$user     = 'gani_ness';
$password = 'epL5bOx8AjXoW6Yr';
$database = 'gani_ness_db';
$connection = mysqli_connect($host , $user , $password , $database);


if (mysqli_connect_errno()) {
    echo mysqli_connect_error();
    die("");
}
echo "Connected";
