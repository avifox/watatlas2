<?php
include '../shared/dbconfig.php';
include './navbar-manageduser.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Access control: must be logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Helper function to upload pictures
function uploadPictures($placeName, $files) {
    $uploaded = [];
    $timestamp = time();
    $folderName = preg_replace('/[^a-zA-Z0-9]/', '_', $placeName) . "_" . $timestamp;
    $dir = "/watatlas/uploads/$folderName";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    foreach ($files['name'] as $key => $name) {
        $tmpName = $files['tmp_name'][$key];
        $targetFile = "$dir/$name";
        if (move_uploaded_file($tmpName, $targetFile)) $uploaded[] = $targetFile;
    }
    return $uploaded;
}

// DELETE individual picture
if (isset($_GET['deletepic'])) {
    $picId = intval($_GET['deletepic']);
    $stmt = $conn->prepare("SELECT pictureUrl, placeId FROM placepictures WHERE id=?");
    $stmt->bind_param("i", $picId);
    $stmt->execute();
    $res = $stmt->get_result();
    $pic = $res->fetch_assoc();
    if ($pic) {
        if (file_exists($pic['pictureUrl'])) unlink($pic['pictureUrl']);
        $stmt = $conn->prepare("DELETE FROM placepictures WHERE id=?");
        $stmt->bind_param("i", $picId);
        $stmt->execute();
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// UPDATE place
if (isset($_POST['update'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("UPDATE places SET
        name=?, description=?, address=?, phone=?, email=?, website=?, typeId=?, updatedAt=NOW(), country=?, region=?, location=?
        WHERE id=?");
    $stmt->bind_param(
        "ssssssisssi",
        $_POST['name'], $_POST['description'], $_POST['address'], $_POST['phone'],
        $_POST['email'], $_POST['website'], $_POST['typeId'], $_POST['country'],
        $_POST['region'], $_POST['location'], $id
    );
    $stmt->execute();

    // Handle new picture uploads
    if (!empty($_FILES['pictures']['name'][0])) {
        $pictureUrls = uploadPictures($_POST['name'], $_FILES['pictures']);
        $stmt2 = $conn->prepare("INSERT INTO placepictures (placeId, pictureUrl) VALUES (?, ?)");
        foreach ($pictureUrls as $url) {
            $stmt2->bind_param("is", $id, $url);
            $stmt2->execute();
        }
        // Update main picture if none exists
        $checkMain = $conn->query("SELECT picture FROM places WHERE id=$id")->fetch_assoc();
        if (empty($checkMain['picture'])) {
            $conn->query("UPDATE places SET picture='$pictureUrls[0]' WHERE id=$id");
        }
    }
    echo "<div class='alert alert-success'>Place updated successfully!</div>";
}

// Fetch user's assigned place
$stmt = $conn->prepare("SELECT p.*, pt.typeName 
                        FROM managedusersplaces mup
                        JOIN places p ON mup.placeId = p.id
                        LEFT JOIN placetypes pt ON p.typeId = pt.typeId
                        WHERE mup.manageduserid=? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$placeResult = $stmt->get_result();
$place = $placeResult->fetch_assoc();

// Fetch types for dropdown
$types = $conn->query("SELECT * FROM placetypes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../shared/head.php'; ?>
    <title>Manage My Place</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage My Place</h1>

    <?php if ($place): ?>
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">Edit Place: <?= htmlspecialchars($place['name']) ?></div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input  type="hidden" name="id" value="<?= $place['id'] ?>">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input readonly type="text" class="form-control" name="name" value="<?= htmlspecialchars($place['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="typeId" class="form-select">
                                <?php $types->data_seek(0); while($t = $types->fetch_assoc()){ ?>
                                    <option value="<?= $t['typeId'] ?>" <?= $t['typeId']==$place['typeId']?'selected':'' ?>><?= $t['typeName'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description"><?= htmlspecialchars($place['description']) ?></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($place['address']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($place['phone']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($place['email']) ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" name="website" value="<?= htmlspecialchars($place['website']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" value="<?= htmlspecialchars($place['country']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Region</label>
                            <input type="text" class="form-control" name="region" value="<?= htmlspecialchars($place['region']) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location (coordinates)</label>
                        <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($place['location']) ?>">
                    </div>

                    <!-- Existing Pictures -->
                    <div class="mb-3">
                        <label class="form-label">Existing Pictures</label>
                        <div class="row">
                        <?php
                        $pics = $conn->query("SELECT * FROM placepictures WHERE placeId=".$place['id']);
                        while($pic = $pics->fetch_assoc()){ ?>
                            <div class="col-md-3 text-center mb-3">
                                <img src="<?= $pic['pictureUrl'] ?>" class="img-thumbnail mb-2" style="height:120px;object-fit:cover;">
                                <br>
                                <a href="?deletepic=<?= $pic['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this picture?')">Remove</a>
                            </div>
                        <?php } ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Add New Pictures</label>
                        <input type="file" class="form-control" name="pictures[]" multiple>
                    </div>

                    <button type="submit" name="update" class="btn btn-primary">Update Place</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No place assigned to your account.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>