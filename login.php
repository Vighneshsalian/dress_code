<?php
include 'config.php';
session_start();

$email = $_POST['email'];
$password = hash('sha256', $_POST['password']);

$query = "SELECT * FROM users WHERE email = ? AND password = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (isset($user['is_verified']) && $user['is_verified'] == 0) {
        echo "<script>alert('Please verify your email before logging in.'); window.location.href='login.html';</script>";
        exit();
    }
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];

    if ($user['role'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: home.php");
    }
} else {
    echo "<script>alert('Invalid email or password.'); window.location.href='login.html';</script>";
}
?>