<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.html");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email'];
    $size = $_POST['size'];
    $gender = $_POST['gender'];
    $contact_no = $_POST['contact_no'];
    $details = $_POST['details'];
    $timeline = $_POST['timeline'];
    $status = "pending";

    // ✅ Use a relative path for uploads
    $upload_dir = "uploads/";

    // Create the uploads folder if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $upload_dir . $image_name;

    // Store the relative path in the database
    $image_path = $upload_dir . $image_name;

    // Move uploaded file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        
        // Insert into the database
        $stmt = $conn->prepare("INSERT INTO dress_requests (user_email, size, gender, contact_no, details, timeline, image, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $email, $size, $gender, $contact_no, $details, $timeline, $image_path, $status);

        if ($stmt->execute()) {
            echo "<script>alert('Dress request submitted successfully!'); window.location.href='home.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

    } else {
        echo "Error uploading image.";
    }

    $stmt->close();
    $conn->close();
}
?>