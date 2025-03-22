<?php
session_start();
header('Content-Type: application/json'); // Set response type to JSON

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
    p.category,
    SUM(ol.quantity * p.price * (1 - COALESCE(p.discount, 0) / 100)) AS total_spent_per_category,
    (SUM(ol.quantity * p.price * (1 - COALESCE(p.discount, 0) / 100)) / 
    (SELECT 
         SUM(ol2.quantity * p2.price * (1 - COALESCE(p2.discount, 0) / 100))
     FROM 
         sales_process sp2
     INNER JOIN 
         order_line ol2 ON sp2.ID_order = ol2.ID_order
     INNER JOIN 
         product p2 ON ol2.ID_P = p2.ID_P
     WHERE 
         sp2.ID_cus = ?
    )) * 100 AS percentage_per_category
FROM 
    sales_process sp
INNER JOIN 
    order_line ol ON sp.ID_order = ol.ID_order
INNER JOIN 
    product p ON ol.ID_P = p.ID_P
WHERE 
    sp.ID_cus = ?
GROUP BY 
    p.category
ORDER BY 
    percentage_per_category DESC;";

$customer_id = $_SESSION['user_id'];
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $customer_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'category' => $row['category'],
        'total_spent' => round($row['total_spent_per_category'], 2),
        'percentage' => round($row['percentage_per_category'], 2)
    ];
}

// Return the JSON response
echo json_encode($data);

$stmt->close();
$conn->close();
?>
