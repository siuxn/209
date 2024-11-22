<?php
$host ="localhost";
$user="root";
$password="";
$db="DIP209";

$data=mysqli_connect($host,$user,$password,$db);
if($data===false)
{
    die("connection_error");
}

?>