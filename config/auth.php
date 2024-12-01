<?php

session_start(); // Начать сессию, если ещё не начата
include 'db.php'; // Подключить файл с настройками базы данных

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($connection, $_POST['user-login']);
    $password = $_POST['user-password']; // Не используем mysqli_real_escape_string для пароля

    // Подготовленный запрос к базе данных для получения учетной записи по имени пользователя
    $sql = "SELECT * FROM accounts WHERE Username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            // Пароль верный, установить сессию и перенаправить
            $_SESSION['AccountId'] = $row['AccountId'];
            $_SESSION['Username'] = $row['Username'];
            $_SESSION['Role'] = $row['Role'];

            // Определить URL для перенаправления в зависимости от роли
            $redirectUrl = '';
            if ($row['Role'] == 'Client') {
                $redirectUrl = 'account.php';
            } elseif ($row['Role'] == 'Employee') {
                $redirectUrl = 'admin_panel.php';
            }

            // Отправить JSON-ответ для JavaScript handling
            echo json_encode(array('success' => true, 'redirectUrl' => $redirectUrl));
            exit;
        } else {
            // Неверный пароль
            echo json_encode(array('success' => false, 'message' => 'Неверный логин или пароль.'));
            exit;
        }
    } else {
        // Пользователь не найден
        echo json_encode(array('success' => false, 'message' => 'Пользователь не найден.'));
        exit;
    }

    $stmt->close();
} else {
    // Перенаправить, если скрипт вызван напрямую без POST данных
    header("Location: ../index.php");
    exit;
}
?>
