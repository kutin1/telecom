<?php
// Include your database connection file here
include 'db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs to prevent SQL injection
    $modalName = mysqli_real_escape_string($connection, $_POST['modal-name']);
    $modalPhone = mysqli_real_escape_string($connection, $_POST['modal-phone']);
    $modalAddress = mysqli_real_escape_string($connection, $_POST['modal-address']);

    // Check if services are selected
    if (!empty($_POST['modal-services'])) {
        $modalServices = implode(", ", $_POST['modal-services']);
    } else {
        $modalServices = "";
    }

    // Insert data into the connectionrequest table
    $sql = "INSERT INTO ConnectionRequests (RequestName, RequestPhone, RequestAddress, Services)
            VALUES ('$modalName', '$modalPhone', '$modalAddress', '$modalServices')";
    
    if (mysqli_query($connection, $sql)) {
        echo "Form submitted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }
} else {
    echo "Form not submitted!";
}

// Close database connection
mysqli_close($connection);
?>