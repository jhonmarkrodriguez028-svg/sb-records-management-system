<?php 
session_start();
include 'config.php';

// =========================
// AUTH
// =========================
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] !== 'admin'){
    header("Location: index.php");
    exit();
}

// =========================
// FILE EXPLORER (AJAX)
// =========================
if(isset($_GET['load_folder'])){
    $base = realpath("scanned_docs");
    $folder = $_GET['load_folder'] ?? "";
    $path = realpath($base . "/" . $folder);

    if(!$path || strpos($path, $base) !== 0){
        exit("Invalid path");
    }

    foreach(scandir($path) as $file){
        if($file === "." || $file === ".." || $file[0] === '.') continue;

        $relative = trim($folder . "/" . $file, "/");
        $fullPath = $path . "/" . $file;

        if(is_dir($fullPath)){
            echo "<div class='file-item' onclick=\"openFolder('$relative')\">
                    <i class='bi bi-folder-fill text-warning'></i> $file
                  </div>";
        } else {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            $icon = "<i class='bi bi-file-earmark'></i>";
            if($ext === "pdf") $icon = "<i class='bi bi-file-earmark-pdf text-danger'></i>";
            elseif(in_array($ext, ["doc","docx"])) $icon = "<i class='bi bi-file-earmark-word text-primary'></i>";
            elseif(in_array($ext, ["xls","xlsx"])) $icon = "<i class='bi bi-file-earmark-excel text-success'></i>";
            elseif(in_array($ext, ["jpg","jpeg","png","gif"])) $icon = "<i class='bi bi-file-earmark-image text-info'></i>";

            echo "<div class='file-item' onclick=\"selectFile('$relative')\">
                    $icon $file
                  </div>";
        }
    }
    exit;
}

// =========================
// VARIABLES
// =========================
$id = $sent = $address = $fn = $subject = $subject_link = $received_by = $remarks = $date_received = "";
$error_msg = "";
$current_page = basename($_SERVER['PHP_SELF']);

// =========================
// SAVE / UPDATE
// =========================
if(isset($_POST['save_record'])){
    extract($_POST);

    $fn_check = mysqli_query($conn, "SELECT id FROM out_info WHERE FN='$fn'" . ($id ? " AND id!='$id'" : ""));

    if(mysqli_num_rows($fn_check) > 0){
        $error_msg = "File No. already exists!";
    } else {

        if(empty($id)){
            mysqli_query($conn, "INSERT INTO out_info 
                (SENT, ADDRESS, FN, SUBJECT, SUBJECT_LINK, RECIEVED_BY, REMARKS, DATE_RECIEVED)
                VALUES ('$sent','$address','$fn','$subject','$subject_link','$received_by','$remarks','$date_received')");
        } else {
            mysqli_query($conn, "UPDATE out_info SET
                SENT='$sent',
                ADDRESS='$address',
                FN='$fn',
                SUBJECT='$subject',
                SUBJECT_LINK='$subject_link',
                RECIEVED_BY='$received_by',
                REMARKS='$remarks',
                DATE_RECIEVED='$date_received'
                WHERE id='$id'");
        }

        header("Location: outgoing.php");
        exit;
    }
}

// =========================
// DELETE
// =========================
if(isset($_GET['delete'])){
    mysqli_query($conn, "DELETE FROM out_info WHERE id='".intval($_GET['delete'])."'");
    header("Location: outgoing.php");
    exit;
}

// =========================
// FETCH DATA
// =========================
$result = mysqli_query($conn, "SELECT * FROM out_info ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Outgoing Records | Sangguniang Bayan</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .file-item {
    cursor: default;
    user-select: none;        /* standard */
    -webkit-user-select: none; /* Chrome/Safari */
    -moz-user-select: none;    /* Firefox */
    -ms-user-select: none;     /* old Edge */
}
html, body { height:100%; margin:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f4f6f9; display:flex; overflow:hidden; }
.sidebar { width:270px; background:#0b3d91; color:white; display:flex; flex-direction:column; padding-top:20px; }
.sidebar .sidebar-brand { font-family:'Times New Roman', serif; font-weight:bold; text-align:center; margin-bottom:20px; font-size:1.2rem; }
.sidebar .nav-link {
    color: white !important;
}

.sidebar .nav-link.active {
    background-color: white;
    color: #0b3d91 !important;
}

.content-wrapper { flex-grow:1; display:flex; flex-direction:column; padding:20px; overflow:hidden; }

.light-card { background:white; border-radius:12px; padding:20px; flex:1; display:flex; flex-direction:column; min-height:0; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
h6 { font-family:'Times New Roman', serif; font-weight:bold; color:#0b3d91; margin-bottom:15px; text-transform:uppercase; font-size:0.85rem; }
.form-control { font-size:0.85rem; border-radius:6px; }
.btn-action-main { background-color:#0b3d91; color:white; border:none; border-radius:5px; padding:8px 20px; font-weight:bold; }

/* Table */
.table-container { flex:1; overflow-y:auto; margin-top:15px; border-top:1px solid #e0e0e0; }
.table tbody td, .table thead th { padding:6px 8px; font-size:0.8rem; vertical-align:middle; }
.table thead th { font-size:0.8rem; padding:5px 8px; }
.table tbody tr { height:auto; }
.btn-delete { color:#dc3545; border:1px solid #dc3545; background:transparent; font-size:0.75rem; padding:2px 6px; border-radius:4px; text-decoration:none; }
.btn-delete:hover { background:#dc3545; color:white; }
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

        <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">
            Home
        </a>

        <a class="nav-link <?= ($current_page == 'incoming.php') ? 'active' : '' ?>" href="incoming.php">
            Incoming
        </a>

        <a class="nav-link <?= ($current_page == 'outgoing.php') ? 'active' : '' ?>" href="outgoing.php">
            Outgoing
        </a>

    </nav>
</aside>

<main class="content-wrapper">
    <div class="light-card">
        <h6>Outgoing Form</h6>
        
        <?php if(!empty($error_msg)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form method="POST" id="recordForm" class="mb-3">
            <input type="hidden" name="id" id="recordId" value="<?= htmlspecialchars($id) ?>">
            <div class="row g-2">
                <div class="col-md-2"><label>Date Sent</label><input type="date" name="date_received" id="date_received" class="form-control" required value="<?= htmlspecialchars($date_received) ?>"></div>
                <div class="col-md-3"><label>Sent To</label><input type="text" name="sent" id="sent" class="form-control" placeholder="Recipient" required value="<?= htmlspecialchars($sent) ?>"></div>
                <div class="col-md-4"><label>Address</label><input type="text" name="address" id="address" class="form-control" placeholder="Office Location" required value="<?= htmlspecialchars($address) ?>"></div>
                <div class="col-md-1"><label>FN#</label><input type="number" name="fn" id="fn" class="form-control" placeholder="00" value="<?= htmlspecialchars($fn) ?>"></div>
                <div class="col-md-2 d-grid align-items-end"><button type="submit" name="save_record" id="saveBtn" class="btn-action-main"><?= empty($id) ? 'SAVE RECORD' : 'UPDATE' ?></button></div>
                <div class="col-md-2"><input type="text" name="subject" id="subject" class="form-control" placeholder="Subject / Title" required value="<?= htmlspecialchars($subject) ?>"></div>
                <div class="col-md-3"><input type="text" name="subject_link" id="subject_link" class="form-control" placeholder="Click to select file" readonly onclick="openFileModal()"value="<?= htmlspecialchars($subject_link) ?>"></div>
                <div class="col-md-3"><input type="text" name="received_by" id="received_by" class="form-control" placeholder="Received By" value="<?= htmlspecialchars($received_by) ?>"></div>
                <div class="col-md-4"><input type="text" name="remarks" id="remarks" class="form-control" placeholder="Remarks" value="<?= htmlspecialchars($remarks) ?>"></div>
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="m-0">Outgoing Records</h6>
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
                        <th>Sent To</th>
                        <th>Address</th>
                        <th>FN</th>
                        <th>Subject</th>
                        <th>Received By</th>
                        <th>Date</th>
                        <th>Tools</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM out_info ORDER BY id DESC");
                    while($row = mysqli_fetch_assoc($result)){
                       $subj = !empty($row['SUBJECT_LINK']) 
    ? '<a href="scanned_docs/' . htmlspecialchars($row['SUBJECT_LINK']) . '" target="_blank">' . htmlspecialchars($row['SUBJECT']) . '</a>'
    : htmlspecialchars($row['SUBJECT']);

                        echo "<tr>
                                <td>#{$row['id']}</td>
                                <td>{$row['SENT']}</td>
                                <td>{$row['ADDRESS']}</td>
                                <td>{$row['FN']}</td>
                                <td>{$subj}</td>
                                <td>{$row['RECIEVED_BY']}</td>
                                <td>{$row['DATE_RECIEVED']}</td>
                                <td>
                                    <button class='btn btn-sm btn-light border editBtn' 
                                        data-id='{$row['id']}' data-sent='{$row['SENT']}' data-address='{$row['ADDRESS']}' data-fn='{$row['FN']}'
                                        data-subject='".htmlspecialchars($row['SUBJECT'], ENT_QUOTES)."' data-subject_link='".htmlspecialchars($row['SUBJECT_LINK'], ENT_QUOTES)."'
                                        data-received_by='{$row['RECIEVED_BY']}' data-remarks='{$row['REMARKS']}' data-date_received='{$row['DATE_RECIEVED']}'>Edit</button>
                                    <a href='outgoing.php?delete={$row['id']}' class='btn-delete' onclick='return confirm(\"Delete this record?\");'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
// Edit button functionality
document.querySelectorAll(".editBtn").forEach(button => {
    button.addEventListener("click", function(){
        const d = this.dataset;

        document.getElementById("recordId").value = d.id;
        document.getElementById("date_received").value = d.date_received;
        document.getElementById("sent").value = d.sent;
        document.getElementById("address").value = d.address;
        document.getElementById("fn").value = d.fn;
        document.getElementById("subject").value = d.subject;
        document.getElementById("subject_link").value = d.subject_link;
        document.getElementById("received_by").value = d.received_by;
        document.getElementById("remarks").value = d.remarks;

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

</body>
</html>