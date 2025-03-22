<?php
session_start();
header('Content-Type: application/json'); // Set JSON response

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Session ID not set']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gh";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "SELECT 
    Customer.name AS customer_name,
    Customer.email AS customer_email,
    SUM(order_line.quantity) AS total_products_bought
FROM 
    sales_process
INNER JOIN 
    Customer ON sales_process.ID_cus = Customer.ID_cus
INNER JOIN 
    order_line ON sales_process.ID_order = order_line.ID_order
WHERE 
    sales_process.ID_cus = ?
GROUP BY 
    sales_process.ID_cus;";

$customer_id = $_SESSION['user_id'];

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = [
        'customer_name' => $row['customer_name'],
        'customer_email' => $row['customer_email'],
        'total_products_bought' => $row['total_products_bought']
    ];
} else {
    $data = ['error' => 'No records found'];
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>
