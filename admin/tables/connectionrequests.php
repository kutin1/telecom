<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

include_once '../../config/db.php';

$RequestId = '';
$RequestName = '';
$RequestPhone = '';
$RequestAddress = '';
$Services = '';
$RequestStatus = '';
$EmployeeId = '';
$OrderId = '';
$edit_state = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $RequestName = $_POST['RequestName'];
    $RequestPhone = $_POST['RequestPhone'];
    $RequestAddress = $_POST['RequestAddress'];
    $Services = $_POST['Services'];
    $RequestStatus = $_POST['RequestStatus'];
    $EmployeeId = $_POST['EmployeeId'] ?: NULL;
    $OrderId = $_POST['OrderId'] ?: NULL;

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO ConnectionRequests (RequestName, RequestPhone, RequestAddress, Services, RequestStatus, EmployeeId, OrderId) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssssi", $RequestName, $RequestPhone, $RequestAddress, $Services, $RequestStatus, $EmployeeId, $OrderId);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $RequestId = $_POST['RequestId'];
        $sql = "UPDATE ConnectionRequests SET RequestName=?, RequestPhone=?, RequestAddress=?, Services=?, RequestStatus=?, EmployeeId=?, OrderId=? WHERE RequestId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssssii", $RequestName, $RequestPhone, $RequestAddress, $Services, $RequestStatus, $EmployeeId, $OrderId, $RequestId);
        $stmt->execute();
    }

    header("Location: connectionrequests.php");
    exit;
}

if (isset($_GET['edit'])) {
    $RequestId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT RequestName, RequestPhone, RequestAddress, Services, RequestStatus, EmployeeId, OrderId FROM ConnectionRequests WHERE RequestId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $RequestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    $RequestName = $request['RequestName'];
    $RequestPhone = $request['RequestPhone'];
    $RequestAddress = $request['RequestAddress'];
    $Services = $request['Services'];
    $RequestStatus = $request['RequestStatus'];
    $EmployeeId = $request['EmployeeId'];
    $OrderId = $request['OrderId'];
}

if (isset($_GET['delete'])) {
    $RequestId = $_GET['delete'];
    $sql = "DELETE FROM ConnectionRequests WHERE RequestId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $RequestId);
    $stmt->execute();

    header("Location: connectionrequests.php");
    exit;
}

$sql = "SELECT cr.*, e.EmployeeSurname FROM ConnectionRequests cr 
        LEFT JOIN Employees e ON cr.EmployeeId = e.EmployeeId";
$result = $connection->query($sql);
$requests = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Запросы на Подключение</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Запросы на Подключение</h1>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Адрес</th>
                    <th>Услуги</th>
                    <th>Дата Запроса</th>
                    <th>Статус</th>
                    <th>Сотрудник</th>
                    <th>Заказ</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo $request['RequestId']; ?></td>
                        <td><?php echo $request['RequestName']; ?></td>
                        <td><?php echo $request['RequestPhone']; ?></td>
                        <td><?php echo $request['RequestAddress']; ?></td>
                        <td><?php echo $request['Services']; ?></td>
                        <td><?php echo $request['RequestDate']; ?></td>
                        <td><?php echo $request['RequestStatus']; ?></td>
                        <td><?php echo $request['EmployeeSurname']; ?></td>
                        <td>
                            <a href="orders.php?orderId=<?php echo $request['OrderId']; ?>"><?php echo $request['OrderId']; ?></a>
                        </td>
                        <td>
                            <a href="connectionrequests.php?edit=<?php echo $request['RequestId']; ?>">Редактировать</a>
                            <a href="connectionrequests.php?delete=<?php echo $request['RequestId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этот запрос?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2><?php echo $edit_state ? 'Редактировать Запрос' : 'Добавить Запрос'; ?></h2>
        <form method="post" action="connectionrequests.php">
            <input type="hidden" name="RequestId" value="<?php echo $RequestId; ?>">
            <div class="form-group">
                <label for="RequestName">Имя:</label>
                <input type="text" name="RequestName" value="<?php echo $RequestName; ?>" required>
            </div>
            <div class="form-group">
                <label for="RequestPhone">Телефон:</label>
                <input type="text" name="RequestPhone" value="<?php echo $RequestPhone; ?>" required>
            </div>
            <div class="form-group">
                <label for="RequestAddress">Адрес:</label>
                <input type="text" name="RequestAddress" value="<?php echo $RequestAddress; ?>" required>
            </div>
            <div class="form-group">
                <label for="Services">Услуги:</label>
                <input type="text" name="Services" value="<?php echo $Services; ?>" required>
            </div>
            <div class="form-group">
                <label for="RequestStatus">Статус:</label>
                <input type="text" name="RequestStatus" value="<?php echo $RequestStatus; ?>">
            </div>
            <div class="form-group">
                <label for="EmployeeId">Сотрудник:</label>
                <select name="EmployeeId">
                    <option value="">Выберите сотрудника</option>
                    <?php
                    $employees = $connection->query("SELECT EmployeeId, EmployeeSurname FROM Employees");
                    while ($employee = $employees->fetch_assoc()): ?>
                        <option value="<?php echo $employee['EmployeeId']; ?>" <?php echo $EmployeeId == $employee['EmployeeId'] ? 'selected' : ''; ?>>
                            <?php echo $employee['EmployeeSurname']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="OrderId">Заказ:</label>
                <select name="OrderId">
                    <option value="">Выберите заказ</option>
                    <?php
                    $orders = $connection->query("SELECT OrderId FROM Orders");
                    while ($order = $orders->fetch_assoc()): ?>
                        <option value="<?php echo $order['OrderId']; ?>" <?php echo $OrderId == $order['OrderId'] ? 'selected' : ''; ?>>
                            <?php echo $order['OrderId']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="<?php echo $edit_state ? 'update' : 'save'; ?>">
                    <?php echo $edit_state ? 'Обновить' : 'Сохранить'; ?>
                </button>
            </div>
        </form>
        <a href="/admin/panel.php" class="back-button">Назад</a>
    </div>
</body>
</html>
