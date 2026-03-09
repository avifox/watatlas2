<?php
include '../shared/dbconfig.php';
include './navbar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Access control
if (!isset($_SESSION['username']) || !isset($_SESSION['organization']) || strtolower($_SESSION['organization']) !== 'watatlas') {
    header("Location: login.php");
    exit;
}

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

// CREATE place
if (isset($_POST['create'])) {
    $pictureUrls = [];
    if (!empty($_FILES['pictures']['name'][0])) {
        $pictureUrls = uploadPictures($_POST['name'], $_FILES['pictures']);
    }
    $mainPicture = $pictureUrls[0] ?? null;

    $stmt = $conn->prepare("INSERT INTO places 
        (name, description, address, phone, email, website, typeId, createdAt, updatedAt, picture, country, region, location) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssissss",
        $_POST['name'], $_POST['description'], $_POST['address'], $_POST['phone'],
        $_POST['email'], $_POST['website'], $_POST['typeId'], $mainPicture,
        $_POST['country'], $_POST['region'], $_POST['location']
    );
    $stmt->execute();
    $placeId = $stmt->insert_id;

    if ($pictureUrls) {
        $stmt2 = $conn->prepare("INSERT INTO placepictures (placeId, pictureUrl) VALUES (?, ?)");
        foreach ($pictureUrls as $url) {
            $stmt2->bind_param("is", $placeId, $url);
            $stmt2->execute();
        }
    }
    echo "<div class='alert alert-success'>Place created successfully!</div>";
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

// DELETE place
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Delete main picture folder
    $stmt = $conn->prepare("SELECT picture FROM places WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $place = $res->fetch_assoc();
    if ($place && !empty($place['picture'])) {
        $folder = dirname($place['picture']);
        if (is_dir($folder)) {
            $files = glob($folder . "/*");
            foreach ($files as $file) if (is_file($file)) unlink($file);
            rmdir($folder);
        }
    }
    $stmt = $conn->prepare("DELETE FROM placepictures WHERE placeId=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt = $conn->prepare("DELETE FROM places WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Fetch types & places
$types = $conn->query("SELECT * FROM placetypes");
$places = $conn->query("SELECT p.*, pt.typeName FROM places p LEFT JOIN placetypes pt ON p.typeId = pt.typeId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../shared/head.php'; ?>
    <title>WatAtlas Admin - Places</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage Places</h1>

    <!-- Create Place -->
    <div class="card mb-5">
        <div class="card-header bg-primary text-white">Create New Place</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="typeId" class="form-select">
                            <?php $types->data_seek(0); while($t = $types->fetch_assoc()){ ?>
                                <option value="<?= $t['typeId'] ?>"><?= $t['typeName'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description"></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="address">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Website</label>
                        <input type="text" class="form-control" name="website">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country</label>
                        <input type="text" class="form-control" name="country">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Region</label>
                        <input type="text" class="form-control" name="region">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location (coordinates)</label>
                    <input type="text" class="form-control" name="location">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pictures</label>
                    <input type="file" class="form-control" name="pictures[]" multiple>
                </div>
                <button type="submit" name="create" class="btn btn-success">Create Place</button>
            </form>
        </div>
    </div>

    <!-- Places Table -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Places List</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Picture</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($p = $places->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= $p['name'] ?></td>
                        <td><?= $p['picture'] ?></td>
                        <td>
                            <?php if($p['picture']) { ?>
                                <img src="watatlas/uploads/Antartica_1773077124/this-was-sent-to-me-from.jpg" width="50" class="img-thumbnail">
                            <?php } ?>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $p['id'] ?>">Edit</button>
                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this place?')">Delete</a>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $p['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Place: <?= $p['name'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name" value="<?= $p['name'] ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Type</label>
                                                <select name="typeId" class="form-select">
                                                    <?php $types->data_seek(0); while($t = $types->fetch_assoc()){ ?>
                                                        <option value="<?= $t['typeId'] ?>" <?= $t['typeId']==$p['typeId']?'selected':'' ?>><?= $t['typeName'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description"><?= $p['description'] ?></textarea>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Address</label>
                                                <input type="text" class="form-control" name="address" value="<?= $p['address'] ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control" name="phone" value="<?= $p['phone'] ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" value="<?= $p['email'] ?>">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Website</label>
                                                <input type="text" class="form-control" name="website" value="<?= $p['website'] ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Country</label>
                                                <input type="text" class="form-control" name="country" value="<?= $p['country'] ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Region</label>
                                                <input type="text" class="form-control" name="region" value="<?= $p['region'] ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Location (coordinates)</label>
                                            <input type="text" class="form-control" name="location" value="<?= $p['location'] ?>">
                                        </div>

                                        <!-- Existing Pictures -->
                                        <div class="mb-3">
                                            <label class="form-label">Existing Pictures</label>
                                            <div class="row">
                                            <?php
                                            $pics = $conn->query("SELECT * FROM placepictures WHERE placeId=".$p['id']);
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
                        </div>
                    </div>

                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>