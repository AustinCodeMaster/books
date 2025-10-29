<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../conf.php';

// Debug: Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'cart'");
error_log("Table check result: " . ($tableCheck->num_rows > 0 ? "Table exists" : "Table does not exist"));

// Debug: Check table structure
if ($tableCheck->num_rows > 0) {
    $structureCheck = $conn->query("DESCRIBE cart");
    $columns = [];
    while ($row = $structureCheck->fetch_assoc()) {
        $columns[] = $row;
    }
    error_log("Table structure: " . print_r($columns, true));
}

// Log the session data
error_log("Session data: " . print_r($_SESSION, true));

// Debug session data
error_log("Session contents: " . print_r($_SESSION, true));

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    error_log("Authentication failed: User not authenticated");
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Check if user_id exists
if (!isset($_SESSION['user_id'])) {
    error_log("User ID missing from session");
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User ID not found in session']);
    exit;
}

error_log("User ID from session: " . $_SESSION['user_id']);

// Get POST data
$rawData = file_get_contents('php://input');
error_log("Received raw data: " . $rawData);
$data = json_decode($rawData, true);
error_log("Decoded data: " . print_r($data, true));

if (isset($data['bookName']) && isset($data['bookPrice'])) {
    $userId = $_SESSION['user_id'];
    $item = $data['bookName'];
    $price = $data['bookPrice'];
    
    error_log("Processing cart addition - UserID: $userId, Item: $item, Price: $price");

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO cart (user_id, item, item_price) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $userId, $item, $price);

    try {
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            throw new Exception("Failed to prepare statement");
        }

        if ($stmt->execute()) {
            $cartId = $conn->insert_id;
            error_log("Successfully added to cart. Cart ID: $cartId");
            echo json_encode([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'cartId' => $cartId
            ]);
        } else {
            error_log("Execute failed: " . $stmt->error);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add item to cart: ' . $stmt->error
            ]);
        }
    } catch (Exception $e) {
        error_log("Cart addition error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid data received'
    ]);
}
?>