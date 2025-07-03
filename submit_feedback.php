<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit();
    }
    
    // Insert feedback into database
    $query = "INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $name, $email, $message);
    
    if ($stmt->execute()) {
        echo "<script>alert('Thank you for your message! We will get back to you soon.'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Sorry, there was an error sending your message. Please try again.'); window.history.back();</script>";
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: index.html");
    exit();
}
?> 