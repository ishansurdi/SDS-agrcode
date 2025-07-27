<?php
// Clear any previous output
ob_clean();

// Required headers
file_put_contents('debug_input.log', file_get_contents('php://input'));
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/pdf');

// Include TCPDF library
require_once(__DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php');


// Read JSON input
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// Validate input
if (!isset($data['receipt'])) {
    http_response_code(400);
    echo "Invalid request. Receipt data missing.";
    exit;
}

$receipt = $data['receipt'];
file_put_contents('debug_parsed_receipt.log', print_r($receipt, true));

// Initialize PDF
$pdf = new TCPDF();
$pdf->SetCreator('SDS AgroCode');
$pdf->SetAuthor('SDS AgroCode');
$pdf->SetTitle('Purchase Receipt');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Purchase Receipt', 0, 1, 'C');
$pdf->Ln(5);

// Buyer Info
$pdf->SetFont('helvetica', '', 11);
$pdf->MultiCell(0, 6, "Name: " . $receipt['buyer_name'], 0, 1);
$pdf->MultiCell(0, 6, "Address: " . $receipt['address'], 0, 1);
$pdf->MultiCell(0, 6, "Contact: " . $receipt['contact'], 0, 1);
$pdf->MultiCell(0, 6, "Payment Mode: " . $receipt['payment_mode'], 0, 1);
if ($receipt['payment_mode'] === 'UPI') {
    $pdf->MultiCell(0, 6, "UPI ID: " . ($receipt['upi_id'] ?? 'N/A'), 0, 1);
} elseif ($receipt['payment_mode'] === 'Card') {
    $pdf->MultiCell(0, 6, "Card: **** **** **** " . substr(($receipt['card_number'] ?? 'XXXX'), -4), 0, 1);
}
$pdf->MultiCell(0, 6, "Date: " . ($receipt['purchase_date'] ?? date('Y-m-d')), 0, 1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(50, 8, 'Product', 1, 0, 'C');
$pdf->Cell(30, 8, 'Quantity', 1, 0, 'C');
$pdf->Cell(40, 8, 'Unit Price ', 1, 0, 'C');
$pdf->Cell(50, 8, 'Total ', 1, 1, 'C');

// Table Body
$pdf->SetFont('helvetica', '', 11);
foreach ($receipt['items'] as $item) {
    $pdf->Cell(50, 8, $item['name'], 1);
    $pdf->Cell(30, 8, $item['quantity'], 1);
    $pdf->Cell(40, 8, number_format($item['price'], 2), 1);
    $pdf->Cell(50, 8, number_format($item['price'] * $item['quantity'], 2), 1, 1);
}

// Total
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(120, 10, 'Total Amount', 1, 0, 'R');
$pdf->Cell(50, 10, number_format($receipt['total_amount'], 2), 1, 1, 'C');

// Footer
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->MultiCell(0, 5, "Thank you for your purchase with  AgroCode.\nThis is a system-generated receipt.", 0, 'C');

// Output PDF
$pdf->Output('receipt.pdf', 'I');  // Use 'I' to show in browser or 'D' to force download
exit;
