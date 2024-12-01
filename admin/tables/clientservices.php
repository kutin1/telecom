<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

include_once '../../config/db.php';

$ClientServiceId = '';
$ClientId = '';
$ServiceId = '';
$edit_state = false;
$selectedClient = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Проверяем существование ключей в массиве $_POST
    $ClientId = isset($_POST['ClientId']) ? $_POST['ClientId'] : '';
    $ServiceId = isset($_POST['ServiceId']) ? $_POST['ServiceId'] : '';

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO ClientServices (ClientId, ServiceId) VALUES (?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $ClientId, $ServiceId);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $ClientServiceId = $_POST['ClientServiceId'];
        $sql = "UPDATE ClientServices SET ClientId=?, ServiceId=? WHERE ClientServiceId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iii", $ClientId, $ServiceId, $ClientServiceId);
        $stmt->execute();
    } elseif (isset($_POST['view_services'])) {
        $selectedClient = $_POST['client_id'];
    }

    if (!isset($_POST['view_services'])) {
        header("Location: clientservices.php");
        exit;
    }
}

if (isset($_GET['edit'])) {
    $ClientServiceId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM ClientServices WHERE ClientServiceId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $ClientServiceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $clientservice = $result->fetch_assoc();

    $ClientId = $clientservice['ClientId'];
    $ServiceId = $clientservice['ServiceId'];
}

if (isset($_GET['delete'])) {
    $ClientServiceId = $_GET['delete'];
    $sql = "DELETE FROM ClientServices WHERE ClientServiceId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $ClientServiceId);
    $stmt->execute();

    header("Location: clientservices.php");
    exit;
}

$sql = "SELECT cs.ClientServiceId, c.ClientSurname, c.ClientName, c.ClientPatr, s.ServiceName
        FROM ClientServices cs
        JOIN Clients c ON cs.ClientId = c.ClientId
        JOIN Services s ON cs.ServiceId = s.ServiceId";
$result = $connection->query($sql);
$clientservices = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Услуги клиентов</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Услуги клиентов</h1>
        <h2>Выберите клиента для просмотра услуг</h2>
        <form method="post">
            <label for="client_id">Клиент:</label>
            <select name="client_id" id="client_id">
                <?php
                $clientsQuery = "SELECT ClientId, ClientSurname, ClientName, ClientPatr FROM Clients";
                $clientsResult = $connection->query($clientsQuery);
                while ($client = $clientsResult->fetch_assoc()) {
                    echo "<option value='{$client['ClientId']}'" . ($selectedClient == $client['ClientId'] ? ' selected' : '') . ">{$client['ClientSurname']} {$client['ClientName']} {$client['ClientPatr']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="view_services">Просмотр услуг</button>
        </form>

        <?php if ($selectedClient): ?>
    <?php
    $query = "SELECT cs.ClientServiceId, c.ClientSurname, c.ClientName, c.ClientPatr, s.ServiceName
              FROM ClientServices cs
              JOIN Clients c ON cs.ClientId = c.ClientId
              JOIN Services s ON cs.ServiceId = s.ServiceId
              WHERE cs.ClientId = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $selectedClient);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Услуги выбранного клиента</h2>";
        echo "<table class='table'>";
        echo "<thead>";
        echo "<tr>
                <th>ID</th>
                <th>Фамилия клиента</th>
                <th>Имя клиента</th>
                <th>Отчество клиента</th>
                <th>Название услуги</th>
                <th>Действия</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['ClientServiceId']}</td>
                    <td>{$row['ClientSurname']}</td>
                    <td>{$row['ClientName']}</td>
                    <td>{$row['ClientPatr']}</td>
                    <td>{$row['ServiceName']}</td>
                    <td>
                        <a href='clientservices.php?edit={$row['ClientServiceId']}'>Редактировать</a>
                        <a href='clientservices.php?delete={$row['ClientServiceId']}' onclick=\"return confirm('Вы уверены, что хотите удалить эту запись?');\">Удалить</a>
                    </td>
                  </tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>У выбранного клиента нет услуг.</p>";
    }
    ?>
<?php endif; ?>

        <h2>Все услуги клиентов</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Фамилия клиента</th>
                    <th>Имя клиента</th>
                    <th>Отчество клиента</th>
                    <th>Название услуги</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientservices as $clientservice): ?>
                    <tr>
                        <td><?php echo $clientservice['ClientServiceId']; ?></td>
                        <td><?php echo $clientservice['ClientSurname']; ?></td>
                        <td><?php echo $clientservice['ClientName']; ?></td>
                        <td><?php echo $clientservice['ClientPatr']; ?></td>
                        <td><?php echo $clientservice['ServiceName']; ?></td>
                        <td>
                            <a href="clientservices.php?edit=<?php echo $clientservice['ClientServiceId']; ?>">Редактировать</a>
                            <a href="clientservices.php?delete=<?php echo $clientservice['ClientServiceId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить эту запись?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2><?php echo $edit_state ? 'Редактировать услугу клиента' : 'Добавить услугу клиента'; ?></h2>
        <form method="post" action="clientservices.php">
            <input type="hidden" name="ClientServiceId" value="<?php echo $ClientServiceId; ?>">
            <div class="form-group">
                <label for="ClientId">Клиент:</label>
                <select name="ClientId" required>
                    <option value="">Выберите клиента</option>
                    <?php
                    $clients = $connection->query("SELECT ClientId, ClientSurname, ClientName, ClientPatr FROM Clients");
                    while ($client = $clients->fetch_assoc()): ?>
                        <option value="<?php echo $client['ClientId']; ?>" <?php echo $ClientId == $client['ClientId'] ? 'selected' : ''; ?>>
                            <?php echo $client['ClientSurname'] . ' ' . $client['ClientName'] . ' ' . $client['ClientPatr']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ServiceId">Услуга:</label>
                <select name="ServiceId" required>
                    <option value="">Выберите услугу</option>
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
                <button type="submit" name="<?php echo $edit_state ? 'update' : 'save'; ?>" class="btn"><?php echo $edit_state ? 'Обновить' : 'Сохранить'; ?></button>
            </div>
        </form>
        <a href="/admin/panel.php" class="back-button">Назад</a>
    </div>
</body>
</html>