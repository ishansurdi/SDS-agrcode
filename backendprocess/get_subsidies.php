<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../db_connection/db_connection.php';

$sql = "SELECT * FROM subsidies";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $subsidies = [];

    while ($row = $result->fetch_assoc()) {
        $subsidies[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "subsidies" => $subsidies
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No subsidy data found."
    ]);
}

$conn->close();
?>
