<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include 'config.php';

$email = $_SESSION['email'];

// Fetch user details
$query = "SELECT name, email FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home</title>
    <link rel="stylesheet" href="home-style.css?v=<?php echo time(); ?>">
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
        <div class="nav-links" style="margin-left: auto">
            <a href="home.php" class="nav-link active"><i class="fas fa-home"></i> Home</a>
            <a href="request.html" class="nav-link"><i class="fas fa-tshirt"></i> Request Dress</a>
            <a href="view_request.php" class="nav-link"><i class="fas fa-eye"></i> View Requests</a>
            <a href="about.html" class="nav-link"><i class="fas fa-info-circle"></i> About</a>
            <!-- <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a> -->
        </div>
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
    left: -260px;
    width: 240px;
    height: 100%;
    background: #fff;
    box-shadow: 2px 0 12px rgba(0,0,0,0.08);
    z-index: 2000;
    transition: left 0.3s;
    display: flex;
    flex-direction: column;
    padding-top: 1rem;
}
.sidebar.active {
    left: 0;
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
    gap: 0.75rem;
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
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
    margin-left: 0.75rem;
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
    <div class="premium-welcome-card animate-fade-in">
        <div class="welcome-icon">
            <i class="fas fa-crown"></i>
        </div>
        <div class="welcome-text">
            <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
            <p class="subtitle">Experience premium dress customization and style.</p>
        </div>
    </div>
    <div class="btn-container animate-fade-in">
        <a href="request.html" class="btn btn-premium">
            <i class="fas fa-tshirt"></i> Request a Dress
        </a>
        <a href="view_request.php" class="btn btn-premium">
            <i class="fas fa-eye"></i> View Requests
        </a>
    </div>
    <div class="motivation-card animate-fade-in">
        <i class="fas fa-quote-left"></i>
        <p>"Fashion is the armor to survive the reality of everyday life."</p>
        <span>- Bill Cunningham</span>
    </div>
</div>

<footer class="simple-footer">
    <p><i class="fas fa-copyright"></i> 2025 Dress Code | <i class="fas fa-exclamation-triangle"></i> We are not responsible if the requested dress is not available in our store within the specified time.</p>
</footer>

</body>
</html>

<?php
$conn->close();
?>