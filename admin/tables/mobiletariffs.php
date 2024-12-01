<!-- mobiletariffs.php -->
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
$MobileId = '';
$MobileName = '';
$MobileVolumeTrafficGb = '';
$MobileVolumeMinutes = '';
$MobilePriceMinuteRub = '';
$MobileRoamingPriceMinuteRub = '';
$MobileVolumeSMS = '';
$MobileDisclaimer = '';
$MobileSector = '';
$MobilePriceRub = '';
$edit_state = false;

// Обработка POST-запроса для добавления/редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $MobileName = $_POST['MobileName'];
    $MobileVolumeTrafficGb = $_POST['MobileVolumeTrafficGb'];
    $MobileVolumeMinutes = $_POST['MobileVolumeMinutes'];
    $MobilePriceMinuteRub = $_POST['MobilePriceMinuteRub'];
    $MobileRoamingPriceMinuteRub = $_POST['MobileRoamingPriceMinuteRub'];
    $MobileVolumeSMS = $_POST['MobileVolumeSMS'];
    $MobileDisclaimer = $_POST['MobileDisclaimer'];
    $MobileSector = $_POST['MobileSector'];
    $MobilePriceRub = $_POST['MobilePriceRub'];

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO MobileTariffs (MobileName, MobileVolumeTrafficGb, MobileVolumeMinutes, MobilePriceMinuteRub, MobileRoamingPriceMinuteRub, MobileVolumeSMS, MobileDisclaimer, MobileSector, MobilePriceRub) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("siiiddssd", $MobileName, $MobileVolumeTrafficGb, $MobileVolumeMinutes, $MobilePriceMinuteRub, $MobileRoamingPriceMinuteRub, $MobileVolumeSMS, $MobileDisclaimer, $MobileSector, $MobilePriceRub);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $MobileId = $_POST['MobileId'];
        $sql = "UPDATE MobileTariffs SET MobileName=?, MobileVolumeTrafficGb=?, MobileVolumeMinutes=?, MobilePriceMinuteRub=?, MobileRoamingPriceMinuteRub=?, MobileVolumeSMS=?, MobileDisclaimer=?, MobileSector=?, MobilePriceRub=? WHERE MobileId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("siiiddssdi", $MobileName, $MobileVolumeTrafficGb, $MobileVolumeMinutes, $MobilePriceMinuteRub, $MobileRoamingPriceMinuteRub, $MobileVolumeSMS, $MobileDisclaimer, $MobileSector, $MobilePriceRub, $MobileId);
        $stmt->execute();
    }

    header("Location: mobiletariffs.php");
    exit;
}

// Обработка GET-запроса для редактирования
if (isset($_GET['edit'])) {
    $MobileId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM MobileTariffs WHERE MobileId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $MobileId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tariff = $result->fetch_assoc();

    $MobileName = $tariff['MobileName'];
    $MobileVolumeTrafficGb = $tariff['MobileVolumeTrafficGb'];
    $MobileVolumeMinutes = $tariff['MobileVolumeMinutes'];
    $MobilePriceMinuteRub = $tariff['MobilePriceMinuteRub'];
    $MobileRoamingPriceMinuteRub = $tariff['MobileRoamingPriceMinuteRub'];
    $MobileVolumeSMS = $tariff['MobileVolumeSMS'];
    $MobileDisclaimer = $tariff['MobileDisclaimer'];
    $MobileSector = $tariff['MobileSector'];
    $MobilePriceRub = $tariff['MobilePriceRub'];
}

// Обработка GET-запроса для удаления
if (isset($_GET['delete'])) {
    $MobileId = $_GET['delete'];
    $sql = "DELETE FROM MobileTariffs WHERE MobileId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $MobileId);
    $stmt->execute();

    header("Location: mobiletariffs.php");
    exit;
}

// Получение данных из таблицы "MobileTariffs"
$sql = "SELECT * FROM MobileTariffs";
$result = $connection->query($sql);
$tariffs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Мобильные Тарифы</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Мобильные Тарифы</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Объём трафика (ГБ)</th>
                    <th>Минуты разговора</th>
                    <th>Цена за минуту (руб)</th>
                    <th>Цена за роуминг (руб)</th>
                    <th>Количество СМС</th>
                    <th>Примечание</th>
                    <th>Сектор</th>
                    <th>Цена (руб)</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tariffs as $tariff): ?>
                    <tr>
                        <td><?php echo $tariff['MobileId']; ?></td>
                        <td><?php echo $tariff['MobileName']; ?></td>
                        <td><?php echo $tariff['MobileVolumeTrafficGb']; ?></td>
                        <td><?php echo $tariff['MobileVolumeMinutes']; ?></td>
                        <td><?php echo $tariff['MobilePriceMinuteRub']; ?></td>
                        <td><?php echo $tariff['MobileRoamingPriceMinuteRub']; ?></td>
                        <td><?php echo $tariff['MobileVolumeSMS']; ?></td>
                        <td><?php echo $tariff['MobileDisclaimer']; ?></td>
                        <td><?php echo $tariff['MobileSector']; ?></td>
                        <td><?php echo $tariff['MobilePriceRub']; ?></td>
                        <td>
                            <a href="mobiletariffs.php?edit=<?php echo $tariff['MobileId']; ?>">Редактировать</a>
                            <a href="mobiletariffs.php?delete=<?php echo $tariff['MobileId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этот тариф?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table-buttons">
            <form method="POST" action="mobiletariffs.php">
                <input type="hidden" name="MobileId" value="<?php echo $MobileId; ?>">
                <div class="form-group">
                    <label for="MobileName">Название</label>
                    <input type="text" name="MobileName" value="<?php echo $MobileName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="MobileVolumeTrafficGb">Объём трафика (ГБ)</label>
                    <input type="number" name="MobileVolumeTrafficGb" value="<?php echo $MobileVolumeTrafficGb; ?>" required>
                </div>
                <div class="form-group">
                    <label for="MobileVolumeMinutes">Минуты разговора</label>
                    <input type="number" name="MobileVolumeMinutes" value="<?php echo $MobileVolumeMinutes; ?>" required>
                </div>
                <div class="form-group">
                    <label for="MobilePriceMinuteRub">Цена за минуту (руб)</label>
                    <input type="number" step="0.01" name="MobilePriceMinuteRub" value="<?php echo $MobilePriceMinuteRub; ?>" required>
                </div>
                <div class="form-group">
                    <label for="MobileRoamingPriceMinuteRub">Цена за роуминг (руб)</label>
                    <input type="number" step="0.01" name="MobileRoamingPriceMinuteRub" value="<?php echo $MobileRoamingPriceMinuteRub; ?>" required>
                </div>
                <div class="form-group">
                    <label for="MobileVolumeSMS">Количество СМС</label>
                    <input type="number" name="MobileVolumeSMS" value="<?php echo $MobileVolumeSMS; ?>" required>
                </div>
                <div class="form-group">
                    <label for="MobileDisclaimer">Примечание</label>
                    <input type="text" name="MobileDisclaimer" value="<?php echo $MobileDisclaimer; ?>" required>
                </div>
                <div class="form-group">
                    <label for="MobileSector">Сектор</label>
                    <input type="text" name="MobileSector" value="<?php echo $MobileSector; ?>" required>
                </div>
                <div class="form-group">
                    <label for="MobilePriceRub">Цена (руб)</label>
                    <input type="number" step="0.01" name="MobilePriceRub" value="<?php echo $MobilePriceRub; ?>" required>
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