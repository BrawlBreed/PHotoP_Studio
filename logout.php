<?php

session_start();
unset($_SESSION['user']['id']);
unset($_SESSION['user']['username']);
session_destroy();

header('Location:login.php');
exit;
?>