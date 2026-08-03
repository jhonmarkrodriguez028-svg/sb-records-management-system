<?php 
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include "config.php";

// Get search input
$search = isset($_GET['search']) ? trim(mysqli_real_escape_string($conn, $_GET['search'])) : "";

// Base query
$query = "SELECT * FROM out_info";

// Multi-keyword AND search
if(!empty($search)){
    $keywords = preg_split('/\s+/', $search);
    $conditions = [];

    foreach($keywords as $word){
        $word = strtolower(trim($word));
        if($word !== ''){
            $conditions[] = "(
                LOWER(SENT) LIKE '%$word%' OR
                LOWER(ADDRESS) LIKE '%$word%' OR
                LOWER(SUBJECT) LIKE '%$word%' OR
                LOWER(FN) LIKE '%$word%' OR
                LOWER(RECIEVED_BY) LIKE '%$word%' OR
                LOWER(REMARKS) LIKE '%$word%'
            )";
        }
    }

    if(count($conditions) > 0){
        $query .= " WHERE " . implode(' AND ', $conditions);
    }
}

$query .= " ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Outgoing Records | Legislative System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
:root {
    --main-bg: #f4f6f9;
    --card-bg: #ffffff;
    --sidebar-bg: #0b3d91;
    --border-color: #d1d1d1;
    --text-light: #ffffff;
    --accent-color: #0b3d91;
}

/* ===== SAME AS INCOMING DESIGN ===== */

body { 
    background-color: var(--main-bg);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    display: flex;
    height: 100vh;
    overflow: hidden;
    color: #333;
}

/* Sidebar (same as incoming) */
.sidebar {
    width: 240px;
    background-color: var(--sidebar-bg);
    display: flex;
    flex-direction: column;
    color: var(--text-light);
}

.sidebar .sidebar-brand {
    font-weight: bold;
    text-align: center;
    padding: 20px;
    font-size: 1.2rem;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.sidebar .nav-link {
    color: white !important;
    padding: 12px 20px;
    font-size: 0.9rem;
}

.sidebar .nav-link.active {
    background-color: white;
    color: #0b3d91 !important;
}

/* Main */
.content-wrapper {
    flex-grow: 1;
    padding: 20px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Card (same feel as incoming) */
.light-card {
    background-color: var(--card-bg);
    border-radius: 12px;
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Title */
h6 { 
    color: var(--accent-color); 
    font-family: 'Times New Roman', serif; 
    font-weight: bold; 
    margin-bottom: 10px; 
    font-size: 0.9rem; 
    text-transform: uppercase; 
}

/* Table container */
.table-container {
    flex-grow: 1;
    overflow-y: auto;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background-color: #fff;
}

/* Table styling (MATCH INCOMING) */
.table { margin: 0; background: #fff; }

.table thead th {
    background: #f1f3f5;
    font-size: 0.75rem;
    padding: 6px 8px;
    text-transform: uppercase;
    position: sticky;
    top: 0;
    z-index: 5;
    border-bottom: 1px solid var(--border-color);
}

.table tbody td {
    font-size: 0.8rem;
    padding: 6px 8px;
    border-bottom: 1px solid #f1f3f5;
    vertical-align: middle;
}

/* Search */
#searchInput { margin-bottom: 10px; }

/* ===== SPREADSHEET PREVIEW (UNCHANGED FUNCTION) ===== */

#hoverPreview {
    position: fixed;
    width: 450px;
    height: 320px;
    background: #fff;
    border: 1px solid #ccc;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    z-index: 9999;
    display: none;
    border-radius: 6px;
    overflow: hidden;
}

#hoverPreview iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.preview-link {
    cursor: pointer;
}

</style>
</head>

<body>

<aside class="sidebar">
    <div class="sidebar-brand" style="font-weight:bold; font-family:'Bookman Old Style';">
        SANGGUNIANG <br> BAYAN
    </div>

    <nav class="nav flex-column mt-2">
        <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">Home</a>
        <a class="nav-link <?= ($current_page == 'view_outgoing_records.php') ? 'active' : '' ?>" href="view_outgoing_records.php">Outgoing Records</a>
        <a class="nav-link <?= ($current_page == 'view_incoming_records.php') ? 'active' : '' ?>" href="view_incoming_records.php">Incoming Records</a>
    </nav>
</aside>

<main class="content-wrapper">
    <div class="light-card">

        <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="m-0">OUTGOING RECORDS</h6>
            <div class="d-flex align-items-center w-25">
                <span class="me-2">Search:</span>
                <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="Search records...">
            </div>
        </div>

        <div class="table-container">
            <table class="table table-striped table-bordered" id="recordsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sent</th>
                        <th>Address</th>
                        <th>File No.</th>
                        <th>Subject</th>
                        <th>Received By</th>
                        <th>Remarks</th>
                        <th>Date Received</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){

$baseUrl = dirname($_SERVER['PHP_SELF']);

$fileLink = !empty($row['SUBJECT_LINK']) 
    ? $baseUrl . '/scanned_docs/' . rawurlencode($row['SUBJECT_LINK'])
    : '';

$subject_display = !empty($row['SUBJECT_LINK']) 
    ? '<a href="'.$fileLink.'" target="_blank" class="preview-link" data-file="'.$fileLink.'">'
    . htmlspecialchars($row['SUBJECT']) . '</a>'
    : htmlspecialchars($row['SUBJECT']);

echo "<tr>
<td>{$row['id']}</td>
<td>{$row['SENT']}</td>
<td>{$row['ADDRESS']}</td>
<td>{$row['FN']}</td>
<td>{$subject_display}</td>
<td>{$row['RECIEVED_BY']}</td>
<td>{$row['REMARKS']}</td>
<td>{$row['DATE_RECIEVED']}</td>
</tr>";

                    }
                }
                ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<!-- SPREADSHEET PREVIEW BOX -->
<div id="hoverPreview">
    <iframe id="previewFrame"></iframe>
</div>

<script>

const preview = document.getElementById("hoverPreview");
const frame = document.getElementById("previewFrame");

let isInsidePreview = false;

document.querySelectorAll(".preview-link").forEach(link => {

    link.addEventListener("mouseenter", function(e) {
        frame.src = this.dataset.file;
        preview.style.display = "block";
        movePreview(e);
    });

    link.addEventListener("mousemove", function(e) {
        movePreview(e);
    });

    link.addEventListener("mouseleave", function() {
        setTimeout(checkClose, 100);
    });

});

preview.addEventListener("mouseenter", function() {
    isInsidePreview = true;
});

preview.addEventListener("mouseleave", function() {
    isInsidePreview = false;
    setTimeout(checkClose, 100);
});

function movePreview(e){
    preview.style.top = (e.clientY + 15) + "px";
    preview.style.left = (e.clientX + 15) + "px";
}

function checkClose(){
    let hoveringLink = document.querySelector(".preview-link:hover");

    if(!hoveringLink && !isInsidePreview){
        preview.style.display = "none";
        frame.src = "";
    }
}
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase().trim();
    let keywords = filter.split(/\s+/).filter(k => k.length > 0);

    document.querySelectorAll("#recordsTable tbody tr").forEach(row => {
        let text = row.innerText.toLowerCase();

        row.style.display = (keywords.length === 0 || 
            keywords.every(k => text.includes(k))) ? "" : "none";
    });
});
</script>

</body>
</html>