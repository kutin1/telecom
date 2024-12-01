<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

include_once '../../config/db.php';

$OrderItemId = '';
$OrderId = '';
$InternetId = null;
$TelevisionId = null;
$MobileId = null;
$ServiceId = null;
$edit_state = false;
$selectedOrder = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['OrderId'])) $OrderId = $_POST['OrderId'];
    if (isset($_POST['InternetId'])) $InternetId = $_POST['InternetId'] !== '' ? $_POST['InternetId'] : null;
    if (isset($_POST['TelevisionId'])) $TelevisionId = $_POST['TelevisionId'] !== '' ? $_POST['TelevisionId'] : null;
    if (isset($_POST['MobileId'])) $MobileId = $_POST['MobileId'] !== '' ? $_POST['MobileId'] : null;
    if (isset($_POST['ServiceId'])) $ServiceId = $_POST['ServiceId'] !== '' ? $_POST['ServiceId'] : null;

    // Проверка наличия хотя бы одного выбранного тарифа или услуги
    if ($InternetId !== null || $TelevisionId !== null || $MobileId !== null || $ServiceId !== null) {
        if (isset($_POST['save'])) {
            $sql = "INSERT INTO OrderItems (OrderId, InternetId, TelevisionId, MobileId, ServiceId) VALUES (?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("iiiii", $OrderId, $InternetId, $TelevisionId, $MobileId, $ServiceId);
            $stmt->execute();
        } elseif (isset($_POST['update'])) {
            $OrderItemId = $_POST['OrderItemId'];
            $sql = "UPDATE OrderItems SET OrderId=?, InternetId=?, TelevisionId=?, MobileId=?, ServiceId=? WHERE OrderItemId=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("iiiiii", $OrderId, $InternetId, $TelevisionId, $MobileId, $ServiceId, $OrderItemId);
            $stmt->execute();
        }
    }

    if (!isset($_POST['view_order'])) {
        header("Location: orderitems.php");
        exit;
    }
}

if (isset($_GET['edit'])) {
    $OrderItemId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM OrderItems WHERE OrderItemId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $OrderItemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderitem = $result->fetch_assoc();

    $OrderId = $orderitem['OrderId'];
    $InternetId = $orderitem['InternetId'];
    $TelevisionId = $orderitem['TelevisionId'];
    $MobileId = $orderitem['MobileId'];
    $ServiceId = $orderitem['ServiceId'];
}

if (isset($_GET['delete'])) {
    $OrderItemId = $_GET['delete'];
    $sql = "DELETE FROM OrderItems WHERE OrderItemId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $OrderItemId);
    $stmt->execute();

    header("Location: orderitems.php");
    exit;
}

$orderItemsQuery = "SELECT oi.OrderItemId, o.OrderId, it.InternetName AS InternetTariff, tt.TelevisionName AS TelevisionTariff, mt.MobileName AS MobileTariff, s.ServiceName
                    FROM OrderItems oi
                    JOIN Orders o ON oi.OrderId = o.OrderId
                    LEFT JOIN InternetTariffs it ON oi.InternetId = it.InternetId
                    LEFT JOIN TelevisionTariffs tt ON oi.TelevisionId = tt.TelevisionId
                    LEFT JOIN MobileTariffs mt ON oi.MobileId = mt.MobileId
                    LEFT JOIN Services s ON oi.ServiceId = s.ServiceId";
if (isset($_POST['view_order'])) {
    $selectedOrder = $_POST['order_id'];
    if ($selectedOrder) {
        $orderItemsQuery .= " WHERE oi.OrderId = ?";
        $stmt = $connection->prepare($orderItemsQuery);
        $stmt->bind_param("i", $selectedOrder);
        $stmt->execute();
        $result = $stmt->get_result();
        $orderitems = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $result = $connection->query($orderItemsQuery);
        $orderitems = $result->fetch_all(MYSQLI_ASSOC);
    }
} else {
    $result = $connection->query($orderItemsQuery);
    $orderitems = $result->fetch_all(MYSQLI_ASSOC);
}

$ordersQuery = "SELECT OrderId FROM Orders";
$ordersResult = $connection->query($ordersQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Состав заказа</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Состав заказа</h1>

        <form method="post" action="orderitems.php">
            <label for="order_id">Выберите заказ:</label>
            <select name="order_id" id="order_id">
                <option value="">Все заказы</option>
                <?php while ($order = $ordersResult->fetch_assoc()): ?>
                    <option value="<?php echo $order['OrderId']; ?>" <?php echo $selectedOrder == $order['OrderId'] ? 'selected' : ''; ?>>
                        <?php echo $order['OrderId']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="view_order">Просмотр заказа</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>ID элемента заказа</th>
                    <th>Заказ</th>
                    <th>Интернет тариф</th>
                    <th>Телевизионный тариф</th>
                    <th>Мобильный тариф</th>
                    <th>Сервис</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderitems as $orderitem): ?>
                    <tr>
                        <td><?php echo $orderitem['OrderItemId']; ?></td>
                        <td><a href="orders.php?order_id=<?php echo $orderitem['OrderId']; ?>"><?php echo $orderitem['OrderId']; ?></a></td>
                        <td><?php echo $orderitem['InternetTariff'] ?? 'Не указано'; ?></td>
                        <td><?php echo $orderitem['TelevisionTariff'] ?? 'Не указано'; ?></td>
                        <td><?php echo $orderitem['MobileTariff'] ?? 'Не указано'; ?></td>
                        <td><?php echo $orderitem['ServiceName'] ?? 'Не указано'; ?></td>
                        <td>
                            <a href="orderitems.php?edit=<?php echo $orderitem['OrderItemId']; ?>">Редактировать</a>
                            <a href="orderitems.php?delete=<?php echo $orderitem['OrderItemId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этот элемент заказа?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2><?php echo $edit_state ? 'Редактировать элемент заказа' : 'Добавить элемент заказа'; ?></h2>
        <form method="post" action="orderitems.php">
            <input type="hidden" name="OrderItemId" value="<?php echo $OrderItemId; ?>">
            <div class="form-group">
                <label for="OrderId">Заказ</label>
                <select name="OrderId" required>
                    <option value="">выбрать заказ</option>
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
                <label for="InternetId">Интернет тариф</label>
                <select name="InternetId">
                    <option value="">выбрать интернет тариф</option>
                    <?php
                    $internetTariffs = $connection->query("SELECT InternetId, InternetName FROM InternetTariffs");
                    while ($internet = $internetTariffs->fetch_assoc()): ?>
                        <option value="<?php echo $internet['InternetId']; ?>" <?php echo $InternetId == $internet['InternetId'] ? 'selected' : ''; ?>>
                            <?php echo $internet['InternetName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="TelevisionId">Телевизионный тариф</label>
                <select name="TelevisionId">
                    <option value="">выбрать телевизионный тариф</option>
                    <?php
                    $televisionTariffs = $connection->query("SELECT TelevisionId, TelevisionName FROM TelevisionTariffs");
                    while ($television = $televisionTariffs->fetch_assoc()): ?>
                        <option value="<?php echo $television['TelevisionId']; ?>" <?php echo $TelevisionId == $television['TelevisionId'] ? 'selected' : ''; ?>>
                            <?php echo $television['TelevisionName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="MobileId">Мобильный тариф</label>
                <select name="MobileId">
                    <option value="">выбрать мобильный тариф</option>
                    <?php
                    $mobileTariffs = $connection->query("SELECT MobileId, MobileName FROM MobileTariffs");
                    while ($mobile = $mobileTariffs->fetch_assoc()): ?>
                        <option value="<?php echo $mobile['MobileId']; ?>" <?php echo $MobileId == $mobile['MobileId'] ? 'selected' : ''; ?>>
                            <?php echo $mobile['MobileName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ServiceId">Сервис</label>
                <select name="ServiceId">
                    <option value="">выбрать услугу</option>
                    <?php
                    $services = $connection->query("SELECT ServiceId, ServiceName FROM Services");
                    while ($service = $services->fetch_assoc()): ?>
                        <option value="<?php echo $service['ServiceId']; ?>" <?php echo $ServiceId == $service['ServiceId'] ? 'selected' : ''; ?>>
                            <?php echo $service['ServiceName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <?php if ($edit_state): ?>
                    <button type="submit" name="update">Обновить</button>
                <?php else: ?>
                    <button type="submit" name="save">Сохранить</button>
                <?php endif; ?>
            </div>
        </form>
        <a href="/admin/panel.php" class="back-button">Назад</a>
    </div>
</body>
</html>