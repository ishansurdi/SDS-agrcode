<?php
// Enable CORS for development
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include DB connection
require_once '../db_connection/db_connection.php';

// Get incoming JSON data
$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->loginEmail) &&
    isset($data->loginPassword) &&
    isset($data->loginRole)
) {
    $email = trim($data->loginEmail);
    $password = trim($data->loginPassword);
    $role = ucfirst(strtolower(trim($data->loginRole))); // 'User' or 'Farmer'

    // Check user
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashedPassword);

    if ($stmt->num_rows === 1) {
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            echo json_encode(["status" => "success", "message" => "Login successful"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No user found for given email and role"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Missing required input"]);
}
?>
