<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

include_once '../../config/db.php';

$OrderId = '';
$ClientId = '';
$InvoiceId = '';
$OrderDate = '';
$OrderStatus = '';
$OrderAmount = '';
$EmployeeId = '';
$RequestId = '';
$edit_state = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ClientId = $_POST['ClientId'];
    $InvoiceId = $_POST['InvoiceId'];
    $OrderDate = $_POST['OrderDate'];
    $OrderStatus = $_POST['OrderStatus'];
    $OrderAmount = $_POST['OrderAmount'];
    $EmployeeId = $_POST['EmployeeId'];
    $RequestId = $_POST['RequestId'];

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO Orders (ClientId, InvoiceId, OrderDate, OrderStatus, OrderAmount, EmployeeId, RequestId) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iissdii", $ClientId, $InvoiceId, $OrderDate, $OrderStatus, $OrderAmount, $EmployeeId, $RequestId);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $OrderId = $_POST['OrderId'];
        $sql = "UPDATE Orders SET ClientId=?, InvoiceId=?, OrderDate=?, OrderStatus=?, OrderAmount=?, EmployeeId=?, RequestId=? WHERE OrderId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iissdiii", $ClientId, $InvoiceId, $OrderDate, $OrderStatus, $OrderAmount, $EmployeeId, $RequestId, $OrderId);
        $stmt->execute();
    }

    header("Location: orders.php");
    exit;
}

if (isset($_GET['edit'])) {
    $OrderId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM Orders WHERE OrderId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $OrderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    $ClientId = $order['ClientId'];
    $InvoiceId = $order['InvoiceId'];
    $OrderDate = $order['OrderDate'];
    $OrderStatus = $order['OrderStatus'];
    $OrderAmount = $order['OrderAmount'];
    $EmployeeId = $order['EmployeeId'];
    $RequestId = $order['RequestId'];
}

if (isset($_GET['delete'])) {
    $OrderId = $_GET['delete'];
    $sql = "DELETE FROM Orders WHERE OrderId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $OrderId);
    $stmt->execute();

    header("Location: orders.php");
    exit;
}

$sql = "SELECT o.OrderId, c.ClientSurname, c.ClientName, i.InvoiceId, i.InvoiceNumber, o.OrderDate, o.OrderStatus, o.OrderAmount, e.EmployeeSurname, e.EmployeeName, cr.RequestId
        FROM Orders o
        JOIN Clients c ON o.ClientId = c.ClientId
        JOIN Invoices i ON o.InvoiceId = i.InvoiceId
        JOIN Employees e ON o.EmployeeId = e.EmployeeId
        LEFT JOIN ConnectionRequests cr ON o.RequestId = cr.RequestId";
$result = $connection->query($sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Вычисление общей суммы заказа
$sql = "SELECT o.OrderId, SUM( COALESCE(it.InternetPriceRub, 0) + COALESCE(tt.TelevisionPriceRub, 0) + COALESCE(mt.MobilePriceRub, 0) + COALESCE(s.ServicePriceRub, 0) ) AS TotalOrderAmount
        FROM Orders o
        LEFT JOIN OrderItems oi ON o.OrderId = oi.OrderId
        LEFT JOIN InternetTariffs it ON oi.InternetId = it.InternetId
        LEFT JOIN TelevisionTariffs tt ON oi.TelevisionId = tt.TelevisionId
        LEFT JOIN MobileTariffs mt ON oi.MobileId = mt.MobileId
        LEFT JOIN Services s ON oi.ServiceId = s.ServiceId
        GROUP BY o.OrderId";
$result = $connection->query($sql);
$orderAmounts = array_column($result->fetch_all(MYSQLI_ASSOC), 'TotalOrderAmount', 'OrderId');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Заказы</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn-calculate-amount').forEach(button => {
                button.addEventListener('click', () => {
                    const orderId = button.dataset.orderId;
                    const orderAmountInput = document.querySelector(`input[name="OrderAmount"][data-order-id="${orderId}"]`);
                    orderAmountInput.value = <?php echo json_encode($orderAmounts); ?>[orderId];
                });
            });
        });
    </script>
</head>
<body>
    <div class="admin-panel">
        <h1>Заказы</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Фамилия клиента</th>
                    <th>Имя клиента</th>
                    <th>Номер счета</th>
                    <th>Дата заказа</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Фамилия сотрудника</th>
                    <th>Имя сотрудника</th>
                    <th>ID заявки</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['OrderId']; ?></td>
                        <td><?php echo $order['ClientSurname']; ?></td>
                        <td><?php echo $order['ClientName']; ?></td>
                        <td><a href="invoices.php?id=<?php echo $order['InvoiceId']; ?>"><?php echo $order['InvoiceNumber']; ?></a></td>
                        <td><?php echo $order['OrderDate']; ?></td>
                        <td><?php echo $order['OrderStatus']; ?></td>
                        <td><?php echo number_format($orderAmounts[$order['OrderId']] ?? 0, 2); ?></td>
                        <td><?php echo $order['EmployeeSurname']; ?></td>
                        <td><?php echo $order['EmployeeName']; ?></td>
                        <td><a href="connectionrequests.php?id=<?php echo $order['RequestId']; ?>"><?php echo $order['RequestId']; ?></a></td>
                        <td> <a href="orders.php?edit=<?php echo $order['OrderId']; ?>">Редактировать</a>
                            <a href="orders.php?delete=<?php echo $order['OrderId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить эту запись?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="post" action="orders.php">
            <input type="hidden" name="OrderId" value="<?php echo $OrderId; ?>">
            <div class="form-group">
                <label for="ClientId">Клиент</label>
                <select name="ClientId" required>
                    <option value="">Выберите клиента</option>
                    <?php
                    $clients_result = $connection->query("SELECT ClientId, ClientSurname, ClientName FROM Clients");
                    while ($client = $clients_result->fetch_assoc()): ?>
                        <option value="<?php echo $client['ClientId']; ?>" <?php echo $ClientId == $client['ClientId'] ? 'selected' : ''; ?>>
                            <?php echo $client['ClientSurname'] . ' ' . $client['ClientName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="InvoiceId">Счет</label>
                <select name="InvoiceId" required>
                    <option value="">Выберите счет</option>
                    <?php
                    $invoices_result = $connection->query("SELECT InvoiceId, InvoiceNumber FROM Invoices");
                    while ($invoice = $invoices_result->fetch_assoc()): ?>
                        <option value="<?php echo $invoice['InvoiceId']; ?>" <?php echo $InvoiceId == $invoice['InvoiceId'] ? 'selected' : ''; ?>>
                            <?php echo $invoice['InvoiceNumber']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="OrderDate">Дата заказа</label>
                <input type="datetime-local" name="OrderDate" value="<?php echo $OrderDate; ?>" required>
            </div>
            <div class="form-group">
                <label for="OrderStatus">Статус заказа</label>
                <input type="text" name="OrderStatus" value="<?php echo $OrderStatus; ?>" required>
            </div>
            <div class="form-group">
                <label for="OrderAmount">Сумма заказа</label>
                <input type="number" step="0.01" name="OrderAmount" data-order-id="<?php echo $OrderId; ?>" value="<?php echo $orderAmounts[$OrderId] ?? $OrderAmount; ?>" required>
                <button type="button" class="btn-calculate-amount" data-order-id="<?php echo $OrderId; ?>">Рассчитать сумму</button>
            </div>
            <div class="form-group">
                <label for="EmployeeId">Сотрудник</label>
                <select name="EmployeeId" required>
                    <option value="">Выберите сотрудника</option>
                    <?php
                    $employees_result = $connection->query("SELECT EmployeeId, EmployeeSurname, EmployeeName FROM Employees");
                    while ($employee = $employees_result->fetch_assoc()): ?>
                        <option value="<?php echo $employee['EmployeeId']; ?>" <?php echo $EmployeeId == $employee['EmployeeId'] ? 'selected' : ''; ?>>
                            <?php echo $employee['EmployeeSurname'] . ' ' . $employee['EmployeeName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="RequestId">Заявка</label>
                <select name="RequestId">
                    <option value="">Выберите заявку</option>
                    <?php
                    $requests_result = $connection->query("SELECT RequestId FROM ConnectionRequests");
                    while ($request = $requests_result->fetch_assoc()): ?>
                        <option value="<?php echo $request['RequestId']; ?>" <?php echo $RequestId == $request['RequestId'] ? 'selected' : ''; ?>>
                            <?php echo $request['RequestId']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="<?php echo $edit_state ? 'update' : 'save'; ?>" class="btn">
                    <?php echo $edit_state ? 'Обновить' : 'Сохранить'; ?>
                </button>
            </div>
        </form>
        <a href="/admin/panel.php" class="back-button">Назад</a>
    </div>
</body>
</html>