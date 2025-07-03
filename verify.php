<?php
include 'config.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';

$success = false;
$message = '';

if (!$token) {
    $message = 'Invalid verification link.';
} else {
    $sql = "SELECT id FROM users WHERE verification_token = ? AND is_verified = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Token is valid, verify the user
        $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
        $update->bind_param("s", $token);
        $update->execute();
        $success = true;
        $message = 'Your email has been verified! You can now <a href="login.html">login</a>.';
    } else {
        $message = 'Invalid or expired verification link.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | Dress Code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .verify-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(102,126,234,0.15);
            padding: 2.5rem 2rem;
            max-width: 400px;
            text-align: center;
        }
        .verify-icon {
            font-size: 3rem;
            color: <?php echo $success ? '#38c172' : '#e53e3e'; ?>;
            margin-bottom: 1rem;
        }
        .verify-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .verify-message {
            font-size: 1.1rem;
            color: #4a5568;
            margin-bottom: 1.5rem;
        }
        a {
            color: #667eea;
            text-decoration: underline;
        }
        .login-btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 2.2rem;
            background: linear-gradient(90deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            border-radius: 24px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(102,126,234,0.12);
            transition: background 0.2s, transform 0.2s;
        }
        .login-btn:hover {
            background: linear-gradient(90deg, #764ba2, #667eea);
            transform: translateY(-2px) scale(1.04);
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="verify-icon">
            <i class="fas <?php echo $success ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
        </div>
        <div class="verify-title">
            <?php echo $success ? 'Email Verified!' : 'Verification Failed'; ?>
        </div>
        <div class="verify-message">
            <?php echo $success ? 'Your email has been verified!' : $message; ?>
        </div>
        <?php if ($success): ?>
        <a href="login.html" class="login-btn">Login</a>
        <?php endif; ?>
    </div>
</body>
</html> 