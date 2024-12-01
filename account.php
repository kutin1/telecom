    <?php
    session_start();
    include 'config/db.php'; // Подключение к базе данных

    // Проверка, что пользователь авторизован
    if (!isset($_SESSION['AccountId'])) {
        header("Location: index.php");
        exit;
    }

    // Получение данных из базы данных для остальных полей клиента
    $accountId = $_SESSION['AccountId'];
    $sqlClient = "SELECT ClientName, ClientSurname, ClientPatr, ClientPhone, ClientEmail FROM Clients WHERE AccountId = $accountId";
    $resultClient = mysqli_query($connection, $sqlClient);

    // Получение данных из базы данных для адреса клиента
    $sqlAddress = "SELECT a.Zipcode, a.Country, a.City, a.Street, a.Building, a.Entrance, a.Apartment, a.Floor, a.Code
                FROM Clients c
                JOIN Addresses a ON c.AddressId = a.AddressId
                WHERE c.AccountId = $accountId";
    $resultAddress = mysqli_query($connection, $sqlAddress);

    // Получение данных из базы данных о счетах клиента
    $sqlInvoices = "SELECT InvoiceNumber, InvoiceBalance FROM Invoices WHERE ClientId = (SELECT ClientId FROM Clients WHERE AccountId = $accountId)";
    $resultInvoices = mysqli_query($connection, $sqlInvoices);


    // Получаем айдишник тарифа интернета у текущего пользователя
    $sqlInternetId = "SELECT InternetId FROM Clients WHERE AccountId = $accountId";
    $resultInternetId = mysqli_query($connection, $sqlInternetId);
    $rowInternetId = mysqli_fetch_assoc($resultInternetId);
    $internetId = $rowInternetId['InternetId'];

    // Получаем название тарифа интернета
    $sqlInternetTariff = "SELECT InternetName FROM InternetTariffs WHERE InternetId = $internetId";
    $resultInternetTariff = mysqli_query($connection, $sqlInternetTariff);
    $rowInternetTariff = mysqli_fetch_assoc($resultInternetTariff);
    $internetTariff = $rowInternetTariff['InternetName'];


    // Получаем айдишник тарифа телевидения у текущего пользователя
    $sqlTelevisionId = "SELECT TelevisionId FROM Clients WHERE AccountId = $accountId";
    $resultTelevisionId = mysqli_query($connection, $sqlTelevisionId);
    $rowTelevisionId = mysqli_fetch_assoc($resultTelevisionId);
    $televisionId = $rowTelevisionId['TelevisionId'];

    // Получаем название тарифа телевидения
    $sqlTelevisionTariff = "SELECT TelevisionName FROM TelevisionTariffs WHERE TelevisionId = $televisionId";
    $resultTelevisionTariff = mysqli_query($connection, $sqlTelevisionTariff);
    $rowTelevisionTariff = mysqli_fetch_assoc($resultTelevisionTariff);
    $televisionTariff = $rowTelevisionTariff['TelevisionName'];

    // Получаем айдишник тарифа мобильной связи у текущего пользователя
    $sqlMobileId = "SELECT MobileId FROM Clients WHERE AccountId = $accountId";
    $resultMobileId = mysqli_query($connection, $sqlMobileId);
    $rowMobileId = mysqli_fetch_assoc($resultMobileId);
    $mobileId = $rowMobileId['MobileId'];

    // Получаем название тарифа мобильной связи
    $sqlMobileTariff = "SELECT MobileName FROM MobileTariffs WHERE MobileId = $mobileId";
    $resultMobileTariff = mysqli_query($connection, $sqlMobileTariff);
    $rowMobileTariff = mysqli_fetch_assoc($resultMobileTariff);
    $mobileTariff = $rowMobileTariff['MobileName'];


    // Получаем айдишники активированных услуг для текущего клиента и их типы
    $clientId = $_SESSION['AccountId'];
    $sqlClientServices = "SELECT s.ServiceName, s.ServiceType 
                        FROM Services s
                        JOIN ClientServices cs ON s.ServiceId = cs.ServiceId
                        WHERE cs.ClientId = $clientId";
    $resultClientServices = mysqli_query($connection, $sqlClientServices);

    $activatedServices = []; // Массив для хранения названий активированных услуг по типам

    if ($resultClientServices && mysqli_num_rows($resultClientServices) > 0) {
        while ($rowService = mysqli_fetch_assoc($resultClientServices)) {
            $serviceType = $rowService['ServiceType'];
            $serviceName = $rowService['ServiceName'];

            // Добавляем услугу в массив соответствующего типа
            $activatedServices[$serviceType][] = $serviceName;
        }
    }



    if ($resultClient && mysqli_num_rows($resultClient) > 0 && $resultAddress && mysqli_num_rows($resultAddress) > 0) {
        $rowClient = mysqli_fetch_assoc($resultClient);
        $clientName = $rowClient['ClientName'];
        $clientSurname = $rowClient['ClientSurname'];
        $clientPatr = $rowClient['ClientPatr'];
        $clientPhone = $rowClient['ClientPhone'];
        $clientEmail = $rowClient['ClientEmail'];

        $rowAddress = mysqli_fetch_assoc($resultAddress);
        $zipcode = $rowAddress['Zipcode'];
        $country = $rowAddress['Country'];
        $city = $rowAddress['City'];
        $street = $rowAddress['Street'];
        $building = $rowAddress['Building'];
        $entrance = $rowAddress['Entrance'];
        $apartment = $rowAddress['Apartment'];
        $floor = $rowAddress['Floor'];
        $code = $rowAddress['Code'];

        // Составляем строку адреса
        $addressString = "$zipcode, $country, город $city, улица $street, дом $building, подъезд $entrance, квартира $apartment, этаж $floor, код от домофона/комментарий: $code";
    } else {
        // Обработка случая, если не удалось получить данные
    }

    if ($resultInvoices && mysqli_num_rows($resultInvoices) > 0) {
        $rowInvoice = mysqli_fetch_assoc($resultInvoices);
        $invoiceNumber = $rowInvoice['InvoiceNumber'];
        $invoiceBalance = $rowInvoice['InvoiceBalance'];
    } else {
        // Обработка случая, если не удалось получить данные о счетах
    }
    ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Телекоммуникационная компания "Телеком" | Личный кабинет</title>
    <link rel="stylesheet" href="src/styles/style.css">
    <link rel="icon" type="image/png" sizes="32x32" href="src/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="src/img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="src/img/favicons/favicon.ico">
</head>
<body class="main-page">
    <header class="header">
        <div class="service-menu">
           
                <ul class="service-menu-list">
                    <li class="service-menu-item">
                        <a class="service-menu-link" href="person.php">Для частных лиц</a>
                    </li>
                    <li class="service-menu-item">
                        <a class="service-menu-link" href="business.php">Для бизнеса</a>
                    </li>
                </ul>
            
            <ul class="service-menu-list icons-list">
                <li class="service-menu-item">
                    <a class="service-menu-link service-menu-location" href="#">
                        <svg class="location-icon" xmlns="http://www.w3.org/2000/svg" width="25" height="34" viewBox="0 0 25 34" fill="none">
                            <path d="M18.1764 33.4934H6.82402C6.20297 33.4835 5.70413 32.9815 5.70413 32.3653C5.70413 31.7476 6.20297 31.2456 6.82402 31.2371H18.1764C18.7975 31.2456 19.2963 31.7476 19.2963 32.3653C19.2963 32.9815 18.7975 33.4836 18.1764 33.4934ZM12.5 30.0765C12.1817 30.0765 11.879 29.9454 11.6629 29.7127L3.31901 20.7214C-4.47627 11.898 2.63685 -0.336943 12.4983 0.00709871C22.4096 -0.328528 29.4572 11.9121 21.6805 20.7214L13.3365 29.7127C13.1205 29.9454 12.8178 30.0765 12.4994 30.0765H12.5ZM12.5015 2.26333C7.77757 2.26333 2.5687 5.42779 2.29183 12.3759V12.3773C2.29894 14.9044 3.26251 17.3369 4.9921 19.1928L12.5003 27.2845L20.0086 19.1943V19.1928C21.7466 17.3243 22.7102 14.8748 22.7074 12.3322C22.7415 9.64159 21.6756 7.05247 19.7527 5.15429C17.8299 3.25759 15.213 2.21398 12.5015 2.26333ZM12.5 18.5413C10.8401 18.5568 9.24407 17.9053 8.07445 16.7362C6.90483 15.5672 6.261 13.9793 6.288 12.3323C6.32921 4.39007 18.3509 3.85578 18.7562 12.3323C18.7548 13.978 18.0954 15.556 16.9215 16.7207C15.749 17.8841 14.1584 18.5384 12.5 18.5413ZM12.517 8.38066C11.4597 8.38066 10.445 8.79806 9.69884 9.54123C8.95129 10.2844 8.53347 11.2913 8.53489 12.3419C8.53773 13.391 8.96126 14.3965 9.71164 15.1355C10.4606 15.8758 11.4768 16.289 12.5355 16.2848C14.7313 16.2763 16.5063 14.5037 16.5019 12.3234C16.4963 10.1433 14.7141 8.37922 12.517 8.38066Z" fill="black"/>
                        </svg>
                        Ростов-на-Дону
                    </a>
                </li>
                <li class="service-menu-item">
                    <a class="service-menu-link service-menu-account" href="config/logout.php">
                    <svg class="logout-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="30" viewBox="0 0 24 30" fill="none">
                        <path d="M3.92871 23H9.92871C11.583 23 12.9287 21.6543 12.9287 20V16C12.9287 15.4478 12.4814 15 11.9287 15C11.376 15 10.9287 15.4478 10.9287 16V20C10.9287 20.5513 10.4805 21 9.92871 21H3.92871C3.37695 21 2.92871 20.5513 2.92871 20V4C2.92871 3.44873 3.37695 3 3.92871 3H9.92871C10.4805 3 10.9287 3.44873 10.9287 4V8C10.9287 8.55225 11.376 9 11.9287 9C12.4814 9 12.9287 8.55225 12.9287 8V4C12.9287 2.3457 11.583 1 9.92871 1H3.92871C2.27441 1 0.928711 2.3457 0.928711 4V20C0.928711 21.6543 2.27441 23 3.92871 23Z" fill="black"/>
                        <path d="M6.92871 12C6.92871 12.5522 7.37598 13 7.92871 13H19.5146L15.2217 17.293C14.8311 17.6836 14.8311 18.3164 15.2217 18.707C15.417 18.9023 15.6729 19 15.9287 19C16.1846 19 16.4404 18.9023 16.6357 18.707L22.6351 12.7077C22.7276 12.6154 22.8009 12.5046 22.8517 12.3819C22.9527 12.1376 22.9527 11.8624 22.8517 11.6181C22.8009 11.4954 22.7276 11.3846 22.6351 11.2923L16.6357 5.29297C16.2451 4.90234 15.6123 4.90234 15.2217 5.29297C14.8311 5.68359 14.8311 6.31641 15.2217 6.70703L19.5146 11H7.92871C7.37598 11 6.92871 11.4478 6.92871 12Z" fill="black"/>
                    </svg>
                    <?php echo $clientName . ' ' . $clientSurname; ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="header-menu-container">
        <nav class="header-nav">
            <a class="header-logo"  href="index.php">
                <svg class="header-logo-icon" xmlns="http://www.w3.org/2000/svg" width="241" height="92" viewBox="0 0 241 92" fill="none">
                    <path d="M83.6336 66.6455C85.5077 66.6455 86.4448 67.2516 86.4448 68.4639C86.4448 69.3314 86.116 69.9988 85.4584 70.4659C84.8118 70.933 83.6391 71.1666 81.9403 71.1666H76.3835C76.4383 69.8987 76.4657 68.3805 76.4657 66.6121C76.4657 64.8437 76.4383 63.3256 76.3835 62.0577H79.7866V62.0744H81.9238C82.9541 62.0744 83.7596 62.1634 84.3405 62.3413C84.9323 62.5081 85.3488 62.7528 85.5899 63.0754C85.8311 63.3979 85.9516 63.8094 85.9516 64.3099C85.9516 65.4332 85.1789 66.2117 83.6336 66.6455ZM79.7866 64.3599V65.5277H81.0361C81.5402 65.5277 81.8964 65.4833 82.1047 65.3943C82.3129 65.2942 82.417 65.1218 82.417 64.8771C82.417 64.688 82.3074 64.5546 82.0882 64.4767C81.88 64.3989 81.5293 64.3599 81.0361 64.3599H79.7866ZM81.0361 68.8643C81.6608 68.8643 82.0882 68.8198 82.3184 68.7309C82.5485 68.6308 82.6636 68.4584 82.6636 68.2137C82.6636 68.0024 82.5431 67.8467 82.3019 67.7466C82.0608 67.6465 81.6389 67.5964 81.0361 67.5964H79.7866V68.8643H81.0361ZM90.9271 66.6121C90.949 67.1794 91.1244 67.6354 91.453 67.9801C91.7928 68.3249 92.2914 68.4973 92.949 68.4973C93.3107 68.4973 93.634 68.4306 93.919 68.2971C94.204 68.1525 94.467 67.9412 94.7081 67.6632C95.6397 68.0302 96.7302 68.3472 97.9797 68.6141C97.618 69.4705 97.0042 70.1378 96.1384 70.616C95.2835 71.0943 94.1711 71.3334 92.8011 71.3334C90.9709 71.3334 89.6174 70.8996 88.7406 70.0321C87.8638 69.1646 87.4254 68.0246 87.4254 66.6121C87.4254 65.1997 87.8638 64.0596 88.7406 63.1921C89.6174 62.3246 90.9709 61.8909 92.8011 61.8909C94.1711 61.8909 95.2835 62.13 96.1384 62.6082C97.0042 63.0865 97.618 63.7538 97.9797 64.6102C97.0042 64.8215 95.9137 65.1385 94.7081 65.5611C94.467 65.2831 94.204 65.0773 93.919 64.9438C93.634 64.7993 93.3107 64.727 92.949 64.727C92.2914 64.727 91.7928 64.8994 91.453 65.2441C91.1244 65.5889 90.949 66.0449 90.9271 66.6121ZM107.91 66.9958H102.551C102.639 67.4852 102.852 67.8856 103.192 68.197C103.543 68.4973 104.036 68.6475 104.672 68.6475C105.132 68.6475 105.565 68.5585 105.97 68.3805C106.376 68.1915 106.699 67.9301 106.94 67.5964C107.675 67.9635 108.65 68.3583 109.867 68.7809C109.527 69.5595 108.908 70.1823 108.009 70.6494C107.121 71.1054 105.959 71.3334 104.524 71.3334C102.661 71.3334 101.28 70.8996 100.381 70.0321C99.4931 69.1535 99.0492 68.0024 99.0492 66.5788C99.0492 65.1885 99.4931 64.0596 100.381 63.1921C101.269 62.3246 102.65 61.8909 104.524 61.8909C105.642 61.8909 106.612 62.0799 107.434 62.4581C108.256 62.8362 108.886 63.3645 109.324 64.043C109.763 64.7103 109.982 65.4777 109.982 66.3452C109.982 66.6344 109.971 66.8513 109.949 66.9958H107.91ZM104.754 64.41C104.206 64.41 103.757 64.5156 103.406 64.727C103.055 64.9383 102.808 65.2274 102.666 65.5945H106.546C106.414 65.2386 106.201 64.955 105.905 64.7436C105.609 64.5212 105.225 64.41 104.754 64.41ZM114.628 71.1666H111.126C111.181 69.8875 111.208 68.3694 111.208 66.6121C111.208 64.8437 111.181 63.3256 111.126 62.0577H120.365C120.355 62.447 120.349 63.2255 120.349 64.3933V65.2942H114.628V71.1666ZM131.274 68.7976H131.768V73.2853H128.66V71.4669H123.465V73.3187H120.358V68.7976H120.901C121.273 68.742 121.553 68.603 121.739 68.3805C121.936 68.1581 122.09 67.7911 122.199 67.2795C122.276 66.8791 122.315 65.9226 122.315 64.41V62.0577L131.274 62.0744V68.7976ZM125.109 68.2471C125.087 68.3694 125.044 68.5529 124.978 68.7976H128.134V64.8771H125.339V65.3776C125.329 66.0227 125.312 66.5343 125.29 66.9124C125.268 67.2795 125.208 67.7243 125.109 68.2471ZM143.627 68.3972C143.627 68.7754 143.671 69.0423 143.759 69.198C143.846 69.3537 144.022 69.4649 144.285 69.5316L144.219 71.0998C143.868 71.1555 143.561 71.1944 143.298 71.2166C143.046 71.2389 142.707 71.25 142.279 71.25C141.337 71.25 140.712 71.0387 140.405 70.616C140.098 70.1823 139.945 69.6317 139.945 68.9644V68.5474C139.627 69.4594 139.161 70.1378 138.547 70.5827C137.944 71.0276 137.172 71.25 136.229 71.25C135.133 71.25 134.306 71.0276 133.747 70.5827C133.199 70.1378 132.925 69.476 132.925 68.5974C132.925 67.8745 133.16 67.3073 133.632 66.8957C134.103 66.4842 134.832 66.2006 135.818 66.0449C134.985 65.4443 134.152 64.9216 133.319 64.4767C133.977 63.6315 134.728 62.9919 135.572 62.5582C136.415 62.1133 137.44 61.8909 138.646 61.8909C140.334 61.8909 141.583 62.2635 142.394 63.0086C143.216 63.7427 143.627 64.8771 143.627 66.4119V68.3972ZM138.991 64.8271C138.432 64.8271 137.939 64.9216 137.511 65.1107C137.084 65.2997 136.7 65.5834 136.361 65.9615C137.018 65.8836 137.84 65.8447 138.827 65.8447C139.232 65.8447 139.517 65.7947 139.682 65.6946C139.846 65.5945 139.928 65.4666 139.928 65.3109C139.928 65.1774 139.846 65.0662 139.682 64.9772C139.517 64.8771 139.287 64.8271 138.991 64.8271ZM137.265 68.5307C137.879 68.5307 138.432 68.4139 138.925 68.1803C139.419 67.9356 139.758 67.6187 139.945 67.2294V66.7956C139.682 66.9291 139.391 67.0348 139.073 67.1126C138.755 67.1794 138.372 67.2461 137.922 67.3128L137.331 67.4129C136.717 67.5353 136.41 67.7466 136.41 68.0469C136.41 68.3694 136.695 68.5307 137.265 68.5307ZM158.923 62.0577C158.868 63.7482 158.841 65.2664 158.841 66.6121C158.841 67.9579 158.868 69.476 158.923 71.1666H155.355V68.1303H152.133V71.1666H148.566C148.62 69.8987 148.648 68.3805 148.648 66.6121C148.648 64.8437 148.62 63.3256 148.566 62.0577H152.133V65.094H155.355V62.0577H158.923ZM170.775 68.3972C170.775 68.7754 170.819 69.0423 170.907 69.198C170.994 69.3537 171.17 69.4649 171.433 69.5316L171.367 71.0998C171.016 71.1555 170.709 71.1944 170.446 71.2166C170.194 71.2389 169.854 71.25 169.427 71.25C168.484 71.25 167.86 71.0387 167.553 70.616C167.246 70.1823 167.093 69.6317 167.093 68.9644V68.5474C166.775 69.4594 166.309 70.1378 165.695 70.5827C165.092 71.0276 164.32 71.25 163.377 71.25C162.281 71.25 161.454 71.0276 160.895 70.5827C160.347 70.1378 160.073 69.476 160.073 68.5974C160.073 67.8745 160.308 67.3073 160.78 66.8957C161.251 66.4842 161.98 66.2006 162.966 66.0449C162.133 65.4443 161.3 64.9216 160.467 64.4767C161.125 63.6315 161.876 62.9919 162.719 62.5582C163.563 62.1133 164.588 61.8909 165.794 61.8909C167.482 61.8909 168.731 62.2635 169.542 63.0086C170.364 63.7427 170.775 64.8771 170.775 66.4119V68.3972ZM166.139 64.8271C165.58 64.8271 165.087 64.9216 164.659 65.1107C164.232 65.2997 163.848 65.5834 163.509 65.9615C164.166 65.8836 164.988 65.8447 165.975 65.8447C166.38 65.8447 166.665 65.7947 166.829 65.6946C166.994 65.5945 167.076 65.4666 167.076 65.3109C167.076 65.1774 166.994 65.0662 166.829 64.9772C166.665 64.8771 166.435 64.8271 166.139 64.8271ZM164.413 68.5307C165.027 68.5307 165.58 68.4139 166.073 68.1803C166.566 67.9356 166.906 67.6187 167.093 67.2294V66.7956C166.829 66.9291 166.539 67.0348 166.221 67.1126C165.903 67.1794 165.52 67.2461 165.07 67.3128L164.479 67.4129C163.865 67.5353 163.558 67.7466 163.558 68.0469C163.558 68.3694 163.843 68.5307 164.413 68.5307ZM179.051 66.6121C179.073 67.1794 179.248 67.6354 179.577 67.9801C179.917 68.3249 180.415 68.4973 181.073 68.4973C181.435 68.4973 181.758 68.4306 182.043 68.2971C182.328 68.1525 182.591 67.9412 182.832 67.6632C183.764 68.0302 184.854 68.3472 186.104 68.6141C185.742 69.4705 185.128 70.1378 184.262 70.616C183.407 71.0943 182.295 71.3334 180.925 71.3334C179.095 71.3334 177.741 70.8996 176.864 70.0321C175.987 69.1646 175.549 68.0246 175.549 66.6121C175.549 65.1997 175.987 64.0596 176.864 63.1921C177.741 62.3246 179.095 61.8909 180.925 61.8909C182.295 61.8909 183.407 62.13 184.262 62.6082C185.128 63.0865 185.742 63.7538 186.104 64.6102C185.128 64.8215 184.038 65.1385 182.832 65.5611C182.591 65.2831 182.328 65.0773 182.043 64.9438C181.758 64.7993 181.435 64.727 181.073 64.727C180.415 64.727 179.917 64.8994 179.577 65.2441C179.248 65.5889 179.073 66.0449 179.051 66.6121ZM194.588 66.6455C196.462 66.6455 197.399 67.2516 197.399 68.4639C197.399 69.3314 197.07 69.9988 196.412 70.4659C195.766 70.933 194.593 71.1666 192.894 71.1666H187.337C187.392 69.8987 187.42 68.3805 187.42 66.6121C187.42 64.8437 187.392 63.3256 187.337 62.0577H190.741V62.0744H192.878C193.908 62.0744 194.714 62.1634 195.294 62.3413C195.886 62.5081 196.303 62.7528 196.544 63.0754C196.785 63.3979 196.906 63.8094 196.906 64.3099C196.906 65.4332 196.133 66.2117 194.588 66.6455ZM190.741 64.3599V65.5277H191.99C192.494 65.5277 192.85 65.4833 193.059 65.3943C193.267 65.2942 193.371 65.1218 193.371 64.8771C193.371 64.688 193.261 64.5546 193.042 64.4767C192.834 64.3989 192.483 64.3599 191.99 64.3599H190.741ZM191.99 68.8643C192.615 68.8643 193.042 68.8198 193.272 68.7309C193.502 68.6308 193.618 68.4584 193.618 68.2137C193.618 68.0024 193.497 67.8467 193.256 67.7466C193.015 67.6465 192.593 67.5964 191.99 67.5964H190.741V68.8643H191.99ZM208.622 62.0577C208.567 63.7482 208.54 65.2664 208.54 66.6121C208.54 67.9579 208.567 69.476 208.622 71.1666H205.383V68.2637H204.051C203.295 69.476 202.725 70.4436 202.342 71.1666H198.478C199.355 69.9543 200.095 68.9088 200.698 68.0302C199.854 67.8077 199.256 67.4463 198.906 66.9458C198.555 66.4453 198.38 65.828 198.38 65.094C198.38 64.1709 198.708 63.4368 199.366 62.8918C200.024 62.3469 201.185 62.0744 202.851 62.0744H205.383V62.0577H208.622ZM205.383 65.9115V64.4767H203.904C203.103 64.4767 202.561 64.5268 202.276 64.6269C202.002 64.727 201.865 64.9105 201.865 65.1774C201.865 65.4555 202.007 65.6501 202.292 65.7613C202.588 65.8614 203.125 65.9115 203.904 65.9115H205.383ZM217.16 66.4954C217.269 66.4842 217.423 66.4787 217.62 66.4787C218.354 66.4787 218.891 66.6511 219.231 66.9958C219.582 67.3406 219.757 67.7466 219.757 68.2137C219.757 69.2036 219.313 69.971 218.426 70.5159C217.538 71.0609 216.398 71.3334 215.006 71.3334C213.789 71.3334 212.715 71.1221 211.784 70.6995C210.863 70.2768 210.195 69.565 209.778 68.564L210.485 68.3805C211.504 68.1247 212.261 67.88 212.754 67.6465C212.929 67.9913 213.176 68.2582 213.493 68.4473C213.811 68.6252 214.233 68.7142 214.759 68.7142C215.351 68.7142 215.801 68.6586 216.107 68.5474C216.425 68.4361 216.584 68.2526 216.584 67.9968C216.584 67.7966 216.409 67.6576 216.058 67.5797C215.707 67.5019 215.203 67.463 214.546 67.463H213.609V65.2608H214.168C214.77 65.2608 215.253 65.233 215.614 65.1774C215.976 65.1218 216.157 64.9995 216.157 64.8104C216.157 64.6213 216.053 64.4712 215.844 64.3599C215.636 64.2487 215.357 64.1931 215.006 64.1931C214.436 64.1931 213.987 64.2988 213.658 64.5101C213.34 64.7214 213.039 65.0662 212.754 65.5444C211.888 65.1774 210.94 64.916 209.91 64.7603C210.754 62.8585 212.507 61.9075 215.17 61.9075C215.949 61.9075 216.661 62.0243 217.308 62.2579C217.954 62.4803 218.464 62.7917 218.837 63.1921C219.209 63.5814 219.395 64.0207 219.395 64.5101C219.395 65.5778 218.65 66.2395 217.16 66.4954ZM231.199 62.0577C231.145 63.6704 231.117 65.1274 231.117 66.4286C231.117 67.7076 231.145 69.1646 231.199 70.7996H227.829L228.01 65.0273L225.495 70.7996H220.908C220.974 69.3315 221.007 67.8745 221.007 66.4286C221.007 64.9605 220.974 63.5036 220.908 62.0577H224.147L223.982 67.9801L226.498 62.0577H231.199ZM234.094 71.3334C233.612 71.3334 233.201 71.161 232.861 70.8162C232.521 70.4603 232.351 70.0377 232.351 69.5483C232.351 69.059 232.521 68.6419 232.861 68.2971C233.201 67.9523 233.612 67.7799 234.094 67.7799C234.565 67.7799 234.971 67.9579 235.31 68.3138C235.661 68.6586 235.836 69.0701 235.836 69.5483C235.836 70.0377 235.661 70.4603 235.31 70.8162C234.971 71.161 234.565 71.3334 234.094 71.3334Z" fill="black"/>
                    <path d="M94.4351 47.6666H83.7158C83.8912 48.6453 84.3186 49.4461 84.9982 50.069C85.6996 50.6695 86.686 50.9698 87.9574 50.9698C88.878 50.9698 89.7438 50.7919 90.5549 50.436C91.3663 50.0578 92.0129 49.5351 92.4952 48.8678C93.9638 49.6018 95.9147 50.3915 98.3478 51.2368C97.6683 52.7938 96.4298 54.0395 94.6324 54.9737C92.8568 55.8857 90.533 56.3417 87.6614 56.3417C83.935 56.3417 81.1731 55.4742 79.3757 53.7392C77.6002 51.9819 76.7124 49.6797 76.7124 46.8325C76.7124 44.052 77.6002 41.7942 79.3757 40.0592C81.1512 38.3242 83.9131 37.4567 87.6614 37.4567C89.8973 37.4567 91.8376 37.8348 93.4816 38.5911C95.1256 39.3474 96.386 40.404 97.2628 41.7609C98.1396 43.0955 98.578 44.6303 98.578 46.3653C98.578 46.9437 98.556 47.3774 98.5122 47.6666H94.4351ZM88.1218 42.4949C87.0258 42.4949 86.127 42.7062 85.4256 43.1289C84.7242 43.5515 84.231 44.1298 83.946 44.8639H91.706C91.443 44.1521 91.0152 43.5849 90.4234 43.1622C89.8315 42.7173 89.0643 42.4949 88.1218 42.4949ZM121.351 56.1749H114.15V44.0965H109.481L109.448 45.1308C109.427 46.1985 109.383 47.1439 109.317 47.9669C109.273 48.7677 109.164 49.6352 108.988 50.5694C108.243 54.6401 105.207 56.6754 99.8804 56.6754V51.3702C100.911 51.2368 101.612 50.9142 101.985 50.4026C102.357 49.8688 102.544 49.0457 102.544 47.9335V37.7903L121.351 37.8237V56.1749ZM141.379 47.6666H130.66C130.835 48.6453 131.263 49.4461 131.942 50.069C132.643 50.6695 133.63 50.9698 134.901 50.9698C135.822 50.9698 136.688 50.7919 137.499 50.436C138.31 50.0578 138.956 49.5351 139.439 48.8678C140.907 49.6018 142.858 50.3915 145.291 51.2368C144.612 52.7938 143.373 54.0395 141.576 54.9737C139.8 55.8857 137.477 56.3417 134.605 56.3417C130.879 56.3417 128.117 55.4742 126.32 53.7392C124.544 51.9819 123.656 49.6797 123.656 46.8325C123.656 44.052 124.544 41.7942 126.32 40.0592C128.095 38.3242 130.857 37.4567 134.605 37.4567C136.841 37.4567 138.781 37.8348 140.425 38.5911C142.069 39.3474 143.329 40.404 144.206 41.7609C145.083 43.0955 145.521 44.6303 145.521 46.3653C145.521 46.9437 145.5 47.3774 145.456 47.6666H141.379ZM135.066 42.4949C133.97 42.4949 133.071 42.7062 132.369 43.1289C131.668 43.5515 131.175 44.1298 130.89 44.8639H138.65C138.387 44.1521 137.959 43.5849 137.367 43.1622C136.775 42.7173 136.008 42.4949 135.066 42.4949ZM164.612 47.2996C165.598 48.1004 166.355 49.0013 166.881 50.0022C167.407 51.0032 167.9 52.2044 168.36 53.6057C168.821 54.8292 169.149 55.6299 169.347 56.0081H161.423C161.357 55.8524 161.225 55.363 161.028 54.54C160.853 53.717 160.677 53.0608 160.502 52.5714C160.327 52.0598 160.064 51.6149 159.713 51.2368C159.165 50.6362 158.551 50.2469 157.872 50.069C157.192 49.891 156.184 49.802 154.847 49.802V56.0081H147.81C147.92 53.4723 147.975 50.436 147.975 46.8992C147.975 43.3624 147.92 40.3261 147.81 37.7903H154.847V44.33H154.978C157.587 41.683 159.483 39.5031 160.666 37.7903H169.347L161.653 45.5646C162.793 46.0539 163.779 46.6323 164.612 47.2996ZM181.259 56.3417C177.511 56.3417 174.749 55.4742 172.974 53.7392C171.22 51.9819 170.343 49.6797 170.343 46.8325C170.343 44.052 171.22 41.7942 172.974 40.0592C174.749 38.3242 177.511 37.4567 181.259 37.4567C183.78 37.4567 185.884 37.8793 187.572 38.7246C189.282 39.5476 190.542 40.6709 191.353 42.0945C192.165 43.4959 192.57 45.0752 192.57 46.8325C192.57 49.7019 191.627 52.0042 189.742 53.7392C187.879 55.4742 185.051 56.3417 181.259 56.3417ZM181.391 50.6695C182.816 50.6695 183.879 50.3248 184.58 49.6352C185.282 48.9456 185.632 48.0225 185.632 46.8658C185.632 45.7759 185.282 44.8861 184.58 44.1966C183.879 43.4848 182.816 43.1289 181.391 43.1289C179.966 43.1289 178.925 43.4736 178.267 44.1632C177.61 44.8528 177.281 45.7536 177.281 46.8658C177.281 48.0225 177.61 48.9456 178.267 49.6352C178.925 50.3248 179.966 50.6695 181.391 50.6695ZM219.97 37.7903L221.219 56.0081H214.841L214.446 42.962L211.947 56.0081H204.155L201.623 43.1289L201.261 56.0081H194.883L196.132 37.7903H205.865L208.002 49.7019L209.909 37.7903H219.97ZM227.002 56.3417C226.037 56.3417 225.215 55.997 224.536 55.3074C223.856 54.5956 223.516 53.7503 223.516 52.7716C223.516 51.7929 223.856 50.9587 224.536 50.2692C225.215 49.5796 226.037 49.2348 227.002 49.2348C227.944 49.2348 228.755 49.5907 229.435 50.3025C230.136 50.9921 230.487 51.8151 230.487 52.7716C230.487 53.7503 230.136 54.5956 229.435 55.3074C228.755 55.997 227.944 56.3417 227.002 56.3417Z" fill="black"/>
                    <path d="M18.7052 69.5767H8.12598V87.7674H18.7052V69.5767Z" fill="black"/>
                    <path d="M33.7389 51.3856H23.1597V87.7673H33.7389V51.3856Z" fill="black"/>
                    <path d="M63.806 15.0037H53.2268V87.7674H63.806V15.0037Z" fill="black"/>
                    <path d="M76.0345 4.09802H40.6677V14.9822H76.0345V4.09802Z" fill="black"/>
                    <path d="M48.7723 33.1946H38.1931V87.7673H48.7723V33.1946Z" fill="black"/>
                </svg>
            </a>
            <ul class="header-menu">
                <li class="header-item">
                    <a class="header-link" href="internet.php">Интернет</a>
                </li>
                <li class="header-item">
                    <a class="header-link" href="television.php">Телевидение</a>
                </li>
                <li class="header-item">
                    <a  class="header-link" href="mobile.php">Мобильная связь</a>
                </li>
            </ul>
        </nav>
        <div class="header-button-container">
        <button class="button header-button" id="openPaymentModalHeader">Пополнить</button></div>
        </div>
    </header>
    <main class="main-container main-page">
        <h1 class="visually-hidden">Личный кабинет абонента телекоммуникационной компании "Телеком"</h1>
        <section>
            <header class="title-container title-container-account">
                <h2 class="page-title">Ваш личный кабинет</h2>
            </header>
        </section>
        <section class="account-screen">
            <div class="account-welcome">
                <div class="account-welcome-section">
                <p class="account-welcome-text">Добро пожаловать, <?php echo $clientName . ' ' . $clientPatr; ?>!</p>
                </div>
                <div class="account-photo-section">
                    <img class="account-user-photo" src="src/img/icons/profilepic.svg" alt="Ваше фото">
                    <!-- <button class="change-photo-button">Изменить фото</button> функция отключена для более быстрого запуска проекта, фото пользователя было заменено картинкой -->
                </div>
            </div>
            <div class="abonent-info">
                <form action="#" method="post" class="profile-form">
                    <label class="account-form-label" for="last-name">Ваша фамилия:</label>
                    <input class="account-form-input" type="text" id="last-name" name="last-name" value="<?php echo $clientSurname; ?>" disabled>
                    <label class="account-form-label" for="first-name">Ваше имя:</label>
                    <input class="account-form-input" type="text" id="first-name" name="first-name" value="<?php echo $clientName; ?>" disabled>
                    <label class="account-form-label" for="patronymic">Ваше отчество:</label>
                    <input class="account-form-input" type="text" id="patronymic" name="patronymic" value="<?php echo $clientPatr; ?>" disabled>
                    <label class="account-form-label" for="phone">Ваш номер телефона:</label>
                    <input class="account-form-input" type="tel" id="phone" name="phone" value="<?php echo $clientPhone; ?>" disabled>
                    <label class="account-form-label account-form-textarea-label" for="address">Ваш домашний адрес:</label>
                    <textarea class="account-form-input account-form-textarea" id="address" name="address" disabled><?php echo htmlspecialchars($addressString); ?></textarea>
                    <label class="account-form-label balance" for="balance">Ваш баланс счёта:</label>
                    <input class="account-form-input" type="text" id="balance" name="balance" value="<?php echo $invoiceBalance . ' рублей'; ?>" disabled>
                    <button class="payment-button" id="openPaymentModal">Пополнить</button>
                </form>
                

<!-- Модальное окно оплаты -->
<div id="paymentModal" class="modal-payment">
    <div class="modal-content-payment">
        <span class="close-payment">&times;</span>
        <h2 class="modal-payment-title">Оплата</h2>
        <form id="paymentForm" class="modal-payment-form">
        <div class="modal-payment-columns">
            <div class="modal-payment-column">
                <label class="modal-payment-label" for="account-number">Ваш номер лицевого счета:</label>
                <input class="modal-payment-input" type="text" id="account-number" name="account-number" value="<?php echo $invoiceNumber; ?>" disabled>
                <label class="modal-payment-label" for="balance">Ваш баланс лицевого счета:</label>
                <input class="modal-payment-input" type="text" id="balance" name="balance" value="<?php echo $invoiceBalance . ' рублей'; ?>" disabled>
                <label class="modal-payment-label" for="payment-amount">Введите сумму для оплаты:</label>
                <input class="modal-payment-input" type="text" id="payment-amount" name="payment-amount" placeholder="500 рублей" required>
            </div>
            <div class="modal-payment-column">
                <p class="modal-payment-subtitle">Введите платежные данные:</p>
                <label class="modal-payment-label visually-hidden" for="card-number">Номер карты:</label>
                <input class="modal-payment-input card-number" type="text" id="card-number" name="card-number" placeholder="4402 3133 9079 4220" required>
                <div class="modal-payment-row">
                    <label class="modal-payment-label visually-hidden" for="expiry-date">Срок действия:</label>
                    <input class="modal-payment-input half-width" type="text" id="expiry-date" name="expiry-date" placeholder="12/27" required>
                    <label class="modal-payment-label visually-hidden" for="cvv">CVC/CVV:</label>
                    <input class="modal-payment-input half-width" type="text" id="cvv" name="cvv" placeholder="799" required>
                </div>
                <label class="modal-payment-label visually-hidden" for="cardholder-name">Имя на карте:</label>
                <input class="modal-payment-input" type="text" id="cardholder-name" name="cardholder-name" placeholder="IVAN IVANOV" required>
            </div>
        </div>
        <button class="modal-payment-button">Оплатить</button>
        </form>
    </div>
</div>
</div>
        
        <h3 class="account-title">Ваши тарифы и услуги</h3>
            <section class="account-connections">
                <h4 class="account-subtitle">Интернет</h4>
                <div class="account-tariff-screen">
                    <form action="#" method="post" class="profile-form">
                        <label class="account-form-label-tariff" for="internet-tariff">Ваш тариф:</label>
                        <input class="account-form-input-tariff" type="text" id="internet-tariff" name="internet-tariff" value="<?php echo $internetTariff; ?>" disabled>
                        
                        <label class="account-form-textarea-label-address" for="internet-address">Ваш адрес подключения:</label>
                    <textarea class="account-form-input-tariff account-form-textarea-address" id="internet-address" name="internet-address" disabled><?php echo htmlspecialchars($addressString); ?></textarea>
                    <label class="account-form-textarea-label-services" for="account-services">Активированные услуги:</label>
                    <textarea class="account-form-input-tariff account-form-textarea-services" id="account-services" name="account-services" rows="4" disabled><?php
            // Проверяем, есть ли активированные услуги интернета
            if (!empty($activatedServices['Интернет'])) {
                // Создаем строку с перечислением активированных услуг интернета
                foreach ($activatedServices['Интернет'] as $service) {
                    echo "- $service\n"; // Каждая услуга на отдельной строке с черточкой перед названием
                }
            } else {
                echo "Нет активированных услуг"; // Выводим сообщение, если нет активированных услуг интернета
            }
            ?></textarea>
                    </form>
                </div>
            </section>
            <section class="account-connections">
                <h4 class="account-subtitle">Телевидение</h4>
                <div class="account-tariff-screen">
                    <form action="#" method="post" class="profile-form">
                        <label class="account-form-label-tariff" for="internet-tariff">Ваш тариф:</label>
                        <input class="account-form-input-tariff" type="text" id="internet-tariff" name="internet-tariff" value="<?php echo $televisionTariff; ?>" disabled>
                        
                        <label class="account-form-textarea-label-address" for="internet-address">Ваш адрес подключения:</label>
                    <textarea class="account-form-input-tariff account-form-textarea-address" id="internet-address" name="internet-address" disabled><?php echo htmlspecialchars($addressString); ?></textarea>
                    <label class="account-form-textarea-label-services" for="account-services">Активированные услуги:</label>
                    <textarea class="account-form-input-tariff account-form-textarea-services" id="account-services" name="account-services" rows="4" disabled><?php
// Проверяем, есть ли активированные услуги телевидения
if (!empty($activatedServices['Телевидение'])) {
    // Создаем строку с перечислением активированных услуг телевидения
    $tvServicesList = '';
    foreach ($activatedServices['Телевидение'] as $tvService) {
        $tvServicesList .= "- $tvService\n";
    }
    echo $tvServicesList; // Выводим список услуг телевидения в поле textarea
} else {
    echo "Нет активированных услуг телевидения"; // Выводим сообщение, если нет активированных услуг телевидения
}
?>

                    </textarea>
                    </form>
                </div>
            </section>
            <section class="account-connections">
                <h4 class="account-subtitle">Мобильная связь</h4>
                <div class="account-tariff-screen">
                    <form action="#" method="post" class="profile-form">
                        <label class="account-form-label-tariff" for="internet-tariff">Ваш тариф:</label>
                        <input class="account-form-input-tariff" type="text" id="internet-tariff" name="internet-tariff" value="<?php echo $mobileTariff; ?>" disabled>
                        
                        <label class="account-form-textarea-label-address" for="internet-address">Ваш номер мобильного телефона сим-карты:</label>
                    <textarea class="account-form-input-tariff account-form-textarea-address" id="internet-address" name="internet-address" disabled><?php echo $clientPhone; ?></textarea>
                    <label class="account-form-textarea-label-services" for="account-services">Активированные услуги:</label>
                    <textarea class="account-form-input-tariff account-form-textarea-services" id="account-services" name="account-services" rows="4" disabled><?php
// Проверяем, есть ли активированные услуги мобильной связи
if (!empty($activatedServices['Мобильная связь'])) {
    // Создаем строку с перечислением активированных услуг мобильной связи
    $mobileServicesList = '';
    foreach ($activatedServices['Мобильная связь'] as $mobileService) {
        $mobileServicesList .= "- $mobileService\n";
    }
    echo $mobileServicesList; // Выводим список услуг мобильной связи в поле textarea
} else {
    echo "Нет активированных услуг мобильной связи"; // Выводим сообщение, если нет активированных услуг мобильной связи
}
?>
</textarea>
                    </form>
                </div>
            </section>
        </section>
    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-about">
                <a class="footer-logo-link" href="index.php">
                <svg class="header-logo-icon" xmlns="http://www.w3.org/2000/svg" width="289" height="103" viewBox="0 0 289 103" fill="none">
                    <path d="M98.8951 72.1676C101.111 72.1676 102.219 72.7697 102.219 73.974C102.219 74.8358 101.83 75.4988 101.053 75.9628C100.288 76.4269 98.9015 76.6589 96.8927 76.6589H90.322C90.3868 75.3993 90.4192 73.8912 90.4192 72.1344C90.4192 70.3777 90.3868 68.8695 90.322 67.6099H94.3461V67.6265H96.8733C98.0915 67.6265 99.0441 67.7149 99.731 67.8917C100.431 68.0574 100.923 68.3005 101.208 68.6209C101.494 68.9413 101.636 69.3501 101.636 69.8473C101.636 70.9633 100.722 71.7367 98.8951 72.1676ZM94.3461 69.897V71.0572H95.8235C96.4197 71.0572 96.8409 71.013 97.0871 70.9246C97.3334 70.8251 97.4565 70.6539 97.4565 70.4108C97.4565 70.223 97.3269 70.0904 97.0677 70.0131C96.8215 69.9357 96.4067 69.897 95.8235 69.897H94.3461ZM95.8235 74.3718C96.5623 74.3718 97.0677 74.3276 97.3399 74.2392C97.612 74.1398 97.7481 73.9685 97.7481 73.7254C97.7481 73.5155 97.6055 73.3608 97.3204 73.2614C97.0353 73.162 96.5363 73.1122 95.8235 73.1122H94.3461V74.3718H95.8235ZM107.52 72.1344C107.545 72.6979 107.753 73.1509 108.141 73.4934C108.543 73.8359 109.133 74.0072 109.91 74.0072C110.338 74.0072 110.72 73.9409 111.057 73.8083C111.394 73.6647 111.705 73.4547 111.99 73.1785C113.092 73.5431 114.382 73.858 115.859 74.1232C115.431 74.974 114.706 75.6369 113.682 76.112C112.671 76.5871 111.355 76.8246 109.735 76.8246C107.571 76.8246 105.971 76.3937 104.934 75.5319C103.897 74.6701 103.379 73.5376 103.379 72.1344C103.379 70.7312 103.897 69.5987 104.934 68.7369C105.971 67.8751 107.571 67.4442 109.735 67.4442C111.355 67.4442 112.671 67.6818 113.682 68.1569C114.706 68.632 115.431 69.2949 115.859 70.1456C114.706 70.3556 113.416 70.6705 111.99 71.0903C111.705 70.8141 111.394 70.6097 111.057 70.4771C110.72 70.3335 110.338 70.2617 109.91 70.2617C109.133 70.2617 108.543 70.4329 108.141 70.7754C107.753 71.1179 107.545 71.5709 107.52 72.1344ZM127.602 72.5156H121.264C121.368 73.0017 121.621 73.3995 122.023 73.7089C122.437 74.0072 123.021 74.1563 123.772 74.1563C124.317 74.1563 124.828 74.068 125.308 73.8912C125.787 73.7033 126.17 73.4437 126.455 73.1122C127.323 73.4768 128.477 73.8691 129.915 74.2889C129.513 75.0623 128.781 75.6811 127.719 76.1451C126.669 76.5981 125.295 76.8246 123.597 76.8246C121.394 76.8246 119.761 76.3937 118.698 75.5319C117.649 74.6591 117.124 73.5155 117.124 72.1013C117.124 70.7202 117.649 69.5987 118.698 68.7369C119.748 67.8751 121.381 67.4442 123.597 67.4442C124.919 67.4442 126.066 67.632 127.038 68.0077C128.01 68.3834 128.755 68.9082 129.274 69.5822C129.792 70.2451 130.051 71.0074 130.051 71.8693C130.051 72.1565 130.038 72.372 130.012 72.5156H127.602ZM123.869 69.9468C123.221 69.9468 122.69 70.0517 122.275 70.2617C121.861 70.4716 121.569 70.7588 121.401 71.1235H125.988C125.833 70.7699 125.58 70.4881 125.23 70.2782C124.88 70.0572 124.427 69.9468 123.869 69.9468ZM135.545 76.6589H131.405C131.469 75.3883 131.502 73.8801 131.502 72.1344C131.502 70.3777 131.469 68.8695 131.405 67.6099H142.33C142.317 67.9967 142.31 68.7701 142.31 69.9302V70.8251H135.545V76.6589ZM155.229 74.3055H155.813V78.7637H152.138V76.9572H145.995V78.7968H142.321V74.3055H142.963C143.403 74.2503 143.734 74.1122 143.954 73.8912C144.187 73.6702 144.369 73.3056 144.498 72.7973C144.589 72.3996 144.635 71.4494 144.635 69.9468V67.6099L155.229 67.6265V74.3055ZM147.939 73.7586C147.913 73.8801 147.862 74.0624 147.784 74.3055H151.516V70.4108H148.212V70.908C148.199 71.5488 148.179 72.0571 148.153 72.4327C148.127 72.7973 148.056 73.2393 147.939 73.7586ZM169.836 73.9077C169.836 74.2834 169.888 74.5486 169.992 74.7033C170.096 74.8579 170.303 74.9684 170.614 75.0347L170.536 76.5926C170.121 76.6478 169.759 76.6865 169.448 76.7086C169.149 76.7307 168.748 76.7418 168.242 76.7418C167.128 76.7418 166.389 76.5318 166.026 76.112C165.663 75.6811 165.482 75.1342 165.482 74.4712V74.0569C165.106 74.9629 164.555 75.6369 163.829 76.0788C163.117 76.5208 162.203 76.7418 161.088 76.7418C159.792 76.7418 158.814 76.5208 158.153 76.0788C157.505 75.6369 157.181 74.9795 157.181 74.1066C157.181 73.3885 157.46 72.825 158.017 72.4162C158.574 72.0074 159.436 71.7256 160.602 71.5709C159.617 70.9743 158.632 70.455 157.647 70.0131C158.425 69.1733 159.313 68.538 160.311 68.1071C161.309 67.6652 162.52 67.4442 163.946 67.4442C165.942 67.4442 167.419 67.8143 168.378 68.5546C169.35 69.2838 169.836 70.4108 169.836 71.9355V73.9077ZM164.354 70.3611C163.693 70.3611 163.11 70.455 162.605 70.6428C162.099 70.8307 161.646 71.1124 161.244 71.4881C162.021 71.4107 162.993 71.3721 164.16 71.3721C164.639 71.3721 164.976 71.3223 165.171 71.2229C165.365 71.1235 165.462 70.9964 165.462 70.8417C165.462 70.7091 165.365 70.5986 165.171 70.5103C164.976 70.4108 164.704 70.3611 164.354 70.3611ZM162.313 74.0403C163.039 74.0403 163.693 73.9243 164.277 73.6923C164.86 73.4492 165.261 73.1343 165.482 72.7476V72.3167C165.171 72.4493 164.827 72.5543 164.451 72.6316C164.076 72.6979 163.622 72.7642 163.091 72.8305L162.391 72.9299C161.665 73.0515 161.302 73.2614 161.302 73.5597C161.302 73.8801 161.639 74.0403 162.313 74.0403ZM187.923 67.6099C187.858 69.2894 187.826 70.7975 187.826 72.1344C187.826 73.4713 187.858 74.9795 187.923 76.6589H183.705V73.6426H179.894V76.6589H175.676C175.741 75.3993 175.773 73.8912 175.773 72.1344C175.773 70.3777 175.741 68.8695 175.676 67.6099H179.894V70.6263H183.705V67.6099H187.923ZM201.938 73.9077C201.938 74.2834 201.99 74.5486 202.094 74.7033C202.197 74.8579 202.405 74.9684 202.716 75.0347L202.638 76.5926C202.223 76.6478 201.861 76.6865 201.549 76.7086C201.251 76.7307 200.85 76.7418 200.344 76.7418C199.23 76.7418 198.491 76.5318 198.128 76.112C197.765 75.6811 197.584 75.1342 197.584 74.4712V74.0569C197.208 74.9629 196.657 75.6369 195.931 76.0788C195.219 76.5208 194.305 76.7418 193.19 76.7418C191.894 76.7418 190.916 76.5208 190.255 76.0788C189.607 75.6369 189.283 74.9795 189.283 74.1066C189.283 73.3885 189.561 72.825 190.119 72.4162C190.676 72.0074 191.538 71.7256 192.704 71.5709C191.719 70.9743 190.734 70.455 189.749 70.0131C190.527 69.1733 191.415 68.538 192.413 68.1071C193.411 67.6652 194.622 67.4442 196.048 67.4442C198.044 67.4442 199.521 67.8143 200.48 68.5546C201.452 69.2838 201.938 70.4108 201.938 71.9355V73.9077ZM196.456 70.3611C195.795 70.3611 195.212 70.455 194.707 70.6428C194.201 70.8307 193.748 71.1124 193.346 71.4881C194.123 71.4107 195.095 71.3721 196.262 71.3721C196.741 71.3721 197.078 71.3223 197.273 71.2229C197.467 71.1235 197.564 70.9964 197.564 70.8417C197.564 70.7091 197.467 70.5986 197.273 70.5103C197.078 70.4108 196.806 70.3611 196.456 70.3611ZM194.415 74.0403C195.141 74.0403 195.795 73.9243 196.378 73.6923C196.962 73.4492 197.363 73.1343 197.584 72.7476V72.3167C197.273 72.4493 196.929 72.5543 196.553 72.6316C196.178 72.6979 195.724 72.7642 195.193 72.8305L194.493 72.9299C193.767 73.0515 193.404 73.2614 193.404 73.5597C193.404 73.8801 193.741 74.0403 194.415 74.0403ZM211.724 72.1344C211.75 72.6979 211.957 73.1509 212.346 73.4934C212.748 73.8359 213.338 74.0072 214.115 74.0072C214.543 74.0072 214.925 73.9409 215.262 73.8083C215.599 73.6647 215.91 73.4547 216.195 73.1785C217.297 73.5431 218.586 73.858 220.064 74.1232C219.636 74.974 218.91 75.6369 217.887 76.112C216.876 76.5871 215.56 76.8246 213.94 76.8246C211.776 76.8246 210.175 76.3937 209.139 75.5319C208.102 74.6701 207.583 73.5376 207.583 72.1344C207.583 70.7312 208.102 69.5987 209.139 68.7369C210.175 67.8751 211.776 67.4442 213.94 67.4442C215.56 67.4442 216.876 67.6818 217.887 68.1569C218.91 68.632 219.636 69.2949 220.064 70.1456C218.91 70.3556 217.621 70.6705 216.195 71.0903C215.91 70.8141 215.599 70.6097 215.262 70.4771C214.925 70.3335 214.543 70.2617 214.115 70.2617C213.338 70.2617 212.748 70.4329 212.346 70.7754C211.957 71.1179 211.75 71.5709 211.724 72.1344ZM230.096 72.1676C232.312 72.1676 233.42 72.7697 233.42 73.974C233.42 74.8358 233.031 75.4988 232.254 75.9628C231.489 76.4269 230.103 76.6589 228.094 76.6589H221.523C221.588 75.3993 221.62 73.8912 221.62 72.1344C221.62 70.3777 221.588 68.8695 221.523 67.6099H225.547V67.6265H228.074C229.293 67.6265 230.245 67.7149 230.932 67.8917C231.632 68.0574 232.124 68.3005 232.409 68.6209C232.695 68.9413 232.837 69.3501 232.837 69.8473C232.837 70.9633 231.923 71.7367 230.096 72.1676ZM225.547 69.897V71.0572H227.025C227.621 71.0572 228.042 71.013 228.288 70.9246C228.534 70.8251 228.657 70.6539 228.657 70.4108C228.657 70.223 228.528 70.0904 228.269 70.0131C228.022 69.9357 227.608 69.897 227.025 69.897H225.547ZM227.025 74.3718C227.763 74.3718 228.269 74.3276 228.541 74.2392C228.813 74.1398 228.949 73.9685 228.949 73.7254C228.949 73.5155 228.807 73.3608 228.521 73.2614C228.236 73.162 227.737 73.1122 227.025 73.1122H225.547V74.3718H227.025ZM246.691 67.6099C246.627 69.2894 246.594 70.7975 246.594 72.1344C246.594 73.4713 246.627 74.9795 246.691 76.6589H242.862V73.7752H241.287C240.393 74.9795 239.719 75.9407 239.265 76.6589H234.697C235.734 75.4546 236.608 74.416 237.321 73.5431C236.323 73.3222 235.617 72.9631 235.202 72.4659C234.788 71.9687 234.58 71.3555 234.58 70.6263C234.58 69.7092 234.969 68.98 235.747 68.4386C236.524 67.8972 237.898 67.6265 239.868 67.6265H242.862V67.6099H246.691ZM242.862 71.4383V70.0131H241.112C240.166 70.0131 239.524 70.0628 239.188 70.1622C238.864 70.2616 238.701 70.444 238.701 70.7091C238.701 70.9853 238.87 71.1787 239.207 71.2892C239.557 71.3886 240.192 71.4383 241.112 71.4383H242.862ZM256.787 72.0184C256.917 72.0074 257.098 72.0018 257.331 72.0018C258.2 72.0018 258.835 72.1731 259.237 72.5156C259.651 72.8581 259.859 73.2614 259.859 73.7254C259.859 74.7088 259.334 75.4712 258.284 76.0125C257.234 76.5539 255.886 76.8246 254.241 76.8246C252.802 76.8246 251.532 76.6147 250.43 76.1948C249.342 75.775 248.551 75.0679 248.059 74.0735L248.895 73.8912C250.1 73.6371 250.994 73.394 251.577 73.162C251.785 73.5045 252.076 73.7696 252.452 73.9575C252.828 74.1342 253.327 74.2226 253.949 74.2226C254.649 74.2226 255.18 74.1674 255.543 74.0569C255.919 73.9464 256.107 73.7641 256.107 73.51C256.107 73.3111 255.899 73.173 255.485 73.0957C255.07 73.0183 254.474 72.9797 253.696 72.9797H252.588V70.792H253.249C253.962 70.792 254.532 70.7644 254.96 70.7091C255.387 70.6539 255.601 70.5323 255.601 70.3445C255.601 70.1567 255.478 70.0075 255.232 69.897C254.986 69.7866 254.655 69.7313 254.241 69.7313C253.567 69.7313 253.035 69.8363 252.646 70.0462C252.271 70.2561 251.914 70.5986 251.577 71.0737C250.553 70.7091 249.432 70.4495 248.214 70.2948C249.212 68.4055 251.286 67.4608 254.435 67.4608C255.355 67.4608 256.197 67.5768 256.962 67.8088C257.727 68.0298 258.329 68.3392 258.77 68.7369C259.211 69.1236 259.431 69.5601 259.431 70.0462C259.431 71.1069 258.55 71.7643 256.787 72.0184ZM273.389 67.6099C273.324 69.212 273.292 70.6594 273.292 71.9521C273.292 73.2227 273.324 74.6701 273.389 76.2943H269.404L269.618 70.56L266.643 76.2943H261.219C261.297 74.8358 261.336 73.3885 261.336 71.9521C261.336 70.4937 261.297 69.0463 261.219 67.6099H265.049L264.855 73.4934L267.829 67.6099H273.389ZM276.811 76.8246C276.241 76.8246 275.755 76.6534 275.353 76.3109C274.952 75.9573 274.751 75.5374 274.751 75.0513C274.751 74.5652 274.952 74.1508 275.353 73.8083C275.755 73.4658 276.241 73.2945 276.811 73.2945C277.369 73.2945 277.848 73.4713 278.25 73.8249C278.665 74.1674 278.872 74.5762 278.872 75.0513C278.872 75.5374 278.665 75.9573 278.25 76.3109C277.848 76.6534 277.369 76.8246 276.811 76.8246Z" fill="white"/>
                    <path d="M111.668 53.3136H98.9924C99.1997 54.2858 99.7052 55.0814 100.509 55.7001C101.338 56.2967 102.505 56.595 104.008 56.595C105.097 56.595 106.12 56.4183 107.079 56.0647C108.039 55.689 108.804 55.1698 109.374 54.5068C111.11 55.236 113.417 56.0205 116.294 56.8602C115.491 58.407 114.026 59.6445 111.901 60.5726C109.801 61.4786 107.053 61.9316 103.658 61.9316C99.2516 61.9316 95.9857 61.0698 93.8602 59.3462C91.7607 57.6005 90.7109 55.3134 90.7109 52.4849C90.7109 49.7227 91.7607 47.4798 93.8602 45.7562C95.9597 44.0326 99.2257 43.1708 103.658 43.1708C106.302 43.1708 108.596 43.5464 110.54 44.2978C112.484 45.0491 113.975 46.0987 115.011 47.4467C116.048 48.7725 116.567 50.2972 116.567 52.0208C116.567 52.5954 116.541 53.0263 116.489 53.3136H111.668ZM104.202 48.1759C102.906 48.1759 101.844 48.3858 101.014 48.8057C100.185 49.2255 99.6015 49.8 99.2645 50.5293H108.441C108.13 49.8221 107.624 49.2587 106.924 48.8388C106.224 48.3968 105.317 48.1759 104.202 48.1759ZM143.495 61.7659H134.981V49.7669H129.46L129.421 50.7944C129.395 51.8551 129.343 52.7943 129.265 53.6119C129.213 54.4074 129.084 55.2692 128.876 56.1973C127.995 60.2411 124.405 62.2631 118.107 62.2631V56.9928C119.325 56.8602 120.154 56.5398 120.595 56.0316C121.036 55.5012 121.256 54.6836 121.256 53.5787V43.5022L143.495 43.5354V61.7659ZM167.178 53.3136H154.503C154.71 54.2858 155.215 55.0814 156.019 55.7001C156.848 56.2967 158.015 56.595 159.518 56.595C160.607 56.595 161.631 56.4183 162.59 56.0647C163.549 55.689 164.313 55.1698 164.884 54.5068C166.62 55.236 168.927 56.0205 171.804 56.8602C171.001 58.407 169.536 59.6445 167.411 60.5726C165.311 61.4786 162.564 61.9316 159.168 61.9316C154.762 61.9316 151.496 61.0698 149.37 59.3462C147.271 57.6005 146.221 55.3134 146.221 52.4849C146.221 49.7227 147.271 47.4798 149.37 45.7562C151.47 44.0326 154.736 43.1708 159.168 43.1708C161.812 43.1708 164.106 43.5464 166.05 44.2978C167.994 45.0491 169.484 46.0987 170.521 47.4467C171.558 48.7725 172.076 50.2972 172.076 52.0208C172.076 52.5954 172.05 53.0263 171.999 53.3136H167.178ZM159.713 48.1759C158.417 48.1759 157.354 48.3858 156.524 48.8057C155.695 49.2255 155.112 49.8 154.775 50.5293H163.95C163.639 49.8221 163.134 49.2587 162.434 48.8388C161.734 48.3968 160.827 48.1759 159.713 48.1759ZM194.651 52.9489C195.817 53.7445 196.711 54.6394 197.333 55.6338C197.955 56.6282 198.539 57.8215 199.083 59.2136C199.627 60.429 200.016 61.2245 200.249 61.6001H190.879C190.801 61.4455 190.646 60.9593 190.413 60.1417C190.205 59.3241 189.998 58.6722 189.791 58.1861C189.583 57.6778 189.272 57.2359 188.857 56.8602C188.209 56.2636 187.484 55.8769 186.68 55.7001C185.877 55.5233 184.684 55.4349 183.103 55.4349V61.6001H174.783C174.912 59.081 174.977 56.0647 174.977 52.5512C174.977 49.0377 174.912 46.0214 174.783 43.5022H183.103V49.9989H183.259C186.343 47.3693 188.585 45.2037 189.985 43.5022H200.249L191.151 51.2253C192.499 51.7115 193.666 52.286 194.651 52.9489ZM214.336 61.9316C209.903 61.9316 206.637 61.0698 204.538 59.3462C202.464 57.6005 201.428 55.3134 201.428 52.4849C201.428 49.7227 202.464 47.4798 204.538 45.7562C206.637 44.0326 209.903 43.1708 214.336 43.1708C217.317 43.1708 219.805 43.5906 221.801 44.4303C223.822 45.2479 225.313 46.3639 226.272 47.7781C227.231 49.1703 227.71 50.7392 227.71 52.4849C227.71 55.3355 226.596 57.6226 224.367 59.3462C222.164 61.0698 218.82 61.9316 214.336 61.9316ZM214.491 56.2967C216.176 56.2967 217.433 55.9542 218.263 55.2692C219.092 54.5842 219.507 53.6671 219.507 52.518C219.507 51.4353 219.092 50.5514 218.263 49.8663C217.433 49.1592 216.176 48.8057 214.491 48.8057C212.806 48.8057 211.575 49.1482 210.798 49.8332C210.02 50.5182 209.631 51.4132 209.631 52.518C209.631 53.6671 210.02 54.5842 210.798 55.2692C211.575 55.9542 212.806 56.2967 214.491 56.2967ZM260.11 43.5022L261.588 61.6001H254.045L253.579 48.6399L250.624 61.6001H241.409L238.415 48.8057L237.988 61.6001H230.445L231.922 43.5022H243.431L245.958 55.3355L248.213 43.5022H260.11ZM268.425 61.9316C267.285 61.9316 266.313 61.5891 265.509 60.9041C264.706 60.1969 264.304 59.3572 264.304 58.3849C264.304 57.4127 264.706 56.584 265.509 55.899C266.313 55.2139 267.285 54.8714 268.425 54.8714C269.54 54.8714 270.499 55.225 271.302 55.9321C272.132 56.6171 272.547 57.4347 272.547 58.3849C272.547 59.3572 272.132 60.1969 271.302 60.9041C270.499 61.5891 269.54 61.9316 268.425 61.9316Z" fill="white"/>
                    <path d="M75.4494 20.8655H62.9397V93.1505H75.4494V20.8655Z" fill="white"/>
                    <path d="M22.1186 75.0793H9.60889V93.1504H22.1186V75.0793Z" fill="white"/>
                    <path d="M39.8955 57.0081H27.3857V93.1504H39.8955V57.0081Z" fill="white"/>
                    <path d="M89.9093 10.0316H48.0886V20.8442H89.9093V10.0316Z" fill="white"/>
                    <path d="M57.6726 38.9368H45.1628V93.1505H57.6726V38.9368Z" fill="white"/>
                </svg>
                </a>
        <p class="footer-text">© 2024 ООО “Телеком”</p>
        <p class="footer-text"><a class="tel-link" href="tel:+78630000001">8 (863) 000-00-01</a></p>
        <p class="footer-text">РФ, г. Ростов-на-Дону, пл. Гагарина, д. 1, стр. 14, офис 1213</p>
        <p class="footer-text">пн-сб: с 10:00 по 18:00</p>
        <p class="footer-text">вс: выходной</p>
            </div>
            <div class="footer-services">
            <h2 class="footer-title">Наши услуги</h2>
            <ul class="footer-list">
                <li class="footer-item">
                    <a class="footer-link" href="internet.php">Интернет</a>
                </li>
                <li class="footer-item">
                    <a class="footer-link" href="television.php">Телевидение</a>
                </li>
                <li class="footer-item">
                    <a class="footer-link" href="mobile.php">Мобильная связь</a>
                </li>
            </ul>
            </div>
            <div class="footer-contacts">
            <h2 class="footer-title">Связь с нами</h2>
            <ul class="footer-list">
                <li class="footer-item">
                    <a class="footer-link" href="help.php">Поддержка</a>
                </li>
                <div class="social-bar">
                    <li class="footer-item">
                        <a class="footer-item-social" href="https://vk.com/kutin1">
                            <span class="visually-hidden">ВКонтакте</span>
                            <svg width="67" height="42" viewBox="0 0 67 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M36.3333 41.625C13.5416 41.625 0.5417 26 0 0H11.4167C11.7917 19.0833 20.2082 27.1666 26.8748 28.8333V0H37.6252L36.3333 20.7499L53.4577 0H64.208C62.4163 10.1667 54.9163 17.6666 49.583 20.7499C54.9163 23.2499 63.4584 29.7916 66.7084 41.625H54.8747C52.333 33.7083 46.0002 27.5833 37.6252 26.7499V41.625H36.3333Z" fill="#BEB931"/>
                            </svg> 
                        </a>
                    </li>
                    <li class="footer-item">
                        <a class="footer-item-social" href="https://ok.ru/kutin1">
                            <span class="visually-hidden">Одноклассники</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="41" height="47" viewBox="0 0 41 47" fill="none">
                                <path d="M20.7969 0C10.7246 0 2.55957 5.45298 2.55957 12.1794C2.55957 18.9059 10.7246 24.3593 20.7969 24.3593C30.8692 24.3593 39.0342 18.9059 39.0342 12.1794C39.0342 5.45298 30.8692 0 20.7969 0ZM20.7969 17.2144C16.6334 17.2144 13.258 14.9601 13.258 12.1795C13.258 9.39902 16.6334 7.14486 20.7969 7.14486C24.9604 7.14486 28.3358 9.39902 28.3358 12.1795C28.3358 14.9601 24.9604 17.2144 20.7969 17.2144Z" fill="#BEB931"/>
                                <path d="M27.2355 34.0504C34.5366 33.0571 38.9118 30.7481 39.1433 30.6242C41.2798 29.4799 41.623 27.3957 39.9096 25.9687C38.1964 24.542 35.0758 24.3128 32.9388 25.4568C32.8937 25.4812 28.2272 27.8719 20.5492 27.8754C12.8714 27.8719 8.10632 25.4812 8.06117 25.4568C5.9242 24.3128 2.80356 24.542 1.09043 25.9687C-0.622953 27.3957 -0.279752 29.4799 1.8567 30.6242C2.09133 30.7498 6.64714 33.119 14.1529 34.089L3.69223 41.3898C1.79119 42.7064 1.84835 44.8029 3.81985 46.0724C4.78211 46.692 6.02233 47 7.2615 47C8.5607 47 9.8586 46.661 10.8316 45.987L20.5495 39.0736L31.2489 46.0318C33.1875 47.3244 36.3267 47.3223 38.2617 46.0282C40.1967 44.7338 40.1943 42.6371 38.2562 41.3449L27.2355 34.0504Z" fill="#BEB931"/>
                            </svg>
                        </a>
                    </li>
                    <li class="footer-item">
                        <a class="footer-item-social" href="https://t.me/kutin0">
                            <span class="visually-hidden">Телеграм</span>
                            <svg width="65" height="51" viewBox="0 0 65 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.46841 21.9552C21.9167 14.7579 33.5516 10.013 39.3731 7.72051C55.9949 1.175 59.4487 0.0379455 61.6999 0.000400304C62.195 -0.00785734 63.3021 0.10832 64.0192 0.659226C64.6247 1.1244 64.7913 1.75279 64.871 2.19383C64.9507 2.63487 65.05 3.63957 64.9711 4.42461C64.0704 13.3849 60.1729 35.1294 58.1901 45.1651C57.3511 49.4115 55.6991 50.8354 54.0997 50.9747C50.624 51.2775 47.9846 48.8 44.6182 46.7107C39.3504 43.4414 36.3744 41.4062 31.2611 38.216C25.3517 34.5292 29.1825 32.5027 32.5502 29.1911C33.4316 28.3244 48.7458 15.1364 49.0422 13.94C49.0793 13.7904 49.1137 13.2327 48.7637 12.9382C48.4138 12.6437 47.8972 12.7444 47.5245 12.8245C46.9962 12.938 38.5808 18.2042 22.2783 28.623C19.8896 30.176 17.726 30.9326 15.7875 30.893C13.6505 30.8493 9.53965 29.749 6.48367 28.8085C2.73539 27.6549 -0.243662 27.045 0.0157469 25.0859C0.150863 24.0655 1.63508 23.0219 4.46841 21.9552Z" fill="#BEB931"/>
                            </svg>
                                
                        </a>
                    </li>
                </div>
            </ul>
            </div>
        </div>
        <div class="copyright-author">
            <div class="copyright-author-person">
                <p class="copyright-author-person">Дизайн, вёрстка и разработка - Сэр Владислав Константинович Кутин</p>
                <p class="copyright-author-person">Все права защищены. Данный сайт является интеллектуальной собственностью. Копирование материалов сайта запрещено. Незаконное копирование, в том числе без разрешения владельца интеллектуальной собственности преследуется по закону Уголовного Кодекса Российской Федерации, а также по принятым международным конвенциям защиты собственности.</p>
            </div>
          </div>
    </footer>
    <script src="src/scripts/script.js"></script>
    <script src="src/scripts/account.js"></script>
    <script src="src/scripts/payments.js"></script>
</body>
</html>
