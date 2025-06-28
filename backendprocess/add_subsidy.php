<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db_connection/db_connection.php';

$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->name) &&
    isset($data->amount) &&
    isset($data->description) &&
    isset($data->eligibility)
) {
    $name = $data->name;
    $amount = $data->amount;
    $description = $data->description;
    $eligibility = $data->eligibility;
    $category = isset($data->category) ? $data->category : '';
    $documents = isset($data->documents) ? $data->documents : '';
    $link = isset($data->link) ? $data->link : '';

    $stmt = $conn->prepare("INSERT INTO subsidies (name, amount, description, eligibility, category, documents, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $amount, $description, $eligibility, $category, $documents, $link);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Subsidy added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to insert subsidy"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
}
?>
