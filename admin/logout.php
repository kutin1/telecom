<?php
// Начало сессии
session_start();

// Удаление сессионных данных
session_destroy();

// Перенаправление на страницу входа
header("Location: login.php");
exit;
?>