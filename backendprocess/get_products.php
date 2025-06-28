<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db_connection/db_connection.php';
$baseURL = 'http://localhost/sds/SDS-agrcode/';  // note the trailing slash


// Read seller_type from GET parameter
$seller_type = isset($_GET['seller_type']) ? $_GET['seller_type'] : '';

// Basic validation to allow only 'user' or 'farmer' for safety
if (!in_array($seller_type, ['user', 'farmer'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid seller_type parameter"
    ]);
    exit;
}

$sql = "SELECT name, price, image_path, category FROM products WHERE seller_type = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $seller_type);
$stmt->execute();
$result = $stmt->get_result();

$productsByCategory = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cat = $row['category'];
        if (!isset($productsByCategory[$cat])) {
            $productsByCategory[$cat] = [];
        }
        $productsByCategory[$cat][] = [
            'name' => $row['name'],
            'price' => (float)$row['price'],
            'image' => !empty($row['image_path']) ? $baseURL . $row['image_path'] : $baseURL . 'images/default.png'
        ];
    }
}

echo json_encode(["status" => "success", "products" => $productsByCategory]);

$stmt->close();
$conn->close();
?>
