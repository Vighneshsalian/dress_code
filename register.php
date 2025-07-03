<?php
include 'config.php';

// PHPMailer namespace imports
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name = $_POST['name'];
$email = $_POST['email'];
$password = hash('sha256', $_POST['password']);

// Email format validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format. Please enter a valid email address.'); window.location.href='register.html';</script>";
    exit();
}

$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email already registered. Redirecting to login.'); window.location.href='login.html';</script>";
} else {
    // Generate verification token
    $token = bin2hex(random_bytes(32));
    $sql = "INSERT INTO users (name, email, password, is_verified, verification_token) VALUES (?, ?, ?, 0, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $token);
    $stmt->execute();

    // Send verification email using PHPMailer
    require __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/PHPMailer/src/SMTP.php';
    require __DIR__ . '/PHPMailer/src/Exception.php';
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dcode5556@gmail.com';
        $mail->Password   = 'kzqj gyfj ekpw akvx';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom($mail->Username, 'Dress Code');
        $mail->addAddress($email);
        $mail->isHTML(false);
        $mail->Subject = 'Verify your email for Dress Code';
        $verify_link = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/verify.php?token=' . $token;
        $mail->Body    = "Hello $name,\n\nThank you for registering with Dress Code! Please verify your email by clicking the link below:\n$verify_link\n\nIf you did not register, please ignore this email.";
        $mail->send();
    } catch (Exception $e) {
        // Optionally, log the error: $mail->ErrorInfo
    }

    echo "<script>alert('Registration successful! Please check your email to verify your account.'); window.location.href='login.html';</script>";
}
?>