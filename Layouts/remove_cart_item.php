<?php
session_start();
require_once '../conf.php';

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['cartId'])) {
    $cartId = $data['cartId'];
    $userId = $_SESSION['user_id'];
    
    // Delete cart item (ensure it belongs to the user)
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cartId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to remove item'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>
