<?php
session_start();
include 'db.php';

// Проверка, что пользователь авторизован
if (!isset($_SESSION['AccountId'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
    exit;
}

// Получение данных об оплате из POST-запроса
$invoiceId = $_POST['invoiceId'];
$paymentAmount = $_POST['paymentAmount'];
$customerId = $_SESSION['AccountId']; // или $clientId из сессии

// Проверка корректности данных
if (empty($invoiceId) || empty($paymentAmount) || !is_numeric($paymentAmount) || $paymentAmount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Некорректные данные']);
    exit;
}

// Начало транзакции
mysqli_begin_transaction($connection);

// Обновление баланса счета
$updateInvoiceQuery = "UPDATE Invoices SET InvoiceBalance = InvoiceBalance + $paymentAmount WHERE InvoiceId = $invoiceId";
if (mysqli_query($connection, $updateInvoiceQuery)) {
    // Сохранение записи об оплате
    $insertPaymentQuery = "INSERT INTO Payments (InvoiceId, ClientId, PaymentDate, PaymentAmount) VALUES ($invoiceId, $customerId, NOW(), $paymentAmount)";
    if (mysqli_query($connection, $insertPaymentQuery)) {
        // Фиксация транзакции
        mysqli_commit($connection);
        echo json_encode(['success' => true]);
    } else {
        // Откат транзакции в случае ошибки
        mysqli_rollback($connection);
        $errorMessage = "Ошибка при сохранении записи об оплате: " . mysqli_error($connection);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $errorMessage]);
    }
} else {
    // Откат транзакции в случае ошибки
    mysqli_rollback($connection);
    $errorMessage = "Ошибка при обновлении баланса счета: " . mysqli_error($connection);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $errorMessage]);
}