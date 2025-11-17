<?php
require 'db_connection.php';

if ($_POST) {
    $login = $_POST['login'];
    $pass  = password_hash($_POST['pass'], PASSWORD_BCRYPT);  

    $sql = "INSERT INTO users (login, password) VALUES ('$login', '$pass')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Регистрация успешна! Теперь можно <a href='index.php'>войти</a>";
    } else {
        echo "Ошибка: " . mysqli_error($conn) . "<br><a href='register.php'>Назад</a>";
    }
}
?>