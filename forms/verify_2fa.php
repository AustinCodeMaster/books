<?php
require_once '../conf.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false, 'message' => ''];

if (isset($data['code']) && isset($_SESSION['user_id'])) {
    $userCode = $data['code'];
    $userId = $_SESSION['user_id'];
    
    // Verify code against database
    $stmt = $conn->prepare("SELECT verification_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($userCode === $row['verification_code']) {
            // Code is valid
            $_SESSION['authenticated'] = true;
            
            // Clear verification code in database
            $clearStmt = $conn->prepare("UPDATE users SET verification_code = '' WHERE id = ?");
            $clearStmt->bind_param("i", $userId);
            $clearStmt->execute();
            
            $response = [
                'success' => true,
                'message' => 'Verification successful'
            ];
        } else {
            $response['message'] = 'Invalid verification code';
        }
    } else {
        $response['message'] = 'Verification code has expired';
    }
} else {
    $response['message'] = 'Invalid request';
}

header('Content-Type: application/json');
echo json_encode($response);
?>