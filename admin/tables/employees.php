<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

include_once '../../config/db.php';

$EmployeeId = '';
$EmployeeSurname = '';
$EmployeeName = '';
$EmployeePatr = '';
$EmployeePost = '';
$EmployeeDepartment = '';
$EmployeePhone = '';
$EmployeeEmail = '';
$EmployeeAddress = '';
$EmployeeEducation = '';
$AccountId = '';
$edit_state = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $EmployeeSurname = $_POST['EmployeeSurname'];
    $EmployeeName = $_POST['EmployeeName'];
    $EmployeePatr = $_POST['EmployeePatr'];
    $EmployeePost = $_POST['EmployeePost'];
    $EmployeeDepartment = $_POST['EmployeeDepartment'];
    $EmployeePhone = $_POST['EmployeePhone'];
    $EmployeeEmail = $_POST['EmployeeEmail'];
    $EmployeeAddress = $_POST['EmployeeAddress'];
    $EmployeeEducation = $_POST['EmployeeEducation'];
    $AccountId = $_POST['AccountId'] ?: NULL;

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO Employees (EmployeeSurname, EmployeeName, EmployeePatr, EmployeePost, EmployeeDepartment, EmployeePhone, EmployeeEmail, EmployeeAddress, EmployeeEducation, AccountId) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssssssi", $EmployeeSurname, $EmployeeName, $EmployeePatr, $EmployeePost, $EmployeeDepartment, $EmployeePhone, $EmployeeEmail, $EmployeeAddress, $EmployeeEducation, $AccountId);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $EmployeeId = $_POST['EmployeeId'];
        $sql = "UPDATE Employees SET EmployeeSurname=?, EmployeeName=?, EmployeePatr=?, EmployeePost=?, EmployeeDepartment=?, EmployeePhone=?, EmployeeEmail=?, EmployeeAddress=?, EmployeeEducation=?, AccountId=? WHERE EmployeeId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssssssii", $EmployeeSurname, $EmployeeName, $EmployeePatr, $EmployeePost, $EmployeeDepartment, $EmployeePhone, $EmployeeEmail, $EmployeeAddress, $EmployeeEducation, $AccountId, $EmployeeId);
        $stmt->execute();
    }

    header("Location: employees.php");
    exit;
}

if (isset($_GET['edit'])) {
    $EmployeeId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM Employees WHERE EmployeeId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $EmployeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();

    $EmployeeSurname = $employee['EmployeeSurname'];
    $EmployeeName = $employee['EmployeeName'];
    $EmployeePatr = $employee['EmployeePatr'];
    $EmployeePost = $employee['EmployeePost'];
    $EmployeeDepartment = $employee['EmployeeDepartment'];
    $EmployeePhone = $employee['EmployeePhone'];
    $EmployeeEmail = $employee['EmployeeEmail'];
    $EmployeeAddress = $employee['EmployeeAddress'];
    $EmployeeEducation = $employee['EmployeeEducation'];
    $AccountId = $employee['AccountId'];
}

if (isset($_GET['delete'])) {
    $EmployeeId = $_GET['delete'];
    $sql = "DELETE FROM Employees WHERE EmployeeId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $EmployeeId);
    $stmt->execute();

    header("Location: employees.php");
    exit;
}

$sql = "SELECT e.*, a.Username FROM Employees e JOIN Accounts a ON e.AccountId = a.AccountId";
$result = $connection->query($sql);
$employees = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Сотрудники</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Сотрудники</h1>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Должность</th>
                    <th>Отдел</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Адрес</th>
                    <th>Образование</th>
                    <th>Аккаунт</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?php echo $employee['EmployeeId']; ?></td>
                        <td><?php echo $employee['EmployeeSurname']; ?></td>
                        <td><?php echo $employee['EmployeeName']; ?></td>
                        <td><?php echo $employee['EmployeePatr']; ?></td>
                        <td><?php echo $employee['EmployeePost']; ?></td>
                        <td><?php echo $employee['EmployeeDepartment']; ?></td>
                        <td><?php echo $employee['EmployeePhone']; ?></td>
                        <td><?php echo $employee['EmployeeEmail']; ?></td>
                        <td><?php echo $employee['EmployeeAddress']; ?></td>
                        <td><?php echo $employee['EmployeeEducation']; ?></td>
                        <td><?php echo $employee['Username']; ?></td>
                        <td>
                            <a href="employees.php?edit=<?php echo $employee['EmployeeId']; ?>">Редактировать</a>
                            <a href="employees.php?delete=<?php echo $employee['EmployeeId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этого сотрудника?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2><?php echo $edit_state ? 'Редактировать сотрудника' : 'Добавить сотрудника'; ?></h2>
        <form method="post" action="employees.php">
            <input type="hidden" name="EmployeeId" value="<?php echo $EmployeeId; ?>">
            <div class="form-group">
            <div class="form-group">
    <label for="EmployeeSurname">Фамилия:</label>
    <input type="text" name="EmployeeSurname" value="<?php echo $EmployeeSurname; ?>" required>
</div>
<div class="form-group">
    <label for="EmployeeName">Имя:</label>
    <input type="text" name="EmployeeName" value="<?php echo $EmployeeName; ?>" required>
</div>
<div class="form-group">
    <label for="EmployeePatr">Отчество:</label>
    <input type="text" name="EmployeePatr" value="<?php echo $EmployeePatr; ?>">
</div>
<div class="form-group">
    <label for="EmployeePost">Должность:</label>
    <input type="text" name="EmployeePost" value="<?php echo $EmployeePost; ?>" required>
</div>
<div class="form-group">
    <label for="EmployeeDepartment">Отдел:</label>
    <input type="text" name="EmployeeDepartment" value="<?php echo $EmployeeDepartment; ?>" required>
</div>
<div class="form-group">
    <label for="EmployeePhone">Телефон:</label>
    <input type="text" name="EmployeePhone" value="<?php echo $EmployeePhone; ?>" required>
</div>
<div class="form-group">
    <label for="EmployeeEmail">Email:</label>
    <input type="email" name="EmployeeEmail" value="<?php echo $EmployeeEmail; ?>" required>
</div>
<div class="form-group">
    <label for="EmployeeAddress">Адрес:</label>
    <input type="text" name="EmployeeAddress" value="<?php echo $EmployeeAddress; ?>" required>
</div>
<div class="form-group">
    <label for="EmployeeEducation">Образование:</label>
    <input type="text" name="EmployeeEducation" value="<?php echo $EmployeeEducation; ?>" required>
</div>
<div class="form-group">
    <label for="AccountId">Аккаунт:</label>
    <select name="AccountId">
        <option value="">Выберите аккаунт</option>
        <?php
        $accounts = $connection->query("SELECT * FROM Accounts");
        while ($account = $accounts->fetch_assoc()): ?>
            <option value="<?php echo $account['AccountId']; ?>" <?php echo $AccountId == $account['AccountId'] ? 'selected' : ''; ?>>
                <?php echo $account['Username']; ?>
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