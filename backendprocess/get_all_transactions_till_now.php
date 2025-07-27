<?php
header('Content-Type: application/json');
include '../db_connection/db_connection.php';

// Fetch all transactions up to now (no date filter)
$sql = "SELECT * FROM purchases ORDER BY purchase_time DESC";
$result = $conn->query($sql);

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

echo json_encode([
    'transactions' => $transactions
]);

$conn->close();
?>
