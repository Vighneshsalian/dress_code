<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

include 'config.php';

// Handle status updates
if (isset($_POST['feedback_id']) && isset($_POST['action'])) {
    $feedback_id = $_POST['feedback_id'];
    $action = $_POST['action'];
    
    if ($action === 'mark_read') {
        $query = "UPDATE feedback SET status = 'read' WHERE id = ?";
    } elseif ($action === 'mark_replied') {
        $query = "UPDATE feedback SET status = 'replied' WHERE id = ?";
    } elseif ($action === 'delete') {
        $query = "DELETE FROM feedback WHERE id = ?";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all feedback messages
$query = "SELECT * FROM feedback ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Feedback Management</title>
    <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="view.css?v=20250101235000">
    <link rel="stylesheet" href="dark-theme.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="theme.js"></script>
</head>
<body>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
  <div class="sidebar-header">
    <span>Menu</span>
    <button class="close-btn" onclick="closeSidebar()">&times;</button>
  </div>
  <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="admin_feedback.php"><i class="fas fa-comments"></i> Feedback</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  <button id="theme-toggle" style="margin:1rem; padding:0.5rem 1rem; border-radius:6px; border:none; background:#667eea; color:#fff; cursor:pointer; font-size:1rem; display:flex; align-items:center; gap:0.5rem;"><i class="fas fa-moon"></i> Change Theme</button>
</div>
<div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

<header>
  <div class="nav-container">
    <div class="menu-dots" onclick="openSidebar()">
      <i class="fas fa-bars"></i>
    </div>
    <div class="logo">
      <img src="uploads/logo.jpg" alt="Dress Code Logo" class="logo-img" />
      <span class="logo-text">Admin Panel</span>
    </div>
    <div class="nav-links">
      <a href="admin.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="admin_feedback.php" class="nav-link active"><i class="fas fa-comments"></i> Feedback</a>
      <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
</header>

<div class="container">
    <h2><i class="fas fa-comments"></i> Feedback Management</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="feedback-stats">
            <div class="stat-card">
                <i class="fas fa-envelope"></i>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $result->num_rows; ?></span>
                    <span class="stat-label">Total Messages</span>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="<?php echo $row['status'] === 'unread' ? 'unread' : ''; ?>">
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="email-link">
                                <?php echo htmlspecialchars($row['email']); ?>
                            </a>
                        </td>
                        <td>
                            <div class="message-preview">
                                <?php echo htmlspecialchars(substr($row['message'], 0, 100)); ?>
                                <?php if (strlen($row['message']) > 100): ?>
                                    <span class="message-full" style="display: none;">
                                        <?php echo htmlspecialchars($row['message']); ?>
                                    </span>
                                    <button class="btn-toggle" onclick="toggleMessage(this)">Show More</button>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $row['status']; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td class="action-column">
                            <div class="action-buttons">
                                <?php if ($row['status'] === 'unread'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="action" value="mark_read" class="btn btn-small btn-primary">
                                            <i class="fas fa-eye"></i> Mark Read
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($row['status'] !== 'replied'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="action" value="mark_replied" class="btn btn-small btn-success">
                                            <i class="fas fa-reply"></i> Mark Replied
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                    <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="delete" class="btn btn-small btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-feedback">
            <i class="fas fa-inbox"></i>
            <h3>No Feedback Messages</h3>
            <p>No feedback messages have been received yet.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleMessage(button) {
    const messagePreview = button.parentElement;
    const messageFull = messagePreview.querySelector('.message-full');
    
    if (messageFull.style.display === 'none') {
        messageFull.style.display = 'block';
        button.textContent = 'Show Less';
    } else {
        messageFull.style.display = 'none';
        button.textContent = 'Show More';
    }
}

function openSidebar() {
  document.getElementById('sidebar').classList.add('active');
  document.getElementById('sidebar-overlay').style.display = 'block';
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('active');
  document.getElementById('sidebar-overlay').style.display = 'none';
}
</script>

</body>
</html>

<?php
$conn->close();
?> 