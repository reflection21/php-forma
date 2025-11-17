<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require 'db_connection.php';
$user = $_SESSION['user'];

// ==================== form (POST) ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newpass  = $_POST['newpass']  ?? '';
    $newpass2 = $_POST['newpass2'] ?? '';

    if ($newpass !== $newpass2) {
        $error = "Паролі не співпадають!";
    } elseif (strlen($newpass) < 4) {
        $error = "Пароль занадто короткий (мін. 4 символи)!";
    } else {
        $hash = password_hash($newpass, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = '$hash' WHERE login = '$user'";

        if (mysqli_query($conn, $sql)) {
            $success = "Пароль успішно змінено!";
        } else {
            $error = "Помилка бази: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Зміна пароля</title>
</head>
<body>
    <h2>Зміна пароля для <?=htmlspecialchars($user)?></h2>

    <?php if (isset($success)): ?>
        <p style="color:green;"><strong><?= $success ?></strong></p>
        <a href="secret.php">← Повернутися на захищену сторінку</a>
    <?php else: ?>

        <?php if (isset($error)): ?>
            <p style="color:red;"><strong><?= $error ?></strong></p>
        <?php endif; ?>

        <form method="post" action="">
            Новий пароль: <input type="password" name="newpass" required><br><br>
            Повторіть:    <input type="password" name="newpass2" required><br><br>
            <input type="submit" value="Змінити пароль">
        </form>

    <?php endif; ?>

    <br><br>
    <a href="secret.php">← Назад</a>
</body>
</html>