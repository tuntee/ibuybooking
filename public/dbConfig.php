<?php

$host = "128.199.66.250";
//$port = 3306;
$username = "BookDBAdmin007";
$password = "HanSBnJaRZftQwS3";
$database = "BookingDB";
//$database = "userfrosting";

$con = mysqli_connect($host, $username, $password);

if(!$con){
//    die('Cannot cannect database: '.mysql_error());
    echo "Error: Unable to connect to Database." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
$db_selected = mysqli_select_db($con,$database);
if(!$db_selected){
    die('Cannot select database name '.$database.' : '.mysqli_connect_error());
}

mysqli_query($con, "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");