<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include 'config.php';

$email = $_SESSION['email'];

// Fetch user's requests by status
$pending_query = "SELECT * FROM dress_requests WHERE user_email = ? AND status = 'pending' ORDER BY created_at DESC";
$accepted_query = "SELECT * FROM dress_requests WHERE user_email = ? AND status = 'accepted' ORDER BY created_at DESC";
$delivered_query = "SELECT * FROM dress_requests WHERE user_email = ? AND status = 'delivered' ORDER BY created_at DESC";
$received_query = "SELECT * FROM dress_requests WHERE user_email = ? AND status = 'received' ORDER BY created_at DESC";
$rejected_query = "SELECT * FROM dress_requests WHERE user_email = ? AND status = 'rejected' ORDER BY created_at DESC";
$canceled_query = "SELECT * FROM dress_requests WHERE user_email = ? AND status = 'canceled' ORDER BY created_at DESC";

// Prepare and execute queries
$stmt = $conn->prepare($pending_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$pending_result = $stmt->get_result();
$pending_count = $pending_result->num_rows;

$stmt = $conn->prepare($accepted_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$accepted_result = $stmt->get_result();
$accepted_count = $accepted_result->num_rows;

$stmt = $conn->prepare($delivered_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$delivered_result = $stmt->get_result();
$delivered_count = $delivered_result->num_rows;

$stmt = $conn->prepare($received_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$received_result = $stmt->get_result();
$received_count = $received_result->num_rows;

$stmt = $conn->prepare($rejected_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$rejected_result = $stmt->get_result();
$rejected_count = $rejected_result->num_rows;

$stmt = $conn->prepare($canceled_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$canceled_result = $stmt->get_result();
$canceled_count = $canceled_result->num_rows;

$total_requests = $pending_count + $accepted_count + $delivered_count + $received_count + $rejected_count + $canceled_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests Dashboard</title>
    <link rel="stylesheet" href="view.css?v=20250101240000">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dark-theme.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="theme.js"></script>
</head>
<body>

<header>
    <div class="nav-container">
        <div class="menu-dots" onclick="openSidebar()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="logo">
            <img src="uploads/logo.jpg" alt="Dress Code Logo" class="logo-img">
            <span class="logo-text">Dress Code</span>
        </div>
        <a href="home.php" class="nav-link" style="margin-left: auto">
            <i class="fas fa-home"></i> Home
        </a>
    </div>
</header>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <span>Menu</span>
        <button class="close-btn" onclick="closeSidebar()">&times;</button>
    </div>
    <a href="home.php"><i class="fas fa-home"></i> Home</a>
    <a href="request.html"><i class="fas fa-tshirt"></i> Request Dress</a>
    <a href="view_request.php"><i class="fas fa-eye"></i> View Requests</a>
    <a href="about.html"><i class="fas fa-info-circle"></i> About</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <button id="theme-toggle" style="margin:1rem; padding:0.5rem 1rem; border-radius:6px; border:none; background:#667eea; color:#fff; cursor:pointer; font-size:1rem; display:flex; align-items:center; gap:0.5rem;"><i class="fas fa-moon"></i> Change Theme</button>
</div>
<div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>
<style>
.nav-container {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 0.5rem;
    padding-left: 0.5rem;
}
.menu-dots {
    margin: 0;
    padding: 0;
    font-size: 1.5rem;
    color: #4a5568;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: color 0.2s;
}
.menu-dots:hover {
    color: #667eea;
}
.nav-links {
    margin-left: auto;
    display: flex;
    gap: 0.5rem;
}
.sidebar {
    position: fixed;
    top: 0;
    left: 0 !important;
    width: 240px;
    height: 100%;
    background: #fff;
    box-shadow: 2px 0 12px rgba(0,0,0,0.08);
    z-index: 3000;
    transition: left 0.3s;
    display: flex;
    flex-direction: column;
    padding-top: 1rem;
}
.sidebar:not(.active) {
    left: -240px !important;
}
.sidebar.active {
    left: 0 !important;
}
.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1rem 1rem 1rem;
    border-bottom: 1px solid #eee;
    font-weight: 600;
    font-size: 1.1rem;
}
.close-btn {
    background: none;
    border: none;
    font-size: 2rem;
    color: #667eea;
    cursor: pointer;
}
.sidebar a {
    padding: 1rem;
    color: #4a5568;
    text-decoration: none;
    font-size: 1rem;
    border-bottom: 1px solid #f1f1f1;
    transition: background 0.2s, color 0.2s;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.sidebar a:hover {
    background: #f3f4f6;
    color: #667eea;
}
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.2);
    z-index: 1999;
}
.sidebar.active ~ #sidebar-overlay {
    display: block;
}
@media (max-width: 768px) {
    .nav-container {
        padding-left: 0.5rem;
    }
    .sidebar {
        width: 80vw;
        min-width: 180px;
        max-width: 320px;
    }
}
.logo {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-left: 1.5rem;
}
.logo i {
    font-size: 2rem;
}
.logo-img {
    height: 48px;
    width: 48px;
    display: block;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}
.logo-img:hover {
    transform: scale(1.1);
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}
body.dark-theme .logo-img {
    background: #fff;
    border: 2px solid #fff;
}
.logo-text {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-left: 0;
}
.nav-container .logo-img:hover {
  background: #fff !important;
}
body.dark-theme .nav-container .logo-img:hover {
  background: #fff !important;
}
</style>
<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('active');
    document.getElementById('sidebar-overlay').style.display = 'block';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('active');
    document.getElementById('sidebar-overlay').style.display = 'none';
}
</script>

<div class="container">
    <h2><i class="fas fa-shopping-bag"></i> My Requests Dashboard</h2>
    
    <!-- Welcome Message -->
    <div class="welcome-section">
        <div class="welcome-card">
            <i class="fas fa-user"></i>
            <div class="welcome-info">
                <h3>Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>!</h3>
                <p>Track your dress requests and manage your orders</p>
            </div>
        </div>
    </div>

    <!-- Request Statistics -->
    <div class="order-stats">
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $pending_count; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $accepted_count; ?></div>
                <div class="stat-label">Accepted</div>
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
            <i class="fas fa-star"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $received_count; ?></div>
                <div class="stat-label">Received</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-times-circle"></i>
            <div class="stat-info">
                <div class="stat-number"><?php echo $rejected_count; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <button class="tab-btn active" onclick="showTab('pending')">
            <i class="fas fa-clock"></i> Pending (<?php echo $pending_count; ?>)
        </button>
        <button class="tab-btn" onclick="showTab('accepted')">
            <i class="fas fa-check-circle"></i> Accepted (<?php echo $accepted_count; ?>)
        </button>
        <button class="tab-btn" onclick="showTab('delivered')">
            <i class="fas fa-truck"></i> Ready for Pickup (<?php echo $delivered_count; ?>)
        </button>
        <button class="tab-btn" onclick="showTab('received')">
            <i class="fas fa-star"></i> Received (<?php echo $received_count; ?>)
        </button>
        <button class="tab-btn" onclick="showTab('rejected')">
            <i class="fas fa-times-circle"></i> Rejected (<?php echo $rejected_count; ?>)
        </button>
    </div>

    <!-- Pending Requests Tab -->
    <div id="pending" class="tab-content active">
        <h3><i class="fas fa-clock"></i> Pending Requests</h3>
        <?php if ($pending_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Contact No</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $pending_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['size']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo htmlspecialchars($row['timeline']); ?></td>
                        <td>
                            <span class="status-badge status-pending">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="cancel_request.php?cancel_id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure you want to cancel this request?')"
                               class="btn btn-danger"
                               target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-clock"></i>
                <h3>No Pending Requests</h3>
                <p>You don't have any pending requests at the moment.</p>
                <a href="request.html" class="btn btn-primary">Make a New Request</a>
            </div>
        <?php } ?>
    </div>

    <!-- Accepted Requests Tab -->
    <div id="accepted" class="tab-content">
        <h3><i class="fas fa-check-circle"></i> Accepted Requests</h3>
        <?php if ($accepted_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Contact No</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $accepted_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['size']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo htmlspecialchars($row['timeline']); ?></td>
                        <td>
                            <span class="status-badge status-accepted">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="cancel_request.php?cancel_id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure you want to cancel this request?')"
                               class="btn btn-danger"
                               target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-check-circle"></i>
                <h3>No Accepted Requests</h3>
                <p>You don't have any accepted requests at the moment.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Delivered Requests Tab -->
    <div id="delivered" class="tab-content">
        <h3><i class="fas fa-truck"></i> Ready for Pickup</h3>
        <?php if ($delivered_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Contact No</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $delivered_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['size']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo htmlspecialchars($row['timeline']); ?></td>
                        <td>
                            <span class="status-badge status-delivered">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <form action="mark_received.php" method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" 
                                        onclick="return confirm('Confirm that you have received your order?')"
                                        class="btn btn-success"
                                        style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:fit-content;">
                                    <i class="fas fa-check"></i> Mark Received
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
                <i class="fas fa-truck"></i>
                <h3>No Orders Ready for Pickup</h3>
                <p>You don't have any orders ready for pickup at the moment.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Received Requests Tab -->
    <div id="received" class="tab-content">
        <h3><i class="fas fa-star"></i> Received Orders</h3>
        <?php if ($received_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Contact No</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $received_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['size']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo htmlspecialchars($row['timeline']); ?></td>
                        <td>
                            <span class="status-badge status-received">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="feedback.php" class="btn btn-primary">
                                <i class="fas fa-comment"></i> Leave Feedback
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-star"></i>
                <h3>No Completed Orders</h3>
                <p>You don't have any completed orders yet.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Rejected Requests Tab -->
    <div id="rejected" class="tab-content">
        <h3><i class="fas fa-times-circle"></i> Rejected Requests</h3>
        <?php if ($rejected_count > 0) { ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Dress Image</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Contact No</th>
                        <th>Details</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $rejected_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <a href="<?php echo $row['image']; ?>" target="_blank" title="Click to view full image">
                                <img src="<?php echo $row['image']; ?>" alt="Dress" style="width: 80px; height: 80px; cursor: pointer;">
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['size']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo htmlspecialchars($row['timeline']); ?></td>
                        <td>
                            <span class="status-badge status-rejected">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="request.html" class="btn btn-primary"
                               target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-plus"></i> Make New Request
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        <?php } else { ?>
            <div class="no-orders">
                <i class="fas fa-times-circle"></i>
                <h3>No Rejected Requests</h3>
                <p>You don't have any rejected requests.</p>
            </div>
        <?php } ?>
    </div>
</div>

<footer class="simple-footer">
    <p><i class="fas fa-copyright"></i> 2025 Dress Code | <i class="fas fa-exclamation-triangle"></i> We are not responsible if the requested dress is not available in our store within the specified time.</p>
</footer>

<script>
function showTab(tabName) {
    // Hide all tab contents
    var tabContents = document.getElementsByClassName('tab-content');
    for (var i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove('active');
    }
    
    // Remove active class from all tab buttons
    var tabButtons = document.getElementsByClassName('tab-btn');
    for (var i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }
    
    // Show the selected tab content
    document.getElementById(tabName).classList.add('active');
    
    // Add active class to the clicked button
    event.target.classList.add('active');
}
</script>

</body>
</html>

<?php 
$stmt->close();
$conn->close(); 
?>
