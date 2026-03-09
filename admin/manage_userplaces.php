<?php
include '../shared/dbconfig.php';
include './navbar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Access control: only WatAtlas org
if (!isset($_SESSION['username']) || !isset($_SESSION['organization']) || strtolower($_SESSION['organization']) !== 'watatlas') {
    header("Location: login.php");
    exit;
}

// CREATE
if (isset($_POST['add'])) {
    $managedUserId = $_POST['manageduserid'];
    $placeId       = $_POST['placeid'];
    $status        = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO managedusersplaces (manageduserid, placeid, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $managedUserId, $placeId, $status);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']); // redirect to avoid duplicate form submission
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id            = $_POST['id'];
    $managedUserId = $_POST['manageduserid'];
    $placeId       = $_POST['placeid'];
    $status        = $_POST['status'];

    $stmt = $conn->prepare("UPDATE managedusersplaces SET manageduserid=?, placeid=?, status=? WHERE id=?");
    $stmt->bind_param("iisi", $managedUserId, $placeId, $status, $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM managedusersplaces WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Fetch users and places into arrays
$usersArray = [];
$placesArray = [];

$users  = $conn->query("SELECT id, username FROM managedusers ORDER BY username");
while ($u = $users->fetch_assoc()) $usersArray[] = $u;

$places = $conn->query("SELECT id, name FROM places ORDER BY name");
while ($p = $places->fetch_assoc()) $placesArray[] = $p;

// Fetch all manageduserplaces
$result = $conn->query("
    SELECT mup.id, mup.manageduserid, mup.placeid, mup.status,
           u.username, p.name AS place_name
    FROM managedusersplaces mup
    JOIN managedusers u ON mup.manageduserid = u.id
    JOIN places p ON mup.placeid = p.id
    ORDER BY u.username, p.name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../shared/head.php'; ?>
    <title>Managed User Places</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage User Places</h1>

    <!-- Add Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Add User Place</div>
        <div class="card-body">
            <form method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <select name="manageduserid" class="form-select" required>
                            <option value="">Select User</option>
                            <?php foreach($usersArray as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Place</label>
                        <select name="placeid" class="form-select" required>
                            <option value="">Select Place</option>
                            <?php foreach($placesArray as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" name="add" class="btn btn-success">Add</button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header bg-secondary text-white">User Places List</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Place</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['place_name']) ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this entry?')">Delete</a>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Entry</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">User</label>
                                            <select name="manageduserid" class="form-select" required>
                                                <?php foreach($usersArray as $u): ?>
                                                    <option value="<?= $u['id'] ?>" <?= $u['id']==$row['manageduserid']?'selected':'' ?>>
                                                        <?= htmlspecialchars($u['username']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Place</label>
                                            <select name="placeid" class="form-select" required>
                                                <?php foreach($placesArray as $p): ?>
                                                    <option value="<?= $p['id'] ?>" <?= $p['id']==$row['placeid']?'selected':'' ?>>
                                                        <?= htmlspecialchars($p['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="Active" <?= $row['status']=='Active'?'selected':'' ?>>Active</option>
                                                <option value="Inactive" <?= $row['status']=='Inactive'?'selected':'' ?>>Inactive</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>