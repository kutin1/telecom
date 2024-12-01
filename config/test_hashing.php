<?php
// Данный файл предназначен для тестирования функции хэширования паролей, которая не реализована в проекте
// по причине неизвестной неправильной валидации сравнения данных
// Подключение к базе данных
$connection = mysqli_connect("localhost", "root", "", "telecom");
if ($connection === false) {
    die("Ошибка подключения: " . mysqli_connect_error());
}

// Имя пользователя для проверки
$username = "ivanov";

// Запрос на получение хеша пароля для пользователя
$sql = "SELECT Password FROM Accounts WHERE Username = '$username'";
$result = mysqli_query($connection, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $storedHash = $row['Password'];

    // Пароль для проверки
    $password = "222";

    // Сравнение хешей паролей
    if (password_verify($password, $storedHash)) {
        echo "Хеш пароля совпадает!";
    } else {
        echo "Хеш пароля не совпадает!";
    }
} else {
    echo "Пользователь не найден";
}

// Закрытие соединения с базой данных
mysqli_close($connection);
?>