<?php 

$host = 'localhost';
$db = 'shop';
$user = 'Zlatko';
$password = 'gunz&granadez187';

$conn = mysqli_connect($host, $user, $password, $db);

if(!$conn){
    die('An error has occured while reaching the database');
}
