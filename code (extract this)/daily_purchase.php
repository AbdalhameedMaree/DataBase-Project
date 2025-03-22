<?php
session_start();
header('Content-Type: application/json'); // Set JSON header

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
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

$sql = "SELECT 
    sales_process.date_sale AS purchase_date,
    (order_line.quantity * product.price * (1 - COALESCE(product.discount, 0) / 100)) AS total_price
FROM 
    sales_process
INNER JOIN 
    order_line ON sales_process.ID_order = order_line.ID_order
INNER JOIN 
    product ON order_line.ID_P = product.ID_P
WHERE 
    sales_process.ID_cus = ?
ORDER BY 
    purchase_date;";

$customer_id = $_SESSION['user_id'];

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'purchase_date' => $row['purchase_date'],
        'total_price' => $row['total_price']
    ];
}

echo json_encode($data); // Return data as JSON

$stmt->close();
$conn->close();
?>
