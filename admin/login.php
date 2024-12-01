<?php
include_once '../config/db.php';

// Обработка формы входа
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Запрос к базе данных для получения учетной записи по имени пользователя
    $sql = "SELECT * FROM Accounts WHERE Username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['Password'];
        $role = $row['Role'];

        // Проверка пароля
        if (password_verify($password, $hashed_password)) {
            // Проверка роли пользователя
            if (strtolower($role) === 'admin') { // Сравниваем роль с 'admin' без учета регистра
                // Аутентификация администратора
                session_start();
                $_SESSION['user_id'] = $row['AccountId'];
                $_SESSION['user_role'] = 'admin';
                header("Location: panel.php");
                exit;
            } else {
                // Пользователь не является администратором
                echo "Вы не являетесь администратором.";
            }
        } else {
            echo "Неверное имя пользователя или пароль.";
        }
    } else {
        echo "Неверное имя пользователя или пароль.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Админ | Телеком</title>
    <link href="../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../src/img/favicons/favicon.ico">
</head>
<body class="admin-login-page">
    <div class="admin-login-container">
    <h1>Вход для администраторов</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label>Имя пользователя: <input class="admin-login-input" type="text" name="username" required></label><br>
        <label>Пароль: <input class="admin-login-input admin-password-input" type="password" name="password" required></label><br>
        <input class="admin-login-button" type="submit" name="submit" value="Войти">
    </form>
    </div>
    <footer>
    <p>Сервисы «Телеком» © 2024 | Владислав Константинович Кутин | Все права защищены | <a class="admin-link" href="/index.php">Главная страница</a></p>
    </footer>
</body>
</html>
