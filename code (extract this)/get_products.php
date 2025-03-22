<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '1234';
$dbname = 'gh';

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// SQL query to fetch products
$sql = "SELECT ID_P, Name_product, price, color, discount, Stock, category, image_url FROM product";
$stmt = $pdo->query($sql);

// Fetch products as an associative array
$products = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $products[] = [
        'id' => $row['ID_P'],
        'name' => $row['Name_product'],
        'price' => $row['price'],
        'color' => $row['color'],
        'discount' => $row['discount'],
        'stock' => $row['Stock'],
        'category' => $row['category'],
        'image' => $row['image_url'], // Get image URL from the database
    ];
}

// Set the header to return JSON data
header('Content-Type: application/json');

// Return the products as JSON
echo json_encode($products);

// Close the database connection
$pdo = null;
?>
