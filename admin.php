<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

include 'config.php';

// Fetch orders by status
$new_query = "SELECT dr.*, u.name, u.email FROM dress_requests dr
              JOIN users u ON dr.user_email = u.email
              WHERE dr.status = 'pending'
              ORDER BY dr.created_at DESC";

$processing_query = "SELECT dr.*, u.name, u.email FROM dress_requests dr
                     JOIN users u ON dr.user_email = u.email
                     WHERE dr.status = 'accepted'
                     ORDER BY dr.created_at DESC";

$delivered_query = "SELECT dr.*, u.name, u.email FROM dress_requests dr
                    JOIN users u ON dr.user_email = u.email
                    WHERE dr.status = 'delivered'
                    ORDER BY dr.created_at DESC";

$received_query = "SELECT dr.*, u.name, u.email FROM dress_requests dr
                   JOIN users u ON dr.user_email = u.email
                   WHERE dr.status = 'received'
                   ORDER BY dr.created_at DESC";

$rejected_query = "SELECT dr.*, u.name, u.email FROM dress_requests dr
                   JOIN users u ON dr.user_email = u.email
                   WHERE dr.status = 'rejected'
                   ORDER BY dr.created_at DESC";

$new_result = $conn->query($new_query);
$processing_result = $conn->query($processing_query);
$delivered_result = $conn->query($delivered_query);
$received_result = $conn->query($received_query);
$rejected_result = $conn->query($rejected_query);

// Get counts for each status
$new_count = $new_result->num_rows;
$processing_count = $processing_result->num_rows;
$delivered_count = $delivered_result->num_rows;
$received_count = $received_result->num_rows;
$rejected_count = $rejected_result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Order Management</title>
    <link rel="stylesheet" href="admin.css?v=20250101241000">
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
      <a href="admin_feedback.php" class="nav-link"><i class="fas fa-comments"></i> Feedback</a>
      <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
</header>

<div class="container">
    <h2><i class="fas fa-shopping-bag"></i> Order Management Dashboard</h2>

    <!-- Order Statistics -->
    <div class="order-stats">
        <div class="stat-card">
            <i class="fas fa-plus-circle"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $new_count; ?></div>
                <div class="stat-label">New Requests</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $processing_count; ?></div>
                <div class="stat-label">Accepted Orders</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-truck"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $delivered_count; ?></div>
                <div class="stat-label">Ready for Pickup</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $received_count; ?></div>
                <div class="stat-label">Received by Customer</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-times-circle"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $rejected_count; ?></div>
                <div class="stat-label">Rejected Orders</div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <button class="tab-btn active" onclick="showTab('new')">
            <i class="fas fa-plus-circle"></i> New Requests (<?php echo $new_count; ?>)
        </button>
        <button class="tab-btn" onclick="showTab('processing')">
            <i class="fas fa-clock"></i> Accepted  (<?php echo $processing_count; ?>)
        </button>
        <button class="tab-btn" onclick="showTab('delivered')">
            <i class="fas fa-truck"></i> Ready for Pickup (<?php echo $delivered_count; ?>)
        </button>
        <button class="tab-btn" onclick="showTab('received')">
            <i class="fas fa-check-circle"></i> Received by Customer (<?php echo $received_count; ?>)
        </button>
        <button class="tab-btn" onclick="showTab('rejected')">
            <i class="fas fa-times-circle"></i> Rejected  (<?php echo $rejected_count; ?>)
        </button>
    </div>

    <!-- New Orders Tab -->
    <div id="new" class="tab-content active">
        <h3><i class="fas fa-plus-circle"></i> New Requests</h3>
        <?php if ($new_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Contact No</th>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $new_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo $row['size']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo $row['timeline']; ?></td>
                        <td>
                            <span class="status-badge status-new">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td class="action-column">
                            <form action="update_status.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="accept" class="btn btn-success" style="margin-bottom: 5px; width: 100%;">
                                    <i class="fas fa-check"></i> Accept
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger" style="width: 100%;">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-plus-circle"></i>
                <h3>No New Requests</h3>
                <p>There are currently no new requests pending review.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Processing Orders Tab (Accepted) -->
    <div id="processing" class="tab-content">
        <h3><i class="fas fa-clock"></i> Accepted Orders</h3>
        <?php if ($processing_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Contact No</th>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $processing_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo $row['size']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo $row['timeline']; ?></td>
                        <td>
                            <span class="status-badge status-accepted">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td class="action-column">
                            <form action="update_status.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="deliver" class="btn btn-primary">
                                    <i class="fas fa-truck"></i> Mark Ready
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger" style="width: 100%;">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-clock"></i>
                <h3>No Accepted Orders</h3>
                <p>There are currently no accepted orders.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Delivered Orders Tab -->
    <div id="delivered" class="tab-content">
        <h3><i class="fas fa-truck"></i> Ready for Pickup</h3>
        <?php if ($delivered_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Contact No</th>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Ready Date</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $delivered_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo $row['size']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo $row['timeline']; ?></td>
                        <td>
                            <span class="status-badge status-delivered">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td class="action-column">
                            <div style="text-align: center; color: #667eea; font-weight: 600;">
                                <i class="fas fa-truck"></i><br>
                                Ready for Pickup
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-truck"></i>
                <h3>No Orders Ready for Pickup</h3>
                <p>There are currently no orders marked as ready for pickup.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Received Orders Tab -->
    <div id="received" class="tab-content">
        <h3><i class="fas fa-check-circle"></i> Received by Customer</h3>
        <?php if ($received_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Contact No</th>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Received Date</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $received_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo $row['size']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo $row['timeline']; ?></td>
                        <td>
                            <span class="status-badge status-received">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td class="action-column">
                            <div style="text-align: center; color: #10b981; font-weight: 600;">
                                <i class="fas fa-check-circle"></i><br>
                                Completed
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-check-circle"></i>
                <h3>No Received Orders</h3>
                <p>There are currently no orders received by customers.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Rejected Orders Tab -->
    <div id="rejected" class="tab-content">
        <h3><i class="fas fa-times-circle"></i> Rejected Orders</h3>
        <?php if ($rejected_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Contact No</th>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Rejection Date</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $rejected_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo $row['size']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo $row['timeline']; ?></td>
                        <td>
                            <span class="status-badge status-rejected">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td class="action-column">
                            <div style="text-align: center; color: #ef4444; font-weight: 600;">
                                <i class="fas fa-times-circle"></i><br>
                                Rejected
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-times-circle"></i>
                <h3>No Rejected Orders</h3>
                <p>There are currently no rejected orders.</p>
            </div>
        <?php } ?>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });

    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById(tabName).classList.add('active');

    // Add active class to clicked button
    event.target.classList.add('active');
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