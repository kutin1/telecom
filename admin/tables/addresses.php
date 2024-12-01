<!-- addresses.php -->
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
$AddressId = '';
$Zipcode = '';
$Country = '';
$City = '';
$Street = '';
$Building = '';
$Entrance = '';
$Apartment = '';
$Floor = '';
$Code = '';
$edit_state = false;

// Обработка POST-запроса для добавления/редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Zipcode = $_POST['Zipcode'];
    $Country = $_POST['Country'];
    $City = $_POST['City'];
    $Street = $_POST['Street'];
    $Building = $_POST['Building'];
    $Entrance = $_POST['Entrance'];
    $Apartment = $_POST['Apartment'];
    $Floor = $_POST['Floor'];
    $Code = $_POST['Code'];

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO Addresses (Zipcode, Country, City, Street, Building, Entrance, Apartment, Floor, Code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssssisi", $Zipcode, $Country, $City, $Street, $Building, $Entrance, $Apartment, $Floor, $Code);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $AddressId = $_POST['AddressId'];
        $sql = "UPDATE Addresses SET Zipcode=?, Country=?, City=?, Street=?, Building=?, Entrance=?, Apartment=?, Floor=?, Code=? WHERE AddressId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssisisi", $Zipcode, $Country, $City, $Street, $Building, $Entrance, $Apartment, $Floor, $Code, $AddressId);
        $stmt->execute();
    }

    header("Location: addresses.php");
    exit;
}

// Обработка GET-запроса для редактирования
if (isset($_GET['edit'])) {
    $AddressId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM Addresses WHERE AddressId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $AddressId);
    $stmt->execute();
    $result = $stmt->get_result();
    $address = $result->fetch_assoc();

    $Zipcode = $address['Zipcode'];
    $Country = $address['Country'];
    $City = $address['City'];
    $Street = $address['Street'];
    $Building = $address['Building'];
    $Entrance = $address['Entrance'];
    $Apartment = $address['Apartment'];
    $Floor = $address['Floor'];
    $Code = $address['Code'];
}

// Обработка GET-запроса для удаления
if (isset($_GET['delete'])) {
    $AddressId = $_GET['delete'];
    $sql = "DELETE FROM Addresses WHERE AddressId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $AddressId);
    $stmt->execute();

    header("Location: addresses.php");
    exit;
}

// Получение данных из таблицы "Addresses"
$sql = "SELECT * FROM Addresses";
$result = $connection->query($sql);
$addresses = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Адреса</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Адреса</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Индекс</th>
                    <th>Страна</th>
                    <th>Город</th>
                    <th>Улица</th>
                    <th>Дом</th>
                    <th>Подъезд</th>
                    <th>Квартира</th>
                    <th>Этаж</th>
                    <th>Код</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($addresses as $address): ?>
                    <tr>
                        <td><?php echo $address['AddressId']; ?></td>
                        <td><?php echo $address['Zipcode']; ?></td>
                        <td><?php echo $address['Country']; ?></td>
                        <td><?php echo $address['City']; ?></td>
                        <td><?php echo $address['Street']; ?></td>
                        <td><?php echo $address['Building']; ?></td>
                        <td><?php echo $address['Entrance']; ?></td>
                        <td><?php echo $address['Apartment']; ?></td>
                        <td><?php echo $address['Floor']; ?></td>
                        <td><?php echo $address['Code']; ?></td>
                        <td>
                            <a href="addresses.php?edit=<?php echo $address['AddressId']; ?>">Редактировать</a>
                            <a href="addresses.php?delete=<?php echo $address['AddressId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этот адрес?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table-buttons">
            <form method="POST" action="addresses.php">
                <input type="hidden" name="AddressId" value="<?php echo $AddressId; ?>">
                <div class="form-group">
                    <label for="Zipcode">Индекс</label>
                    <input type="text" name="Zipcode" value="<?php echo $Zipcode; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Country">Страна</label>
                    <input type="text" name="Country" value="<?php echo $Country; ?>" required>
                </div>
                <div class="form-group">
                    <label for="City">Город</label>
                    <input type="text" name="City" value="<?php echo $City; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Street">Улица</label>
                    <input type="text" name="Street" value="<?php echo $Street; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Building">Дом</label>
                    <input type="text" name="Building" value="<?php echo $Building; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Entrance">Подъезд</label>
                    <input type="number" name="Entrance" value="<?php echo $Entrance; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Apartment">Квартира</label>
                    <input type="text" name="Apartment" value="<?php echo $Apartment; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Floor">Этаж</label>
                    <input type="number" name="Floor" value="<?php echo $Floor; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Code">Код</label>
                    <input type="text" name="Code" value="<?php echo $Code; ?>" required>
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
