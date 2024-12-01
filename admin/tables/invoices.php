<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

include_once '../../config/db.php';

$InvoiceId = '';
$InvoiceNumber = '';
$ClientId = '';
$InvoiceBalance = '';
$InvoiceStatus = '';
$edit_state = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $InvoiceNumber = $_POST['InvoiceNumber'];
    $ClientId = $_POST['ClientId'];
    $InvoiceBalance = $_POST['InvoiceBalance'];
    $InvoiceStatus = $_POST['InvoiceStatus'];

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO Invoices (InvoiceNumber, ClientId, InvoiceBalance, InvoiceStatus) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iids", $InvoiceNumber, $ClientId, $InvoiceBalance, $InvoiceStatus);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $InvoiceId = $_POST['InvoiceId'];
        $sql = "UPDATE Invoices SET InvoiceNumber=?, ClientId=?, InvoiceBalance=?, InvoiceStatus=? WHERE InvoiceId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iidsi", $InvoiceNumber, $ClientId, $InvoiceBalance, $InvoiceStatus, $InvoiceId);
        $stmt->execute();
    }

    header("Location: invoices.php");
    exit;
}

if (isset($_GET['edit'])) {
    $InvoiceId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM Invoices WHERE InvoiceId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $InvoiceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();

    $InvoiceNumber = $invoice['InvoiceNumber'];
    $ClientId = $invoice['ClientId'];
    $InvoiceBalance = $invoice['InvoiceBalance'];
    $InvoiceStatus = $invoice['InvoiceStatus'];
}

if (isset($_GET['delete'])) {
    $InvoiceId = $_GET['delete'];
    $sql = "DELETE FROM Invoices WHERE InvoiceId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $InvoiceId);
    $stmt->execute();

    header("Location: invoices.php");
    exit;
}

$sql = "SELECT i.InvoiceId, i.InvoiceNumber, c.ClientSurname, c.ClientName, i.InvoiceBalance, i.InvoiceStatus
        FROM Invoices i
        JOIN Clients c ON i.ClientId = c.ClientId";
$result = $connection->query($sql);
$invoices = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Счета</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Счета</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Номер счета</th>
                    <th>Фамилия клиента</th>
                    <th>Имя клиента</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?php echo $invoice['InvoiceId']; ?></td>
                        <td><?php echo $invoice['InvoiceNumber']; ?></td>
                        <td><?php echo $invoice['ClientSurname']; ?></td>
                        <td><?php echo $invoice['ClientName']; ?></td>
                        <td><?php echo number_format($invoice['InvoiceBalance'], 2); ?></td>
                        <td><?php echo $invoice['InvoiceStatus']; ?></td>
                        <td>
                            <a href="invoices.php?edit=<?php echo $invoice['InvoiceId']; ?>">Редактировать</a>
                            <a href="invoices.php?delete=<?php echo $invoice['InvoiceId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить эту запись?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2><?php echo $edit_state ? 'Редактировать счет' : 'Добавить счет'; ?></h2>
        <form method="post" action="invoices.php">
            <input type="hidden" name="InvoiceId" value="<?php echo $InvoiceId; ?>">
            <div class="form-group">
                <label for="InvoiceNumber">Номер счета:</label>
                <input type="text" name="InvoiceNumber" value="<?php echo $InvoiceNumber; ?>" required>
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
            <label for="InvoiceBalance">Сумма:</label>
            <input type="number" step="0.01" name="InvoiceBalance" value="<?php echo $InvoiceBalance; ?>" required>
        </div>
        <div class="form-group">
            <label for="InvoiceStatus">Статус:</label>
            <input type="text" name="InvoiceStatus" value="<?php echo $InvoiceStatus; ?>" required>
        </div>
        <div class="form-group">
            <button type="submit" name="<?php echo $edit_state ? 'update' : 'save'; ?>" class="btn"><?php echo $edit_state ? 'Обновить' : 'Сохранить'; ?></button>
        </div>
    </form>
    <a href="/admin/panel.php" class="back-button">Назад</a>
</div>
</body>
</html>
