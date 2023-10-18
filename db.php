<?php

$host = 'localhost';
$db = 'shop';
$user = 'Zlatko';
$password = 'gunz&granadez187';

$conn = mysqli_connect($host, $user, $password, $db);

if(!$conn){
    die('Възникна грешка при опит за връзка с базата от данни. Моля, опитайте по-късно!');
}