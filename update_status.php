<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHPMailer namespace imports
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include 'config.php';

if (!isset($_POST['request_id']) || !isset($_POST['action'])) {
    echo "<script>alert('Invalid request.'); window.location.href='admin.php';</script>";
    exit();
}

$request_id = intval($_POST['request_id']);
$action = $_POST['action'];

// Set the status based on action
switch ($action) {
    case 'process':
        $status = 'processing';
        break;
    case 'deliver':
        $status = 'delivered';
        break;
    case 'cancel':
        $status = 'canceled';
        break;
    case 'accept':
        $status = 'accepted';
        break;
    case 'reject':
        $status = 'rejected';
        break;
    default:
        $status = 'pending';
}

// Check if the request exists
$check = $conn->prepare("SELECT id FROM dress_requests WHERE id = ?");
$check->bind_param("i", $request_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    echo "<script>alert('Request not found.'); window.location.href='admin.php';</script>";
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Update the status
$stmt = $conn->prepare("UPDATE dress_requests SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $request_id);

if ($stmt->execute()) {
    // Fetch user email for this request
    $email_stmt = $conn->prepare("SELECT user_email FROM dress_requests WHERE id = ?");
    $email_stmt->bind_param("i", $request_id);
    $email_stmt->execute();
    $email_stmt->bind_result($user_email);
    $email_stmt->fetch();
    $email_stmt->close();

    // Send email notification using PHPMailer
    require __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/PHPMailer/src/SMTP.php';
    require __DIR__ . '/PHPMailer/src/Exception.php';
    
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dcode5556@gmail.com';
        $mail->Password   = 'kzqj gyfj ekpw akvx';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        //Recipients
        echo "<script>alert('Sending to: $user_email');</script>";
        $mail->setFrom($mail->Username, 'Dress Code');
        $mail->clearAddresses(); // Ensure no previous recipients
        $mail->addAddress($user_email); // Send to the user

        // Content
        $mail->isHTML(false);
        $mail->Subject = 'Your Dress Request Status Has Been Updated';
        $mail->Body    = "Hello,\n\nYour dress request status is now: $status.\n\nThank you for using Dress Code!";

        $mail->send();
        // Optionally, you can log or show a message if needed
    } catch (Exception $e) {
        echo "<script>alert('Order updated, but email could not be sent: " . addslashes($mail->ErrorInfo) . "'); window.location.href='admin.php';</script>";
        exit();
    }

    echo "<script>alert('Order marked as $status successfully.'); window.location.href='admin.php';</script>";
} else {
    echo "<script>alert('Failed to update order status.'); window.location.href='admin.php';</script>";
}

$stmt->close();
$conn->close();
?>