<!-- services.php -->
<?php
// Проверка авторизации
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

// Подключение к базе данных
include_once '../../config/db.php';

// Инициализация переменных
$ServiceId = '';
$ServiceName = '';
$ServiceType = '';
$ServiceFunctions = '';
$ServiceDisclaimer = '';
$ServicePriceRub = '';
$edit_state = false;

// Обработка POST-запроса для добавления/редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ServiceName = $_POST['ServiceName'];
    $ServiceType = $_POST['ServiceType'];
    $ServiceFunctions = $_POST['ServiceFunctions'];
    $ServiceDisclaimer = $_POST['ServiceDisclaimer'];
    $ServicePriceRub = $_POST['ServicePriceRub'];

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO Services (ServiceName, ServiceType, ServiceFunctions, ServiceDisclaimer, ServicePriceRub) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssd", $ServiceName, $ServiceType, $ServiceFunctions, $ServiceDisclaimer, $ServicePriceRub);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $ServiceId = $_POST['ServiceId'];
        $sql = "UPDATE Services SET ServiceName=?, ServiceType=?, ServiceFunctions=?, ServiceDisclaimer=?, ServicePriceRub=? WHERE ServiceId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssdi", $ServiceName, $ServiceType, $ServiceFunctions, $ServiceDisclaimer, $ServicePriceRub, $ServiceId);
        $stmt->execute();
    }

    header("Location: services.php");
    exit;
}

// Обработка GET-запроса для редактирования
if (isset($_GET['edit'])) {
    $ServiceId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM Services WHERE ServiceId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $ServiceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();

    $ServiceName = $service['ServiceName'];
    $ServiceType = $service['ServiceType'];
    $ServiceFunctions = $service['ServiceFunctions'];
    $ServiceDisclaimer = $service['ServiceDisclaimer'];
    $ServicePriceRub = $service['ServicePriceRub'];
}

// Обработка GET-запроса для удаления
if (isset($_GET['delete'])) {
    $ServiceId = $_GET['delete'];
    $sql = "DELETE FROM Services WHERE ServiceId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $ServiceId);
    $stmt->execute();

    header("Location: services.php");
    exit;
}

// Получение данных из таблицы "Services"
$sql = "SELECT * FROM Services";
$result = $connection->query($sql);
$services = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Услуги</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Услуги</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Функции</th>
                    <th>Примечание</th>
                    <th>Цена (руб)</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo $service['ServiceId']; ?></td>
                        <td><?php echo $service['ServiceName']; ?></td>
                        <td><?php echo $service['ServiceType']; ?></td>
                        <td><?php echo $service['ServiceFunctions']; ?></td>
                        <td><?php echo $service['ServiceDisclaimer']; ?></td>
                        <td><?php echo $service['ServicePriceRub']; ?></td>
                        <td>
                            <a href="services.php?edit=<?php echo $service['ServiceId']; ?>">Редактировать</a>
                            <a href="services.php?delete=<?php echo $service['ServiceId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить эту услугу?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table-buttons">
            <form method="POST" action="services.php">
                <input type="hidden" name="ServiceId" value="<?php echo $ServiceId; ?>">
                <div class="form-group">
                    <label for="ServiceName">Название</label>
                    <input type="text" name="ServiceName" value="<?php echo $ServiceName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="ServiceType">Тип</label>
                    <input type="text" name="ServiceType" value="<?php echo $ServiceType; ?>" required>
                </div>
                <div class="form-group">
                    <label for="ServiceFunctions">Функции</label>
                    <input type="text" name="ServiceFunctions" value="<?php echo $ServiceFunctions; ?>" required>
                </div>
                <div class="form-group">
                    <label for="ServiceDisclaimer">Примечание</label>
                    <input type="text" name="ServiceDisclaimer" value="<?php echo $ServiceDisclaimer; ?>" required>
                </div>
                <div class="form-group">
                    <label for="ServicePriceRub">Цена (руб)</label>
                    <input type="number" step="0.01" name="ServicePriceRub" value="<?php echo $ServicePriceRub; ?>" required>
                </div>
                <div class="form-group">
                    <?php if ($edit_state): ?>
                        <button type="submit" name="update" class="btn">Обновить</button>
                    <?php else: ?>
                        <button type="submit" name="save" class="btn">Сохранить</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <a href="/admin/panel.php" class="back-button">Назад</a>
    </div>
</body>
</html>
