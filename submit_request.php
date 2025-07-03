<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include 'config.php';

$user_email = $_SESSION['email'];

// Handle image upload
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["dress_image"]["name"]);
move_uploaded_file($_FILES["dress_image"]["tmp_name"], $target_file);

$size = $_POST['size'];
$gender = $_POST['gender'];
$contact_no = $_POST['contact_no'];
$details = $_POST['details'];
$timeline = $_POST['timeline'];

// Insert into database
$status = 'pending';
$stmt = $conn->prepare("INSERT INTO dress_requests (user_email, image, size, gender, contact_no, details, timeline, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $user_email, $target_file, $size, $gender, $contact_no, $details, $timeline, $status);

if ($stmt->execute()) {
    echo "<script>alert('Dress request submitted successfully!'); window.location.href='home.php';</script>";
} else {
    echo "<script>alert('Error submitting request.'); window.location.href='request.html';</script>";
}

$stmt->close();
$conn->close();
?>