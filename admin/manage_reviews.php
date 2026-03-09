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

// DELETE flagged review
if (isset($_GET['delete'])) {
    $reviewId = intval($_GET['delete']);

    // Delete review
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id=?");
    $stmt->bind_param("i", $reviewId);
    $stmt->execute();

    // Also delete related flags
    $stmt2 = $conn->prepare("DELETE FROM user_review_flag WHERE reviewid=?");
    $stmt2->bind_param("i", $reviewId);
    $stmt2->execute();

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Fetch reviews with place name and flagged count
$result = $conn->query("
    SELECT r.id, r.placeid, r.rating, r.reviewtext, r.createdat, r.flagged,
           p.name AS place_name
    FROM reviews r
    LEFT JOIN places p ON r.placeid = p.id
    ORDER BY r.flagged DESC, r.createdat DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../shared/head.php'; ?>
    <title>Manage Reviews</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage Reviews</h1>

    <div class="card">
        <div class="card-header bg-secondary text-white">
            Reviews List
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Place</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Created</th>
                        <th>Flagged Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['place_name']) ?></td>
                        <td><?= $row['rating'] ?></td>
                        <td><?= htmlspecialchars($row['reviewtext']) ?></td>
                        <td><?= $row['createdat'] ?></td>
                        <td><?= $row['flagged'] ?></td>
                        <td>
                            <?php if($row['flagged'] > 0): ?>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this flagged review?')">Delete</a>
                            <?php else: ?>
                                <span class="text-muted">No flags</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>