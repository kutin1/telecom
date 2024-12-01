<?php
session_start(); // Начать сессию
session_unset(); // Очистить все переменные сессии
session_destroy(); // Уничтожить сессию

// Перенаправить на главную страницу
header("Location: ../index.php");
exit;
?>