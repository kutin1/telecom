<!-- internettariffs.php -->
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
$InternetId = '';
$InternetName = '';
$InternetSpeedMbit = '';
$InternetTechnology = '';
$InternetVolumeGb = '';
$InternetDisclaimer = '';
$InternetSector = '';
$InternetPriceRub = '';
$edit_state = false;

// Обработка POST-запроса для добавления/редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $InternetName = $_POST['InternetName'];
    $InternetSpeedMbit = $_POST['InternetSpeedMbit'];
    $InternetTechnology = $_POST['InternetTechnology'];
    $InternetVolumeGb = $_POST['InternetVolumeGb'];
    $InternetDisclaimer = $_POST['InternetDisclaimer'];
    $InternetSector = $_POST['InternetSector'];
    $InternetPriceRub = $_POST['InternetPriceRub'];

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO InternetTariffs (InternetName, InternetSpeedMbit, InternetTechnology, InternetVolumeGb, InternetDisclaimer, InternetSector, InternetPriceRub) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sissssd", $InternetName, $InternetSpeedMbit, $InternetTechnology, $InternetVolumeGb, $InternetDisclaimer, $InternetSector, $InternetPriceRub);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $InternetId = $_POST['InternetId'];
        $sql = "UPDATE InternetTariffs SET InternetName=?, InternetSpeedMbit=?, InternetTechnology=?, InternetVolumeGb=?, InternetDisclaimer=?, InternetSector=?, InternetPriceRub=? WHERE InternetId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sissssdi", $InternetName, $InternetSpeedMbit, $InternetTechnology, $InternetVolumeGb, $InternetDisclaimer, $InternetSector, $InternetPriceRub, $InternetId);
        $stmt->execute();
    }

    header("Location: internettariffs.php");
    exit;
}

// Обработка GET-запроса для редактирования
if (isset($_GET['edit'])) {
    $InternetId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM InternetTariffs WHERE InternetId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $InternetId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tariff = $result->fetch_assoc();

    $InternetName = $tariff['InternetName'];
    $InternetSpeedMbit = $tariff['InternetSpeedMbit'];
    $InternetTechnology = $tariff['InternetTechnology'];
    $InternetVolumeGb = $tariff['InternetVolumeGb'];
    $InternetDisclaimer = $tariff['InternetDisclaimer'];
    $InternetSector = $tariff['InternetSector'];
    $InternetPriceRub = $tariff['InternetPriceRub'];
}

// Обработка GET-запроса для удаления
if (isset($_GET['delete'])) {
    $InternetId = $_GET['delete'];
    $sql = "DELETE FROM InternetTariffs WHERE InternetId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $InternetId);
    $stmt->execute();

    header("Location: internettariffs.php");
    exit;
}

// Получение данных из таблицы "InternetTariffs"
$sql = "SELECT * FROM InternetTariffs";
$result = $connection->query($sql);
$tariffs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Тарифы Интернет</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Тарифы Интернет</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Скорость (Мбит/с)</th>
                    <th>Технология</th>
                    <th>Объём (ГБ)</th>
                    <th>Примечание</th>
                    <th>Сектор</th>
                    <th>Цена (руб)</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tariffs as $tariff): ?>
                    <tr>
                        <td><?php echo $tariff['InternetId']; ?></td>
                        <td><?php echo $tariff['InternetName']; ?></td>
                        <td><?php echo $tariff['InternetSpeedMbit']; ?></td>
                        <td><?php echo $tariff['InternetTechnology']; ?></td>
                        <td><?php echo $tariff['InternetVolumeGb']; ?></td>
                        <td><?php echo $tariff['InternetDisclaimer']; ?></td>
                        <td><?php echo $tariff['InternetSector']; ?></td>
                        <td><?php echo $tariff['InternetPriceRub']; ?></td>
                        <td>
                            <a href="internettariffs.php?edit=<?php echo $tariff['InternetId']; ?>">Редактировать</a>
                            <a href="internettariffs.php?delete=<?php echo $tariff['InternetId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этот тариф?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table-buttons">
            <form method="POST" action="internettariffs.php">
                <input type="hidden" name="InternetId" value="<?php echo $InternetId; ?>">
                <div class="form-group">
                    <label for="InternetName">Название</label>
                    <input type="text" name="InternetName" value="<?php echo $InternetName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="InternetSpeedMbit">Скорость (Мбит/с)</label>
                    <input type="number" name="InternetSpeedMbit" value="<?php echo $InternetSpeedMbit; ?>" required>
                </div>
                <div class="form-group">
                    <label for="InternetTechnology">Технология</label>
                    <input type="text" name="InternetTechnology" value="<?php echo $InternetTechnology; ?>" required>
                </div>
                <div class="form-group">
                    <label for="InternetVolumeGb">Объём (ГБ)</label>
                    <input type="number" name="InternetVolumeGb" value="<?php echo $InternetVolumeGb; ?>" required>
                </div>
                <div class="form-group">
                    <label for="InternetDisclaimer">Примечание</label>
                    <input type="text" name="InternetDisclaimer" value="<?php echo $InternetDisclaimer; ?>" required>
                </div>
                <div class="form-group">
                    <label for="InternetSector">Сектор</label>
                    <input type="text" name="InternetSector" value="<?php echo $InternetSector; ?>" required>
                </div>
                <div class="form-group">
                    <label for="InternetPriceRub">Цена (руб)</label>
                    <input type="number" step="0.01" name="InternetPriceRub" value="<?php echo $InternetPriceRub; ?>" required>
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