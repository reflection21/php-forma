<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Секрет</title></head><body>
<h1>Вітаємо, <?=htmlspecialchars($_SESSION['user'])?>!</h1>
<p>Це захищена сторінка. Тільки авторизовані користувачі можуть її бачити.</p>
<br>
<a href="change_password.php">Змінити пароль</a> | <a href="logout.php">Вийти</a>
</body></html>