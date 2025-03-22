<?php
// Database connection parameters
session_start();
$host = 'localhost';
$username = 'root';
$password = '1234';
$dbname = 'gh';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Connection failed.']));
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$customerId = $_SESSION['user_id'];

if ($customerId <= 0) {
    echo json_encode(['error' => 'Invalid customer ID']);
    exit();
}

// Prepare and execute query
$sql = "SELECT name, email, phone, birth_date, password, gender FROM Customer WHERE ID_cus = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $customerId, PDO::PARAM_INT);
$stmt->execute();

$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if ($customer) {
    echo json_encode($customer);
} else {
    echo json_encode(['error' => 'Customer not found']);
}

$pdo = null;
?>
