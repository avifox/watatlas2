<?php
// search.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'shared/dbConfig.php';
include 'shared/navbar.php';

// Get search term
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

$results = [];
if (!empty($searchTerm)) {
    // Query places joined with placetypes
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.address, p.country, p.picture, t.typename
        FROM places p
        JOIN placetypes t ON p.typeid = t.typeid
        WHERE p.name LIKE ? 
           OR p.address LIKE ? 
           OR p.country LIKE ? 
           OR t.typename LIKE ?
    ");
    $like = "%$searchTerm%";
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results - WatAtlas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container my-5">
  <h2>Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"</h2>

  <div class="row mt-4">
    <?php if (!empty($results)): ?>
      <?php foreach ($results as $place): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <img src="<?php echo $place['picture']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($place['name']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($place['name']); ?></h5>
              <span class="badge bg-secondary"><?php echo htmlspecialchars($place['typename']); ?></span>
              <p class="card-text mt-2">
                <?php echo htmlspecialchars($place['address']); ?>, <?php echo htmlspecialchars($place['country']); ?>
              </p>
              <a href="features/place.php?id=<?php echo $place['id']; ?>" class="btn btn-outline-primary btn-sm">View Details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No results found.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
