<?php
require('tfpdf/tfpdf.php');  // Use tFPDF for Unicode support
require('../db_connection/db_connection.php');

header('Content-Type: text/html; charset=utf-8');

$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
$pdf->AddFont('DejaVu', '', 'DejaVuSans-Bold.ttf', true);
$pdf->SetFont('DejaVu', '', 12);

if (isset($_GET['tillNow']) && $_GET['tillNow'] == '1') {
    // Fetch all transactions till now
    $sql = "SELECT * FROM purchases ORDER BY purchase_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $title = "Transaction Summary Till Now";
} else if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $start = $date . " 00:00:00";
    $end = $date . " 23:59:59";
    $sql = "SELECT * FROM purchases WHERE purchase_time BETWEEN ? AND ? ORDER BY purchase_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    $title = "Transaction Summary - $date";
} else {
    die("Error: 'date' or 'tillNow' parameter required.");
}

$transactions = [];
$totalProfit = 0;
$productCounts = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    $totalProfit += floatval($row['total_price']);
    $product = $row['product_name'];
    $productCounts[$product] = ($productCounts[$product] ?? 0) + intval($row['quantity']);
}

$totalTransactions = count($transactions);
$averageValue = $totalTransactions > 0 ? ($totalProfit / $totalTransactions) : 0;
arsort($productCounts);
$topProduct = key($productCounts) ?? "N/A";

// Title
$pdf->SetFont('DejaVu', '', 14);
$pdf->Cell(0, 10, $title, 0, 1, 'C');
$pdf->Ln(8);

// Stats
$pdf->SetFont('DejaVu', '', 12);
$pdf->Cell(0, 10, "Total Transactions: $totalTransactions", 0, 1);
$pdf->Cell(0, 10, "Total Profit (₹): " . number_format($totalProfit, 2), 0, 1);
$pdf->Cell(0, 10, "Top Selling Product: $topProduct", 0, 1);
$pdf->Cell(0, 10, "Average Transaction Value (₹): " . number_format($averageValue, 2), 0, 1);
$pdf->Ln(10);

// Table Header
$pdf->SetFont('DejaVu', '', 12);
$pdf->Cell(30, 10, "Buyer", 1);
$pdf->Cell(40, 10, "Product", 1);
$pdf->Cell(20, 10, "Qty", 1, 0, 'C');
$pdf->Cell(30, 10, "Unit Price", 1, 0, 'R');
$pdf->Cell(30, 10, "Total", 1, 0, 'R');
$pdf->Ln();

// Table Rows
$pdf->SetFont('DejaVu', '', 11);
foreach ($transactions as $txn) {
    $pdf->Cell(30, 10, $txn['buyer_name'], 1);
    $pdf->Cell(40, 10, $txn['product_name'], 1);
    $pdf->Cell(20, 10, $txn['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, "₹" . number_format($txn['unit_price'], 2), 1, 0, 'R');
    $pdf->Cell(30, 10, "₹" . number_format($txn['total_price'], 2), 1, 0, 'R');
    $pdf->Ln();
}

$pdf->Output("D", ($title === "Transaction Summary Till Now" ? "Summary_TillNow.pdf" : "Summary_{$date}.pdf"));
