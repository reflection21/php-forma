<?php
session_start();
require 'db_connection.php';

if ($_POST) {
    $login = $_POST['login'];
    $pass  = $_POST['pass'];  

    $sql = "SELECT * FROM users WHERE login='$login'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($pass, $row['password'])) {
            $_SESSION['user'] = $login;
            header("Location: secret.php");
            exit;
        } else {
            echo "Невірний пароль!<br><a href='index.php'>Назад</a>";
        }
    } else {
        echo "Користувача не знайдено!<br><a href='index.php'>Назад</a>";
    }
}
?>