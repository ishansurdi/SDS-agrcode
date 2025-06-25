<?php
error_reporting(0);            // Optional: suppress PHP warnings
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request (for CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include DB connection (adjust path if needed)
require_once '../db_connection/db_connection.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->registerRole) &&
    isset($data->registerName) &&
    isset($data->registerEmail) &&
    isset($data->registerPassword)
) {
    $role = ucfirst(strtolower(trim($data->registerRole)));
    $name = trim($data->registerName);
    $email = trim($data->registerEmail);
    $password = password_hash(trim($data->registerPassword), PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        exit;
    } else {
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO users (role, name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $role, $name, $email, $password);

        if ($stmt->execute()) {
            ob_clean();
            echo json_encode(["status" => "success", "message" => "Registered successfully"]);
            exit;
        } else {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Registration failed"]);
            exit;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}
