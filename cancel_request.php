<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include 'config.php';

if (isset($_GET['cancel_id']) && is_numeric($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);
    $email = $_SESSION['email'];

    // Delete the request only if it belongs to the logged-in user
    $stmt = $conn->prepare("DELETE FROM dress_requests WHERE id = ? AND user_email = ?");
    $stmt->bind_param("is", $cancel_id, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Request cancelled successfully!'); window.location.href='view_request.php';</script>";
    } else {
        echo "<script>alert('Error cancelling request!'); window.location.href='view_request.php';</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='view_request.php';</script>";
}
$conn->close();
?>