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

$data = json_decode(file_get_contents("php://input"));
$sellerCategory = isset($_GET['category']) ? $_GET['category'] : '';
$email = isset($data->checkouremail) ? trim(strtolower($data->checkouremail)) : null;

if (
    property_exists($data, 'name') &&
    property_exists($data, 'address') &&
    property_exists($data, 'contact') &&
    property_exists($data, 'paymentMode') &&
    property_exists($data, 'cart') &&
    is_array($data->cart) &&
    count($data->cart) > 0
) {
    if (!$email) {
        echo json_encode(["status" => "error", "message" => "Email is required and must be valid"]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "DB prepare failed: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId);
    $userFound = $stmt->fetch();
    $stmt->close();

    if (!$userFound) {
        echo json_encode([
            "status" => "error",
            "message" => "User not found for this email",
            "email" => $email
        ]);
        $conn->close();
        exit();
    }

    $conn->begin_transaction();

    $insert = $conn->prepare("INSERT INTO purchases (
        user_id, product_name, category, quantity, unit_price, 
        buyer_name, delivery_address, contact_number, payment_mode, 
        upi_id, masked_card_number
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$insert) {
        echo json_encode(["status" => "error", "message" => "Insert prepare failed: " . $conn->error]);
        exit();
    }

    try {
        foreach ($data->cart as $item) {
            $productName = $item->name;
            $category = $sellerCategory;
            $quantity = isset($item->quantity) ? $item->quantity : 1;
            $unitPrice = $item->price;

            $buyerName = $data->name;
            $deliveryAddress = $data->address;
            $contactNumber = $data->contact;
            $paymentMode = $data->paymentMode;
            $upiId = ($paymentMode === 'UPI') ? ($data->upiId ?? null) : null;

            $maskedCardNumber = null;
            if ($paymentMode === 'Card' && !empty($data->cardNumber)) {
                $maskedCardNumber = "**** **** **** " . substr($data->cardNumber, -4);
            }

            $insert->bind_param(
                "issidssisss",
                $userId,
                $productName,
                $category,
                $quantity,
                $unitPrice,
                $buyerName,
                $deliveryAddress,
                $contactNumber,
                $paymentMode,
                $upiId,
                $maskedCardNumber
            );

            if (!$insert->execute()) {
                throw new Exception("Insert failed: " . $insert->error);
            }
        }

        $conn->commit();
        // Build receipt data
    $purchaseDate = date('Y-m-d H:i:s');
    $items = [];
    $totalAmount = 0;

    foreach ($data->cart as $item) {
        $qty = isset($item->quantity) ? $item->quantity : 1;
        $price = $item->price;
        $total = $qty * $price;

        $items[] = [
            "name" => $item->name,
            "quantity" => $qty,
            "price" => $price,
            "total" => $total
        ];

        $totalAmount += $total;
    }

    $receipt = [
        "buyer_name" => $data->name,
        "address" => $data->address,
        "contact" => $data->contact,
        "payment_mode" => $data->paymentMode,
        "purchase_date" => $purchaseDate,
        "upi_id" => $upiId,
        "items" => $items,
        "total_amount" => $totalAmount
    ];

    echo json_encode([
        "status" => "success",
        "message" => "Purchase saved",
        "userId" => $userId,
        "receipt" => $receipt
    ]);
        

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

    $insert->close();
    $conn->close();

} else {
    echo json_encode(["status" => "error", "message" => "Incomplete or invalid input"]);
}
