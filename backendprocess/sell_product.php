<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../db_connection/db_connection.php';

// Check if form-data POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Required fields
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : null;
    $seller_type = isset($_POST['seller_type']) ? trim($_POST['seller_type']) : null;  // farmer/user
    $category = isset($_POST['category']) ? trim($_POST['category']) : null;

    // Validate required
    if (!$name || !$price || !$seller_type || !$category) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit();
    }

    // Handle file upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed or missing']);
        exit();
    }

    $uploadDir = __DIR__ . '/../uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = basename($_FILES['image']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileExt, $allowedExts)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid image file type']);
        exit();
    }

    // Rename file to avoid conflicts
    $newFileName = uniqid('prod_', true) . '.' . $fileExt;
    $destPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmpPath, $destPath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded image']);
        exit();
    }

    // Relative path to save in DB (adjust this based on your app)
    $imagePath = 'uploads/products/' . $newFileName;

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO products (name, price, image_path, category, seller_type) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'DB prepare failed: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("sdsss", $name, $price, $imagePath, $category, $seller_type);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Insert failed: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
