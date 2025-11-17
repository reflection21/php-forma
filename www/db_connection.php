<?php
$host = 'localhost'; # db compose
$user = 'user4_sql_user';    
$pass = '21012000zxc';  
$db   = 'user4_sql_lab';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("error");
?>