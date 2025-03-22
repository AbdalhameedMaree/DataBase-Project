<?php
session_start();
header('Content-Type: application/json');

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
    SUM(order_line.quantity * product.price * (1 - COALESCE(product.discount, 0) / 100)) AS total_purchase_price
FROM 
    sales_process
INNER JOIN 
    Customer ON sales_process.ID_cus = Customer.ID_cus
INNER JOIN 
    order_line ON sales_process.ID_order = order_line.ID_order
INNER JOIN 
    product ON order_line.ID_P = product.ID_P
WHERE 
    sales_process.ID_cus = ?
GROUP BY 
    sales_process.ID_cus;";

$customer_id = $_SESSION['user_id'];

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = [
        'customer_name' => $row['customer_name'],
        'customer_email' => $row['customer_email'],
        'total_purchase_price' => number_format($row['total_purchase_price'], 2)
    ];
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No records found']);
}

$stmt->close();
$conn->close();
?>
