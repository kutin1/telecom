<!-- televisiontariffs.php -->
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
$TelevisionId = '';
$TelevisionName = '';
$TelevisionChannelQuantity = '';
$TelevisionTechnology = '';
$TelevisionDisclaimer = '';
$TelevisionSector = '';
$TelevisionPriceRub = '';
$edit_state = false;

// Обработка POST-запроса для добавления/редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $TelevisionName = $_POST['TelevisionName'];
    $TelevisionChannelQuantity = $_POST['TelevisionChannelQuantity'];
    $TelevisionTechnology = $_POST['TelevisionTechnology'];
    $TelevisionDisclaimer = $_POST['TelevisionDisclaimer'];
    $TelevisionSector = $_POST['TelevisionSector'];
    $TelevisionPriceRub = $_POST['TelevisionPriceRub'];

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO TelevisionTariffs (TelevisionName, TelevisionChannelQuantity, TelevisionTechnology, TelevisionDisclaimer, TelevisionSector, TelevisionPriceRub) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sisssd", $TelevisionName, $TelevisionChannelQuantity, $TelevisionTechnology, $TelevisionDisclaimer, $TelevisionSector, $TelevisionPriceRub);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $TelevisionId = $_POST['TelevisionId'];
        $sql = "UPDATE TelevisionTariffs SET TelevisionName=?, TelevisionChannelQuantity=?, TelevisionTechnology=?, TelevisionDisclaimer=?, TelevisionSector=?, TelevisionPriceRub=? WHERE TelevisionId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sisssdi", $TelevisionName, $TelevisionChannelQuantity, $TelevisionTechnology, $TelevisionDisclaimer, $TelevisionSector, $TelevisionPriceRub, $TelevisionId);
        $stmt->execute();
    }

    header("Location: televisiontariffs.php");
    exit;
}

// Обработка GET-запроса для редактирования
if (isset($_GET['edit'])) {
    $TelevisionId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM TelevisionTariffs WHERE TelevisionId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $TelevisionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tariff = $result->fetch_assoc();

    $TelevisionName = $tariff['TelevisionName'];
    $TelevisionChannelQuantity = $tariff['TelevisionChannelQuantity'];
    $TelevisionTechnology = $tariff['TelevisionTechnology'];
    $TelevisionDisclaimer = $tariff['TelevisionDisclaimer'];
    $TelevisionSector = $tariff['TelevisionSector'];
    $TelevisionPriceRub = $tariff['TelevisionPriceRub'];
}

// Обработка GET-запроса для удаления
if (isset($_GET['delete'])) {
    $TelevisionId = $_GET['delete'];
    $sql = "DELETE FROM TelevisionTariffs WHERE TelevisionId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $TelevisionId);
    $stmt->execute();

    header("Location: televisiontariffs.php");
    exit;
}

// Получение данных из таблицы "TelevisionTariffs"
$sql = "SELECT * FROM TelevisionTariffs";
$result = $connection->query($sql);
$tariffs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Тарифы Телевидение</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Тарифы Телевидение</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Количество каналов</th>
                    <th>Технология</th>
                    <th>Примечание</th>
                    <th>Сектор</th>
                    <th>Цена (руб)</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tariffs as $tariff): ?>
                    <tr>
                        <td><?php echo $tariff['TelevisionId']; ?></td>
                        <td><?php echo $tariff['TelevisionName']; ?></td>
                        <td><?php echo $tariff['TelevisionChannelQuantity']; ?></td>
                        <td><?php echo $tariff['TelevisionTechnology']; ?></td>
                        <td><?php echo $tariff['TelevisionDisclaimer']; ?></td>
                        <td><?php echo $tariff['TelevisionSector']; ?></td>
                        <td><?php echo $tariff['TelevisionPriceRub']; ?></td>
                        <td>
                            <a href="televisiontariffs.php?edit=<?php echo $tariff['TelevisionId']; ?>">Редактировать</a>
                            <a href="televisiontariffs.php?delete=<?php echo $tariff['TelevisionId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этот тариф?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table-buttons">
            <form method="POST" action="televisiontariffs.php">
                <input type="hidden" name="TelevisionId" value="<?php echo $TelevisionId; ?>">
                <div class="form-group">
                    <label for="TelevisionName">Название</label>
                    <input type="text" name="TelevisionName" value="<?php echo $TelevisionName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="TelevisionChannelQuantity">Количество каналов</label>
                    <input type="number" name="TelevisionChannelQuantity" value="<?php echo $TelevisionChannelQuantity; ?>" required>
                </div>
                <div class="form-group">
                    <label for="TelevisionTechnology">Технология</label>
                    <input type="text" name="TelevisionTechnology" value="<?php echo $TelevisionTechnology; ?>" required>
                </div>
                <div class="form-group">
                    <label for="TelevisionDisclaimer">Примечание</label>
                    <input type="text" name="TelevisionDisclaimer" value="<?php echo $TelevisionDisclaimer; ?>" required>
                </div>
                <div class="form-group">
                    <label for="TelevisionSector">Сектор</label>
                    <input type="text" name="TelevisionSector" value="<?php echo $TelevisionSector; ?>" required>
                </div>
                <div class="form-group">
                    <label for="TelevisionPriceRub">Цена (руб)</label>
                    <input type="number" step="0.01" name="TelevisionPriceRub" value="<?php echo $TelevisionPriceRub; ?>" required>
                </div>
                <div class="form-group">
                    <?php if ($edit_state): ?>
                        <button type="submit" name="update">Обновить</button>
                    <?php else: ?>
                        <button type="submit" name="save">Сохранить</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <a href="/admin/panel.php" class="back-button">Назад</a>
    </div>
</body>
</html>