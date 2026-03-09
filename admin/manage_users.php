<?php
include __DIR__ . "/../shared/dbconfig.php";
include './navbar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['organization']) || strtolower($_SESSION['organization']) !== 'watatlas') {
    header("Location: login.php");
    exit;
}

/* ADD USER */
if (isset($_POST['add'])) {

    $uname = $_POST['username'];
    $email = $_POST['emailaddress'];
    $org   = $_POST['organization'];
    $plainPassword = $_POST['password'];
    $status = $_POST['status'];

    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO managedusers (username,emailaddress,password,organization,status) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss",$uname,$email,$hashedPassword,$org,$status);
    $stmt->execute();
}

/* DELETE */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM managedusers WHERE id=$id");
}

/* UPDATE */
if (isset($_POST['update'])) {

    $id    = (int)$_POST['id'];
    $uname = $_POST['username'];
    $email = $_POST['emailaddress'];
    $org   = $_POST['organization'];
    $status= $_POST['status'];

    if(!empty($_POST['password'])){

        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE managedusers SET username=?,emailaddress=?,password=?,organization=?,status=? WHERE id=?");
        $stmt->bind_param("sssssi",$uname,$email,$hashedPassword,$org,$status,$id);

    }else{

        $stmt = $conn->prepare("UPDATE managedusers SET username=?,emailaddress=?,organization=?,status=? WHERE id=?");
        $stmt->bind_param("ssssi",$uname,$email,$org,$status,$id);
    }

    $stmt->execute();
}

$result = $conn->query("SELECT * FROM managedusers");
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Manage Users</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<h2 class="mb-4">Manage Users</h2>

<!-- ADD USER -->

<form method="post" class="card p-3 mb-4">

<h5>Add New User</h5>

<div class="row mb-2">

<div class="col">
<input type="text" name="username" class="form-control" placeholder="Username" required>
</div>

<div class="col">
<input type="email" name="emailaddress" class="form-control" placeholder="Email" required>
</div>

</div>

<div class="row mb-2">

<div class="col">
<input type="text" name="organization" class="form-control" placeholder="Organization">
</div>

<div class="col">
<input type="password" name="password" class="form-control" placeholder="Password" required>
</div>

</div>

<div class="mb-2">

<select name="status" class="form-select">
<option value="Active">Active</option>
<option value="Inactive">Inactive</option>
</select>

</div>

<button type="submit" name="add" class="btn btn-primary">Add User</button>

</form>

<!-- USERS TABLE -->

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Organization</th>
<th>Status</th>
<th>Actions</th>
</tr>

</thead>

<tbody>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td><?= $row['id'] ?></td>

<td>
<input type="text" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" readonly>
</td>

<td>
<input type="text" class="form-control" value="<?= htmlspecialchars($row['emailaddress']) ?>" readonly>
</td>

<td>
<input type="text" class="form-control" value="<?= htmlspecialchars($row['organization']) ?>" readonly>
</td>

<td>
<input type="text" class="form-control" value="<?= $row['status'] ?>" readonly>
</td>

<td>

<button
class="btn btn-success btn-sm"
data-bs-toggle="modal"
data-bs-target="#editModal"

data-id="<?= $row['id'] ?>"
data-username="<?= htmlspecialchars($row['username']) ?>"
data-email="<?= htmlspecialchars($row['emailaddress']) ?>"
data-org="<?= htmlspecialchars($row['organization']) ?>"
data-status="<?= $row['status'] ?>"

>
Update
</button>

<a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">
Delete
</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>


<!-- UPDATE MODAL -->

<div class="modal fade" id="editModal">

<div class="modal-dialog">

<div class="modal-content">

<form method="post">

<div class="modal-header">

<h5 class="modal-title">Update User</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<input type="hidden" name="id" id="edit_id">

<div class="mb-2">
<label>Username</label>
<input type="text" name="username" id="edit_username" class="form-control" required>
</div>

<div class="mb-2">
<label>Email</label>
<input type="email" name="emailaddress" id="edit_email" class="form-control" required>
</div>

<div class="mb-2">
<label>Organization</label>
<input type="text" name="organization" id="edit_org" class="form-control">
</div>

<div class="mb-2">
<label>New Password (optional)</label>
<input type="password" name="password" class="form-control">
</div>

<div class="mb-2">

<label>Status</label>

<select name="status" id="edit_status" class="form-select">

<option value="Active">Active</option>
<option value="Inactive">Inactive</option>

</select>

</div>

</div>

<div class="modal-footer">

<button type="submit" name="update" class="btn btn-success">
Save Changes
</button>

<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

</div>

</form>

</div>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

var editModal = document.getElementById('editModal')

editModal.addEventListener('show.bs.modal', function (event) {

var button = event.relatedTarget

document.getElementById('edit_id').value = button.getAttribute('data-id')
document.getElementById('edit_username').value = button.getAttribute('data-username')
document.getElementById('edit_email').value = button.getAttribute('data-email')
document.getElementById('edit_org').value = button.getAttribute('data-org')
document.getElementById('edit_status').value = button.getAttribute('data-status')

})

</script>

</body>
</html>