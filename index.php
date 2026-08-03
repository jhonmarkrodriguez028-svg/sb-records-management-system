<?php 
include 'config.php'; 
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>SB Records Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    height: 100%;
    margin: 0;
    overflow: hidden; /* prevent scrolling */
}

/* Official Government Header */
.gov-header {
    background-color: #0b3d91; /* Deep Blue */
    color: white;
    padding: 20px 0;
}

.gov-header h1 {
    font-family: 'Times New Roman', serif;
    font-weight: bold;
    letter-spacing: 1px;
    margin: 0;
}

/* Navbar Dropdown */
.menu-btn {
    background: transparent;
    border: none;
    color: white;
    font-size: 20px;
}
.dropdown-menu {
    min-width: 200px;  /* adjust as needed */
}

/* Cards */
.dashboard-card {
    background: white;
    border-radius: 8px;
    border: 1px solid #dcdcdc;
    padding: 30px;
    transition: 0.2s ease;
}

.dashboard-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.dashboard-card h5 {
    font-weight: 600;
    color: #333;
}

.dashboard-card .count {
    font-size: 48px;
    font-weight: 700;
    color: #0b3d91;
}

/* Buttons */
.btn-primary-custom {
    background-color: #0b3d91;
    border: none;
}

.btn-primary-custom:hover {
    background-color: #072c66;
}

.btn-outline-custom {
    border: 1px solid #0b3d91;
    color: #0b3d91;
}

.btn-outline-custom:hover {
    background-color: #0b3d91;
    color: white;
}

.section-title {
    font-weight: 600;
    color: #333;
}
.gov-header img {
    height: 60px;
    width: auto;
    display: block;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="gov-header">
    <div class="container position-relative text-center">
    <div class="d-flex align-items-center justify-content-center gap-3">
    <img src="images/SB.png" alt="Logo" style="height:60px; border-radius: 100px;">    
    <h1 class="m-0">SANGGUNIANG BAYAN</h1>
    <img src="images/sdc.png" alt="Logo" style="height:50px; border-radius: 50px;">
</div>

        <!-- Dropdown -->
        <div class="dropdown position-absolute end-0 top-50 translate-middle-y">
            <button class="menu-btn" data-bs-toggle="dropdown" >
                ☰
            </button>

           <ul class="dropdown-menu dropdown-menu-center shadow" aria-labelledby="userDropdown">
                <li class="px-3 py-2 text-center">
                    <small class="text-muted d-block">Logged in as:</small>
                    <small class="d-block">
                        <strong>
                            <?php 
                                echo $_SESSION['fullname'] . " (" . ucfirst($_SESSION['role']) . ")";
                            ?>
                        </strong>
                    </small>
                </li>
        <li><hr class="my-1"></li>
        <li class="px-3 pb-2 text-center">
            <a href="logout.php" class="btn btn-danger w-100">
                Logout
            </a>
        </li>
        
    </ul>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container py-4 d-flex flex-column justify-content-center h-100">

    <div class="text-center mb-4">
        <h2 class="section-title">Records Management Dashboard</h2>
        <p class="text-muted">
            Management of Incoming and Outgoing Official Documents
        </p>
    </div>

    <!-- DASHBOARD CARDS -->
    <div class="row g-4">

        <!-- Incoming -->
        <div class="col-md-6">
            <div class="dashboard-card text-center h-100">
                <h5>Incoming Records</h5>

                <?php
                    $incoming_count = mysqli_fetch_assoc(
                        mysqli_query($conn, "SELECT COUNT(*) as total FROM in_info")
                    );
                    echo "<div class='count'>{$incoming_count['total']}</div>";
                ?>

                <a href="view_incoming_records.php"
                   class="btn btn-outline-custom mt-3">
                    View Records
                </a>
            </div>
        </div>

        <!-- Outgoing -->
        <div class="col-md-6">
            <div class="dashboard-card text-center h-100">
                <h5>Outgoing Records</h5>

                <?php
                    $outgoing_count = mysqli_fetch_assoc(
                        mysqli_query($conn, "SELECT COUNT(*) as total FROM out_info")
                    );
                    echo "<div class='count'>{$outgoing_count['total']}</div>";
                ?>

                <a href="view_outgoing_records.php"
                   class="btn btn-outline-custom mt-3">
                    View Records
                </a>
            </div>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
   <?php if($_SESSION['role'] == 'admin'){ ?>

<div class="row mt-5 g-3">

    <div class="col-md-6">
        <a href="incoming.php"
           class="btn btn-primary-custom w-100 py-2 text-white">
           Add New Incoming Record
        </a>
    </div>

    <div class="col-md-6">
        <a href="outgoing.php"
           class="btn btn-primary-custom w-100 py-2 text-white">
           Add New Outgoing Record
        </a>
    </div>

</div>

<?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>