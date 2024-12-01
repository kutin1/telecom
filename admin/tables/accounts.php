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
$AccountId = '';
$Username = '';
$Password = '';
$Role = '';
$ClientId = '';
$EmployeeId = '';
$edit_state = false;

// Проверка существования ClientId и EmployeeId
function checkIdExists($connection, $table, $column, $id) {
    if ($id === '') return true; // Разрешаем пустые значения
    $sql = "SELECT COUNT(*) FROM $table WHERE $column=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count > 0;
}

// Проверка уникальности имени пользователя
function isUsernameUnique($connection, $username, $accountId = null) {
    $sql = "SELECT COUNT(*) FROM Accounts WHERE Username=?";
    if ($accountId) {
        $sql .= " AND AccountId != ?";
    }
    $stmt = $connection->prepare($sql);
    if ($accountId) {
        $stmt->bind_param("si", $username, $accountId);
    } else {
        $stmt->bind_param("s", $username);
    }
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count === 0;
}

// Обработка POST-запроса для добавления/редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Username = $_POST['Username'];
    $Password = password_hash($_POST['Password'], PASSWORD_DEFAULT); // Хеширование пароля
    $Role = $_POST['Role'];
    $ClientId = $_POST['ClientId'] ?: NULL;
    $EmployeeId = $_POST['EmployeeId'] ?: NULL;

    if (isUsernameUnique($connection, $Username, isset($_POST['AccountId']) ? $_POST['AccountId'] : null)) {
        if ((is_null($ClientId) || checkIdExists($connection, 'Clients', 'ClientId', $ClientId)) && (is_null($EmployeeId) || checkIdExists($connection, 'Employees', 'EmployeeId', $EmployeeId))) {
            if (isset($_POST['save'])) {
                $sql = "INSERT INTO Accounts (Username, Password, Role, ClientId, EmployeeId) VALUES (?, ?, ?, ?, ?)";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("sssii", $Username, $Password, $Role, $ClientId, $EmployeeId);
                $stmt->execute();
            } elseif (isset($_POST['update'])) {
                $AccountId = $_POST['AccountId'];
                $sql = "UPDATE Accounts SET Username=?, Password=?, Role=?, ClientId=?, EmployeeId=? WHERE AccountId=?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("sssiii", $Username, $Password, $Role, $ClientId, $EmployeeId, $AccountId);
                $stmt->execute();
            }

            header("Location: accounts.php");
            exit;
        } else {
            $error_message = "Client ID или Employee ID не существует.";
        }
    } else {
        $error_message = "Имя пользователя уже существует.";
    }
}

// Обработка GET-запроса для редактирования
if (isset($_GET['edit'])) {
    $AccountId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM Accounts WHERE AccountId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $AccountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    $Username = $account['Username'];
    $Password = ''; // Пароль не выводится для редактирования
    $Role = $account['Role'];
    $ClientId = $account['ClientId'];
    $EmployeeId = $account['EmployeeId'];
}

// Обработка GET-запроса для удаления
if (isset($_GET['delete'])) {
    $AccountId = $_GET['delete'];
    $sql = "DELETE FROM Accounts WHERE AccountId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $AccountId);
    $stmt->execute();

    header("Location: accounts.php");
    exit;
}

// Получение данных из таблицы "Accounts" с JOIN для отображения связанных данных
$sql = "SELECT 
            a.AccountId, 
            a.Username, 
            a.Role, 
            c.ClientSurname, 
            c.ClientName, 
            e.EmployeeSurname, 
            e.EmployeeName 
        FROM 
            Accounts a
        LEFT JOIN 
            Clients c ON a.ClientId = c.ClientId
        LEFT JOIN 
            Employees e ON a.EmployeeId = e.EmployeeId";
$result = $connection->query($sql);
$accounts = $result->fetch_all(MYSQLI_ASSOC);

// Получение данных для выпадающих списков клиентов и сотрудников
$clients_result = $connection->query("SELECT ClientId, ClientSurname, ClientName FROM Clients");
$clients = $clients_result->fetch_all(MYSQLI_ASSOC);

$employees_result = $connection->query("SELECT EmployeeId, EmployeeSurname, EmployeeName FROM Employees");
$employees = $employees_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Аккаунты</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Аккаунты</h1>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя пользователя</th>
                    <th>Роль</th>
                    <th>Клиент</th>
                    <th>Сотрудник</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td><?php echo $account['AccountId']; ?></td>
                        <td><?php echo $account['Username']; ?></td>
                        <td><?php echo $account['Role']; ?></td>
                        <td><?php echo $account['ClientSurname'] . ' ' . $account['ClientName']; ?></td>
                        <td><?php echo $account['EmployeeSurname'] . ' ' . $account['EmployeeName']; ?></td>
                        <td>
                            <a href="accounts.php?edit=<?php echo $account['AccountId']; ?>">Редактировать</a>
                            <a href="accounts.php?delete=<?php echo $account['AccountId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этот аккаунт?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table-buttons">
            <form method="POST" action="accounts.php">
                <input type="hidden" name="AccountId" value="<?php echo $AccountId; ?>">
                <div class="form-group">
                    <label for="Username">Имя пользователя</label>
                    <input type="text" name="Username" value="<?php echo $Username; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Password">Пароль</label>
                    <input type="password" name="Password" value="<?php echo $Password; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Role">Роль</label>
                    <select name="Role" required>
                        <option value="">Выберите роль</option>
                        <option value="Client" <?php echo $Role == 'Client' ? 'selected' : ''; ?>>Client</option>
                        <option value="Admin" <?php echo $Role == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ClientId">Клиент</label>
                    <select name="ClientId">
                        <option value="">Выберите клиента</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['ClientId']; ?>" <?php echo $ClientId == $client['ClientId'] ? 'selected' : ''; ?>>
                                <?php echo $client['ClientSurname'] . ' ' . $client['ClientName']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="EmployeeId">Сотрудник</label>
                    <select name="EmployeeId">
                        <option value="">Выберите сотрудника</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo $employee['EmployeeId']; ?>" <?php echo $EmployeeId == $employee['EmployeeId'] ? 'selected' : ''; ?>>
                                <?php echo $employee['EmployeeSurname'] . ' ' . $employee['EmployeeName']; ?>
                            </option>
                        <?php endforeach; ?>
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
        </div>
        <a href="/admin/panel.php" class="back-button">Назад</a>
    </div>
</body>
</html>
