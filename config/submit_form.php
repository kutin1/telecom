<?php
// Include your database connection file here
include 'db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs to prevent SQL injection
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);

    // Check if services are selected
    if (!empty($_POST['services'])) {
        $services = implode(", ", $_POST['services']);
    } else {
        $services = "";
    }

    // Insert data into the connectionrequest table
    $sql = "INSERT INTO ConnectionRequests (RequestName, RequestPhone, RequestAddress, Services)
            VALUES ('$name', '$phone', '$address', '$services')";
    
    if (mysqli_query($connection, $sql)) {
        echo "Form submitted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }
}

// Close database connection
mysqli_close($connection);
?>
