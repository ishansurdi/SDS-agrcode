<?php
header('Content-Type: application/json');

include '../db_connection/db_connection.php';



// Get input date from frontend
$data = json_decode(file_get_contents("php://input"));
$selectedDate = $data->date ?? null;

if (!$selectedDate) {
    echo json_encode(['error' => 'Date is required']);
    exit;
}

$dateOnly = substr($selectedDate, 0, 10);

// Format the date properly
$dateStart = $dateOnly . ' 00:00:00';
$dateEnd = $dateOnly . ' 23:59:59';
error_log("Received date: " . $selectedDate);
error_log("Parsed dateOnly: " . $dateOnly);
error_log("Query range: $dateStart to $dateEnd");
error_log("Parsed date: " . $dateOnly);




// Fetch transactions for selected date
$sql = "SELECT * FROM purchases WHERE purchase_time BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $dateStart, $dateEnd);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
$totalProfit = 0;
$productCount = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    $totalProfit += floatval($row['total_price']);
    
    $product = $row['product_name'];
    $productCount[$product] = ($productCount[$product] ?? 0) + $row['quantity'];
}

$totalTransactions = count($transactions);
$avgTransaction = $totalTransactions > 0 ? round($totalProfit / $totalTransactions, 2) : 0;

// Find top product
$topProduct = '';
if (!empty($productCount)) {
    arsort($productCount);
    $topProduct = array_key_first($productCount);
}

// Output response
echo json_encode([
    'transactions' => $transactions,
    'stats' => [
        'totalTransactions' => $totalTransactions,
        'totalProfit' => $totalProfit,
        'avgTransaction' => $avgTransaction,
        'topProduct' => $topProduct
    ]
]);

$conn->close();
