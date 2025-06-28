<?php
require '../db_connection/db_connection.php';

$result = $conn->query("SELECT id, name, email, role, created_at FROM users");
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(["users" => $users]);
?>
