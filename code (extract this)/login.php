<?php
session_start();  // Start the session

$email = $_POST['email'];
$password = $_POST['password'];
$account_type = $_POST['account_type'];

// Database connection
$conn = new mysqli('localhost', 'root', '1234', 'gh');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query based on account type
switch ($account_type) {
    case 'customer':
        $table = 'Customer';
        break;
    case 'employee':
        $table = 'Employee';
        break;
    case 'manager':
        $table = 'Manager';
        break;
    default:
        die("Invalid account type.");
}

// Prepare and execute SQL statement
$stmt = $conn->prepare("SELECT ID_cus FROM $table WHERE email = ? AND password = ?");
if ($stmt === false) {
    die("Error in statement preparation: " . $conn->error);
}

$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row['ID_cus'];
    
    // Store user data in session
    $_SESSION['loggedin'] = true;
    $_SESSION['user_id'] = $id;
    $_SESSION['account_type'] = $account_type;
    
    echo "Login successful! Welcome $account_type.";
    
    // Redirect to profile.php with customer_id
    header("Location: profile.html");
    exit();
} else {
    echo "Invalid email or password.";
}

// Close connections
$stmt->close();
$conn->close();
?>
