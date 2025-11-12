<?php
session_start();
require_once '../conf.php';

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Get all cart items for this user
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Cart is empty');
    }
    
    // Calculate total
    $totalAmount = 0;
    $cartItems = [];
    while ($row = $result->fetch_assoc()) {
        $totalAmount += $row['item_price'];
        $cartItems[] = $row;
    }
    
    // Create order
    $orderStmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_status) VALUES (?, ?, 'pending')");
    $orderStmt->bind_param("id", $userId, $totalAmount);
    $orderStmt->execute();
    $orderId = $conn->insert_id;
    
    // Insert order items
    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, item_price, quantity, subtotal) VALUES (?, ?, ?, 1, ?)");
    
    foreach ($cartItems as $item) {
        $itemStmt->bind_param("isdd", $orderId, $item['item'], $item['item_price'], $item['item_price']);
        $itemStmt->execute();
    }
    
    // Clear cart
    $clearStmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clearStmt->bind_param("i", $userId);
    $clearStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'orderId' => $orderId
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Checkout failed: ' . $e->getMessage()
    ]);
}
?>
