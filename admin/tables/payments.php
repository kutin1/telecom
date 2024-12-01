<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

include_once '../../config/db.php';

$PaymentId = '';
$InvoiceId = '';
$ClientId = '';
$PaymentDate = '';
$PaymentAmount = '';
$edit_state = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $InvoiceId = $_POST['InvoiceId'];
    $ClientId = $_POST['ClientId'];
    $PaymentDate = $_POST['PaymentDate'];
    $PaymentAmount = $_POST['PaymentAmount'];

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO Payments (InvoiceId, ClientId, PaymentDate, PaymentAmount) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iisd", $InvoiceId, $ClientId, $PaymentDate, $PaymentAmount);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $PaymentId = $_POST['PaymentId'];
        $sql = "UPDATE Payments SET InvoiceId=?, ClientId=?, PaymentDate=?, PaymentAmount=? WHERE PaymentId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iisdi", $InvoiceId, $ClientId, $PaymentDate, $PaymentAmount, $PaymentId);
        $stmt->execute();
    }

    header("Location: payments.php");
    exit;
}

if (isset($_GET['edit'])) {
    $PaymentId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM Payments WHERE PaymentId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $PaymentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();

    $InvoiceId = $payment['InvoiceId'];
    $ClientId = $payment['ClientId'];
    $PaymentDate = $payment['PaymentDate'];
    $PaymentAmount = $payment['PaymentAmount'];
}

if (isset($_GET['delete'])) {
    $PaymentId = $_GET['delete'];
    $sql = "DELETE FROM Payments WHERE PaymentId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $PaymentId);
    $stmt->execute();

    header("Location: payments.php");
    exit;
}

$sql = "SELECT p.PaymentId, i.InvoiceId, i.InvoiceNumber, c.ClientSurname, c.ClientName, p.PaymentDate, p.PaymentAmount
        FROM Payments p
        JOIN Invoices i ON p.InvoiceId = i.InvoiceId
        JOIN Clients c ON p.ClientId = c.ClientId";
$result = $connection->query($sql);
$payments = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Платежи</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Платежи</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Номер счета</th>
                    <th>Фамилия клиента</th>
                    <th>Имя клиента</th>
                    <th>Дата платежа</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo $payment['PaymentId']; ?></td>
                        <td><a href="invoices.php?invoice=<?php echo $payment['InvoiceId']; ?>"><?php echo $payment['InvoiceNumber']; ?></a></td>
                        <td><?php echo $payment['ClientSurname']; ?></td>
                        <td><?php echo $payment['ClientName']; ?></td>
                        <td><?php echo $payment['PaymentDate']; ?></td>
                        <td><?php echo $payment['PaymentAmount']; ?></td>
                        <td>
                            <a href="payments.php?edit=<?php echo $payment['PaymentId']; ?>">Редактировать</a>
                            <a href="payments.php?delete=<?php echo $payment['PaymentId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить эту запись?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2><?php echo $edit_state ? 'Редактировать платеж' : 'Добавить платеж'; ?></h2>
        <form method="post" action="payments.php">
            <input type="hidden" name="PaymentId" value="<?php echo $PaymentId; ?>">
            <div class="form-group">
                <label for="InvoiceId">Номер счета:</label>
                <select name="InvoiceId" required>
                    <option value="">Выберите счет</option>
                    <?php
                    $invoices = $connection->query("SELECT InvoiceId, InvoiceNumber FROM Invoices");
                    while ($invoice = $invoices->fetch_assoc()): ?>
                        <option value="<?php echo $invoice['InvoiceId']; ?>" <?php
                        echo $InvoiceId == $invoice['InvoiceId'] ? 'selected' : ''; ?>>
                        <?php echo $invoice['InvoiceNumber']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="ClientId">Клиент:</label>
            <select name="ClientId" required>
                <option value="">Выберите клиента</option>
                <?php
                $clients = $connection->query("SELECT ClientId, ClientSurname, ClientName FROM Clients");
                while ($client = $clients->fetch_assoc()): ?>
                    <option value="<?php echo $client['ClientId']; ?>" <?php
                    echo $ClientId == $client['ClientId'] ? 'selected' : ''; ?>>
                    <?php echo $client['ClientSurname'] . ' ' . $client['ClientName']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="PaymentDate">Дата платежа:</label>
        <input type="datetime-local" name="PaymentDate" value="<?php echo date('Y-m-d\TH:i:s', strtotime($PaymentDate)); ?>" required>
    </div>
    <div class="form-group">
        <label for="PaymentAmount">Сумма:</label>
        <input type="number" step="0.01" name="PaymentAmount" value="<?php echo $PaymentAmount; ?>" required>
    </div>
    <div class="form-group">
        <button type="submit" name="<?php echo $edit_state ? 'update' : 'save'; ?>" class="btn"><?php echo $edit_state ? 'Обновить' : 'Сохранить'; ?></button>
    </div>
</form>
<a href="/admin/panel.php" class="back-button">Назад</a>
</div>
</body>
</html>