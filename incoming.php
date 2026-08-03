<?php 
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit(); }
?>
<?php
if($_SESSION['role'] !== 'admin'){
    header("Location: index.php");
    exit();
}
?>
<?php include 'config.php'; ?>

    <?php
    if(isset($_GET['load_folder'])){
        $base = realpath("scanned_docs");
        $folder = $_GET['load_folder'] ?? "";

        $path = realpath($base . "/" . $folder);

        // Security check
        if(!$path || strpos($path, $base) !== 0){
            exit("Invalid path");
        }

        $files = scandir($path);

        foreach($files as $file){
            if($file === "." || $file === "..") continue;
            if($file[0] === '.') continue;

            $relative = trim($folder . "/" . $file, "/");
            $fullPath = $path . "/" . $file;

           if(is_dir($fullPath)){
    echo "<div class='file-item' onclick=\"openFolder('$relative')\">
            <i class='bi bi-folder-fill text-warning'></i> $file
          </div>";
            } else {

                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                $icon = "<i class='bi bi-file-earmark'></i>";

                if($ext === "pdf"){
                    $icon = "<i class='bi bi-file-earmark-pdf text-danger'></i>";
                } elseif(in_array($ext, ["doc", "docx"])){
                    $icon = "<i class='bi bi-file-earmark-word text-primary'></i>";
                } elseif(in_array($ext, ["xls", "xlsx"])){
                    $icon = "<i class='bi bi-file-earmark-excel text-success'></i>";
                } elseif(in_array($ext, ["jpg", "jpeg", "png", "gif"])){
                    $icon = "<i class='bi bi-file-earmark-image text-info'></i>";
                }

                echo "<div class='file-item' onclick=\"selectFile('$relative')\">
                        $icon $file
                    </div>";
            }
        }
        exit; // VERY IMPORTANT
    }
    ?>
<?php
// Detect edit mode
$is_edit = !empty($id); // true if editing
$button_text = $is_edit ? "UPDATE" : "SAVE RECORD";
?>
<?php
include 'config.php';

$id = $date_recieved = $Sender = $fn = $subject = $subject_link = $action_taken = $remarks = "";

if(isset($_POST['save_record'])){
    $id = $_POST['id'];
    $date_recieved = $_POST['date_recieved'];
    $Sender = $_POST['Sender'];
    $fn = $_POST['fn'];
    $subject = $_POST['subject'];
    $subject_link = $_POST['subject_link']; 
    $action_taken = $_POST['action_taken'];
    $remarks = $_POST['remarks'];

    $fn_check_query = "SELECT id FROM in_info WHERE FN='$fn'";
    if($id != "") { $fn_check_query .= " AND id != '$id'"; }

    $fn_check = mysqli_query($conn, $fn_check_query);
    if(mysqli_num_rows($fn_check) > 0){
        $error_msg = "File No. (FN) already exists!";
        // Don't redirect, keep values in form
    } else {
        if($id == "") {
            mysqli_query($conn, "INSERT INTO in_info (DATE_RECIEVER, SENDER, FN, SUBJECT, SUBJECT_LINK, ACTION_TAKEN, REMARKS) 
                VALUES ('$date_recieved','$Sender','$fn','$subject','$subject_link','$action_taken','$remarks')");
        } else {
            mysqli_query($conn, "UPDATE in_info SET DATE_RECIEVER='$date_recieved', SENDER='$Sender', FN='$fn', SUBJECT='$subject', SUBJECT_LINK='$subject_link', ACTION_TAKEN='$action_taken', REMARKS='$remarks' WHERE id='$id'");
        }
        echo "<script>window.location='incoming.php';</script>";
        exit;
    }
}

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM in_info WHERE id='$delete_id'");
    echo "<script>window.location='incoming.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Government Records Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
html, body {
    height: 100%;
    margin: 0;
    overflow: hidden; /* page itself does not scroll */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f9;
    color: #333;
    display: flex;
}
.file-item {
    cursor: default;
    user-select: none;        /* standard */
    -webkit-user-select: none; /* Chrome/Safari */
    -moz-user-select: none;    /* Firefox */
    -ms-user-select: none;     /* old Edge */
}
/* Sidebar */
.sidebar {
    width: 240px;
    background-color: #0b3d91;
    display: flex;
    flex-direction: column;
    color: white;
    padding-top: 20px;
}

.sidebar .sidebar-brand {
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    font-family: 'Times New Roman', serif;
    font-size: 1.2rem;
}

.sidebar .nav-link {
    color: white !important;
}

.sidebar .nav-link.active {
    background-color: white;
    color: #0b3d91 !important;
}

/* Main content */
.content-wrapper {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    padding: 20px;
    overflow: hidden; /* prevent page scroll */
}

/* Card for form and table */
.light-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    flex: 1;                 /* fill vertical space */
    display: flex;
    flex-direction: column;
    min-height: 0;           /* allow internal scrolling */
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
h6 { font-family:'Times New Roman', serif; font-weight:bold; color:#0b3d91; margin-bottom:15px; text-transform:uppercase; font-size:0.85rem; }
.form-control { font-size:0.85rem; border-radius:6px; }
/* Form styling */
.light-card h6 {
    font-family: 'Times New Roman', serif;
    font-weight: bold;
    margin-bottom: 15px;
    color: #0b3d91;
}

.form-control {
    font-size: 0.85rem;
    border-radius: 6px;
}

/* Save button */
.btn-action-main {
    background-color: #0b3d91;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 8px 20px;
    font-weight: bold;
}

/* Table container scrolls */
.table-container {
    flex: 1;
    overflow-y: auto;
    margin-top: 15px;
    border-top: 1px solid #e0e0e0;
}

/* Make table rows more compact */
.table tbody td, 
.table thead th {
    padding: 6px 8px;       /* smaller vertical and horizontal padding */
    font-size: 0.8rem;     /* smaller font */
    vertical-align: middle; /* align text nicely */
}

.table thead th {
    font-size: 0.8rem;      /* slightly smaller header text */
    padding: 5px 8px;
}

.table tbody tr {
    height: auto;           /* let it shrink naturally */
}

.btn-delete {
    color: #dc3545;
    border: 1px solid #dc3545;
    background: transparent;
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 4px;
    text-decoration: none;
}

.btn-delete:hover { background: #dc3545; color: white; }
</style>
<div id="fileModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:1000;">

    <div style="
        width: 600px;
        max-width: 90%;
        background: #ffffff;
        margin: 8% auto;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        overflow: hidden;
        font-family: Segoe UI, sans-serif;
    ">

        <!-- HEADER -->
        <div style="
            background:#0b3d91;
            color:white;
            padding:12px 16px;
            font-weight:600;
            display:flex;
            justify-content:space-between;
            align-items:center;
        ">
            <span>Select Scanned File</span>
            <button onclick="closeFileModal()" style="
                background:transparent;
                border:none;
                color:white;
                font-size:18px;
                cursor:pointer;
            ">×</button>
        </div>

        <!-- BODY -->
        <div style="padding:15px;">

            <!-- SEARCH BAR -->
            <input type="text" id="fileSearch" placeholder="Search file..."
                style="
                    width:100%;
                    padding:8px 10px;
                    border:1px solid #ddd;
                    border-radius:6px;
                    margin-bottom:10px;
                    font-size:0.85rem;
                "
                onkeyup="filterFiles()"
            >

            <!-- FILE LIST -->
            <div id="fileList" style="
                max-height:360px; /* approx 10 rows */
                overflow-y:auto;
                border:1px solid #e6e6e6;
                border-radius:8px;
            ">
            </div>
        </div>
    </div>
</div>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">SANGGUNIANG BAYAN</div>
    <nav class="nav flex-column mt-2">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link active" href="incoming.php">Incoming</a>
        <a class="nav-link" href="outgoing.php">Outgoing</a>
    </nav>
</aside>

<main class="content-wrapper">
    <div class="light-card">
        <h6 >INCOMING FORM</h6>
       <form method="POST" id="recordForm" class="mb-3">
    <input type="hidden" name="id" id="recordId" value="<?= htmlspecialchars($id) ?>">
    <div class="row g-2">
        <div class="col-md-2">
            <label>Date</label>
            <input type="date" name="date_recieved" id="date_recieved" class="form-control" required value="<?= htmlspecialchars($date_recieved) ?>">
        </div>
        <div class="col-md-3">
            <label>From / Sender</label>
            <input type="text" name="Sender" id="Sender" class="form-control" placeholder="Agency Name" required value="<?= htmlspecialchars($Sender) ?>">
        </div>
        <div class="col-md-1">
            <label>FN#</label>
            <input type="number" name="fn" id="fn" class="form-control" placeholder="00" value="<?= htmlspecialchars($fn) ?>">
        </div>
        <div class="col-md-4">
            <label>Subject / Title</label>
            <input type="text" name="subject" id="subject" class="form-control" placeholder="Document Description" required value="<?= htmlspecialchars($subject) ?>">
        </div>
        <div class="col-md-2 d-grid align-items-end">
           <button type="submit" name="save_record" class="btn-action-main"><?= $button_text ?></button>
        </div>
        <div class="col-md-4">
           <input type="text" name="subject_link" id="subject_link" class="form-control" placeholder="Click to select file" readonly onclick="openFileModal()" value="<?= htmlspecialchars($subject_link) ?>">
        </div>
        <div class="col-md-4">
            <input type="text" name="action_taken" id="action_taken" class="form-control" placeholder="Action Details" value="<?= htmlspecialchars($action_taken) ?>">
        </div>
        <div class="col-md-4">
            <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Remarks" value="<?= htmlspecialchars($remarks) ?>">
        </div>
    </div>
</form>

<?php if(!empty($error_msg)): ?>
<div class="alert alert-danger mt-2"><?= htmlspecialchars($error_msg) ?></div>
<?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="m-0">INCOMING RECORDS</h6>
            <div class="d-flex align-items-center w-25">
                 <span class="me-2">Search:</span>
                <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="Search records...">
            </div>
        </div>

        <div class="table-container">
            <table class="table table-striped" id="recordsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Sender</th>
                        <th>FN</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Tools</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM in_info ORDER BY id DESC");
                    while($row = mysqli_fetch_assoc($result)){
                                             $subj = !empty($row['SUBJECT_LINK']) 
                            ? '<a href="scanned_docs/' . htmlspecialchars($row['SUBJECT_LINK']) . '" target="_blank">' . htmlspecialchars($row['SUBJECT']) . '</a>'
                            : htmlspecialchars($row['SUBJECT']);

                        echo "<tr>
                                <td>#{$row['id']}</td>
                                <td>{$row['DATE_RECIEVER']}</td>
                                <td>{$row['SENDER']}</td>
                                <td>{$row['FN']}</td>
                                <td>{$subj}</td>
                                <td>{$row['ACTION_TAKEN']}<br><small>{$row['REMARKS']}</small></td>
                                <td>
                                    <button class='btn btn-sm btn-light border editBtn' 
                                        data-id='{$row['id']}' data-date_recieved='{$row['DATE_RECIEVER']}'
                                        data-sender='{$row['SENDER']}' data-fn='{$row['FN']}'
                                        data-subject='".htmlspecialchars($row['SUBJECT'], ENT_QUOTES)."'
                                        data-subject_link='".htmlspecialchars($row['SUBJECT_LINK'], ENT_QUOTES)."'
                                        data-action_taken='{$row['ACTION_TAKEN']}' data-remarks='{$row['REMARKS']}'>Edit</button>
                                    <a href='incoming.php?delete={$row['id']}' class='btn-delete' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
<script>
// Edit button functionality
document.querySelectorAll(".editBtn").forEach(button => {
    button.addEventListener("click", function(){
        const d = this.dataset;
        document.getElementById("recordId").value = d.id;
        document.getElementById("date_recieved").value = d.date_recieved;
        document.getElementById("Sender").value = d.sender;
        document.getElementById("fn").value = d.fn;
        document.getElementById("subject").value = d.subject;
        document.getElementById("subject_link").value = d.subject_link;
        document.getElementById("action_taken").value = d.action_taken;
        document.getElementById("remarks").value = d.remarks;

        // Change button text to UPDATE
        document.querySelector('button[name="save_record"]').textContent = "UPDATE";
    });
});

// Live search
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase().trim();
    let keywords = filter.split(/\s+/).filter(k => k.length > 0);
    document.querySelectorAll("#recordsTable tbody tr").forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = (keywords.length === 0 || keywords.every(k => text.includes(k))) ? "" : "none";
    });
});
let currentPath = "";

function loadFiles(folder = ""){
    currentPath = folder;

    fetch("incoming.php?load_folder=" + encodeURIComponent(folder))
    .then(res => res.text())
    .then(data => {
        let fileList = document.getElementById("fileList");
        fileList.innerHTML = data;

        // BACK button
        if(folder !== ""){
            let back = document.createElement("div");
            back.innerHTML = "⬅️ Back";
            back.style.padding = "10px";
            back.style.cursor = "pointer";
            back.style.fontWeight = "bold";
            back.onclick = () => {
                let parent = folder.split("/").slice(0, -1).join("/");
                loadFiles(parent);
            };
            fileList.prepend(back);
        }
    });
}

function openFolder(folder){
    loadFiles(folder);
}

function openFileModal(){
    document.getElementById("fileModal").style.display = "block";
    loadFiles(""); // load root
}

function closeFileModal(){
    document.getElementById("fileModal").style.display = "none";
}

function selectFile(filename){
    document.getElementById("subject_link").value = filename;
    closeFileModal();
}
function filterFiles(){
    let input = document.getElementById("fileSearch").value.toLowerCase();
    let items = document.querySelectorAll(".file-item");

    items.forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(input) ? "block" : "none";
    });
}
</script>

</html>