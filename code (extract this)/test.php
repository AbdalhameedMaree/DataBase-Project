<?php
session_start();
if (isset($_SESSION['user_id'])) {
    echo json_encode(['user_id' => $_SESSION['user_id']]);
} else {
    echo json_encode(['error' => 'Session ID not set']);
    exit;  // Stop execution if session is not set
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gh"; // Update with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement
$sql = "SELECT 
    sp.ID_cus,
    c.name AS customer_name,
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
INNER JOIN 
    Customer c ON sp.ID_cus = c.ID_cus
WHERE 
    sp.ID_cus = ?
GROUP BY 
    sp.ID_cus, p.category, c.name
ORDER BY 
    percentage_per_category DESC;";

// Retrieve customer_id from session
$customer_id = $_SESSION['user_id'];

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $customer_id, $customer_id);

// Execute and fetch result
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Customer ID: " . htmlspecialchars($row['ID_cus']) . "<br>";
        echo "Customer Name: " . htmlspecialchars($row['customer_name']) . "<br>";
        echo "Category: " . htmlspecialchars($row['category']) . "<br>";
        echo "Total Spent per Category: $" . htmlspecialchars($row['total_spent_per_category']) . "<br>";
        echo "Percentage of Total Spending: " . htmlspecialchars($row['percentage_per_category']) . "%<br><br>";
    }
} else {
    echo "No records found.";
}

$stmt->close();
$conn->close();
?>
