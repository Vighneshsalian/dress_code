<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $user_email = $_SESSION['email'];
    
    // Verify that the request belongs to the logged-in user and is in 'delivered' status
    $check_sql = "SELECT id FROM dress_requests WHERE id = ? AND user_email = ? AND status = 'delivered'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("is", $request_id, $user_email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update the status to 'received'
        $update_sql = "UPDATE dress_requests SET status = 'received' WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $request_id);
        
        if ($update_stmt->execute()) {
            echo "<script>
                alert('Order marked as received successfully!');
                window.location.href='view_request.php';
            </script>";
        } else {
            echo "<script>
                alert('Failed to update order status. Please try again.');
                window.location.href='view_request.php';
            </script>";
        }
        
        $update_stmt->close();
    } else {
        echo "<script>
            alert('Invalid request or order not in delivered status.');
            window.location.href='view_request.php';
        </script>";
    }
    
    $check_stmt->close();
} else {
    header("Location: view_request.php");
}

$conn->close();
?> 