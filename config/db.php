<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'telecom';

$connection = mysqli_connect($host, $username, $password, $database);

if (!$connection) {
    die('Ошибка подключения: ' . mysqli_connect_error());
}
?>