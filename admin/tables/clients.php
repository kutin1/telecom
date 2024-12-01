<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login.php");
    exit;
}

include_once '../../config/db.php';

$ClientId = '';
$ContractNumber = '';
$ClientSurname = '';
$ClientName = '';
$ClientPatr = '';
$ClientPhone = '';
$AddressId = '';
$ClientEmail = '';
$InternetId = '';
$TelevisionId = '';
$MobileId = '';
$AccountId = '';
$edit_state = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ContractNumber = $_POST['ContractNumber'];
    $ClientSurname = $_POST['ClientSurname'];
    $ClientName = $_POST['ClientName'];
    $ClientPatr = $_POST['ClientPatr'];
    $ClientPhone = $_POST['ClientPhone'];
    $AddressId = $_POST['AddressId'] ?: NULL;
    $ClientEmail = $_POST['ClientEmail'];
    $InternetId = $_POST['InternetId'] ?: NULL;
    $TelevisionId = $_POST['TelevisionId'] ?: NULL;
    $MobileId = $_POST['MobileId'] ?: NULL;
    $AccountId = $_POST['AccountId'] ?: NULL;

    if (isset($_POST['save'])) {
        $sql = "INSERT INTO Clients (ContractNumber, ClientSurname, ClientName, ClientPatr, ClientPhone, AddressId, ClientEmail, InternetId, TelevisionId, MobileId, AccountId) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssisiiii", $ContractNumber, $ClientSurname, $ClientName, $ClientPatr, $ClientPhone, $AddressId, $ClientEmail, $InternetId, $TelevisionId, $MobileId, $AccountId);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $ClientId = $_POST['ClientId'];
        $sql = "UPDATE Clients SET ContractNumber=?, ClientSurname=?, ClientName=?, ClientPatr=?, ClientPhone=?, AddressId=?, ClientEmail=?, InternetId=?, TelevisionId=?, MobileId=?, AccountId=? WHERE ClientId=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssissiiii", $ContractNumber, $ClientSurname, $ClientName, $ClientPatr, $ClientPhone, $AddressId, $ClientEmail, $InternetId, $TelevisionId, $MobileId, $AccountId, $ClientId);
        $stmt->execute();
    }

    header("Location: clients.php");
    exit;
}

if (isset($_GET['edit'])) {
    $ClientId = $_GET['edit'];
    $edit_state = true;
    $sql = "SELECT * FROM Clients WHERE ClientId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $ClientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $client = $result->fetch_assoc();

    $ContractNumber = $client['ContractNumber'];
    $ClientSurname = $client['ClientSurname'];
    $ClientName = $client['ClientName'];
    $ClientPatr = $client['ClientPatr'];
    $ClientPhone = $client['ClientPhone'];
    $AddressId = $client['AddressId'];
    $ClientEmail = $client['ClientEmail'];
    $InternetId = $client['InternetId'];
    $TelevisionId = $client['TelevisionId'];
    $MobileId = $client['MobileId'];
    $AccountId = $client['AccountId'];
}

if (isset($_GET['delete'])) {
    $ClientId = $_GET['delete'];
    $sql = "DELETE FROM Clients WHERE ClientId=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $ClientId);
    $stmt->execute();

    header("Location: clients.php");
    exit;
}

$sql = "SELECT c.ClientId, c.ContractNumber, c.ClientSurname, c.ClientName, c.ClientPatr, c.ClientPhone, a.Street, a.Building, a.Apartment, c.ClientEmail, i.InternetName, t.TelevisionName, m.MobileName, acc.Username 
        FROM Clients c
        LEFT JOIN Addresses a ON c.AddressId = a.AddressId
        LEFT JOIN InternetTariffs i ON c.InternetId = i.InternetId
        LEFT JOIN TelevisionTariffs t ON c.TelevisionId = t.TelevisionId
        LEFT JOIN MobileTariffs m ON c.MobileId = m.MobileId
        LEFT JOIN Accounts acc ON c.AccountId = acc.AccountId";
$result = $connection->query($sql);
$clients = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Клиенты</title>
    <link href="../../src/styles/admin.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="../../src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../src/img/favicons/favicon.ico">
</head>
<body>
    <div class="admin-panel">
        <h1>Клиенты</h1>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Номер договора</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Телефон</th>
                    <th>Адрес</th>
                    <th>Email</th>
                    <th>Интернет тариф</th>
                    <th>ТВ тариф</th>
                    <th>Мобильный тариф</th>
                    <th>Аккаунт</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo $client['ClientId']; ?></td>
                        <td><?php echo $client['ContractNumber']; ?></td>
                        <td><?php echo $client['ClientSurname']; ?></td>
                        <td><?php echo $client['ClientName']; ?></td>
                        <td><?php echo $client['ClientPatr']; ?></td>
                        <td><?php echo $client['ClientPhone']; ?></td>
                        <td><?php echo "{$client['Street']}, {$client['Building']}, {$client['Apartment']}"; ?></td>
                        <td><?php echo $client['ClientEmail']; ?></td>
                        <td><?php echo $client['InternetName']; ?></td>
                        <td><?php echo $client['TelevisionName']; ?></td>
                        <td><?php echo $client['MobileName']; ?></td>
                        <td><?php echo $client['Username']; ?></td>
                        <td>
                            <a href="clients.php?edit=<?php echo $client['ClientId']; ?>">Редактировать</a>
                            <a href="clients.php?delete=<?php echo $client['ClientId']; ?>" onclick="return confirm('Вы уверены, что хотите удалить этого клиента?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2><?php echo $edit_state ? 'Редактировать клиента' : 'Добавить клиента'; ?></h2>
        <form method="post" action="clients.php">
            <input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
            <div class="form-group">
                <label for="ContractNumber">Номер договора:</label>
                <input type="text" name="ContractNumber" value="<?php echo $ContractNumber; ?>" required>
            </div>
            <div class="form-group">
                <label for="ClientSurname">Фамилия:</label>
                <input type="text" name="ClientSurname" value="<?php echo $ClientSurname; ?>" required>
            </div>
            <div class="form-group">
                <label for="ClientName">Имя:</label>
                <input type="text" name="ClientName" value="<?php echo $ClientName; ?>" required>
            </div>
            <div class="form-group">
                <label for="ClientPatr">Отчество:</label>
                <input type="text" name="ClientPatr" value="<?php echo $ClientPatr; ?>">
            </div>
            <div class="form-group">
                <label for="ClientPhone">Телефон:</label>
                <input type="text" name="ClientPhone" value="<?php echo $ClientPhone; ?>" required>
            </div>
            <div class="form-group">
                <label for="AddressId">Адрес:</label>
                <select name="AddressId">
                    <option value="">Выберите адрес</option>
                    <?php
                    $addresses = $connection->query("SELECT * FROM Addresses");
                    while ($address = $addresses->fetch_assoc()): ?>
                        <option value="<?php echo $address['AddressId']; ?>" <?php echo $AddressId == $address['AddressId'] ? 'selected' : ''; ?>>
                            <?php echo "{$address['Street']}, {$address['Building']}, {$address['Apartment']}"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ClientEmail">Email:</label>
                <input type="email" name=" ClientEmail" value="<?php echo $ClientEmail; ?>" required>
                </div>
            <div class="form-group">
                <label for="InternetId">Интернет тариф:</label>
                <select name="InternetId">
                    <option value="">Выберите интернет тариф</option>
                    <?php
                    $internetTariffs = $connection->query("SELECT * FROM InternetTariffs");
                    while ($internet = $internetTariffs->fetch_assoc()): ?>
                        <option value="<?php echo $internet['InternetId']; ?>" <?php echo $InternetId == $internet['InternetId'] ? 'selected' : ''; ?>>
                            <?php echo $internet['InternetName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="TelevisionId">ТВ тариф:</label>
                <select name="TelevisionId">
                    <option value="">Выберите ТВ тариф</option>
                    <?php
                    $televisionTariffs = $connection->query("SELECT * FROM TelevisionTariffs");
                    while ($television = $televisionTariffs->fetch_assoc()): ?>
                        <option value="<?php echo $television['TelevisionId']; ?>" <?php echo $TelevisionId == $television['TelevisionId'] ? 'selected' : ''; ?>>
                            <?php echo $television['TelevisionName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="MobileId">Мобильный тариф:</label>
                <select name="MobileId">
                    <option value="">Выберите мобильный тариф</option>
                    <?php
                    $mobileTariffs = $connection->query("SELECT * FROM MobileTariffs");
                    while ($mobile = $mobileTariffs->fetch_assoc()): ?>
                        <option value="<?php echo $mobile['MobileId']; ?>" <?php echo $MobileId == $mobile['MobileId'] ? 'selected' : ''; ?>>
                            <?php echo $mobile['MobileName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
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