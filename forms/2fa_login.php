<?php
require_once '../conf.php';
session_start();

// Get JSON POST data
$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false, 'message' => ''];

if (isset($data['email']) && isset($data['password'])) {
    $email = $data['email'];
    $password = $data['password'];

    // Debug information
    error_log("Login attempt - Email: " . $email);
    error_log("Login attempt - Password: " . $password);

    // Verify user credentials
    $stmt = $conn->prepare("SELECT id, user_password, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        error_log("Found user - Stored password: " . $row['user_password']);
        if ($password === $row['user_password']) {  // Direct comparison since password is stored as VARCHAR(40)
            error_log("Password match successful");
            // Generate 6-digit code
            $code = sprintf('%06d', mt_rand(0, 999999));
            
            // Update verification code in database
            $updateStmt = $conn->prepare("UPDATE users SET verification_code = ? WHERE id = ?");
            $updateStmt->bind_param("si", $code, $row['id']);
            $updateStmt->execute();
            
            // Store data in session
            $_SESSION['2fa_code'] = $code;
            $_SESSION['2fa_time'] = time();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Send email using PHPMailer
            require_once '../pluggins/PHPMailer/src/Exception.php';
            require_once '../pluggins/PHPMailer/src/PHPMailer.php';
            require_once '../pluggins/PHPMailer/src/SMTP.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = $conf['smtp_host'];
                $mail->SMTPAuth = true;
                $mail->Username = $conf['smtp_user'];
                $mail->Password = $conf['smtp_pass'];
                $mail->SMTPSecure = $conf['smtp_secure'];
                $mail->Port = $conf['smtp_port'];

                // Recipients
                $mail->setFrom($conf['admin_email'], $conf['site_name']);
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = '2FA Verification Code - ' . $conf['site_name'];
                $mail->Body = "
                    <h2>Your Verification Code</h2>
                    <p>Hello {$row['username']},</p>
                    <p>Your verification code is: <strong>{$code}</strong></p>
                    <p>This code will expire in 10 minutes.</p>
                    <p>If you didn't request this code, please ignore this email.</p>
                ";

                $mail->send();
                $response = [
                    'success' => true,
                    'message' => 'Verification code has been sent to your email'
                ];
            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
                $response = [
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again.',
                    'debug' => $mail->ErrorInfo  // Remove in production
                ];
            }
        } else {
            $response['message'] = 'Invalid credentials';
        }
    } else {
        $response['message'] = 'User not found';
    }
} else {
    $response['message'] = 'Invalid request';
}

header('Content-Type: application/json');
echo json_encode($response);
?>