<?php

$name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone_number'];
$birth_date = $_POST['birth_date'];
$password = $_POST['password'];
$gender = $_POST['gender'];

// Database connection
$conn = new mysqli('localhost', 'root', '1234', 'gh');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepared statement
$stmt = $conn->prepare("INSERT INTO Customer (name, email, phone, birth_date, password, gender) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt === false) {
    die("Error in statement preparation: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("ssssss", $name, $email, $phone, $birth_date, $password, $gender);

// Execute and check for errors
if ($stmt->execute()) {
    echo "Registration successful...";
} else {
    die("Error in execution: " . $stmt->error);
}

// Close connections
$stmt->close();
$conn->close();
header("Location: login.html");
exit();
?>
