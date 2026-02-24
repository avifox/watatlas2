<?php
include __DIR__ . '/../../shared/dbConfig.php';
include '../../shared/navbar.php';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$nameFilter = $_GET['name'] ?? '';
$addressFilter = $_GET['address'] ?? '';
$countryFilter = $_GET['country'] ?? '';
$regionFilter = $_GET['region'] ?? '';

$sql = "
    SELECT p.id, p.name, p.description, p.region, p.country, p.location, p.phone, p.picture, t.typename
    FROM places p
    JOIN placetypes t ON p.typeid = t.typeid
    WHERE t.typename = ?
";

$params = [$type];
$types = "s";

// Add filters if provided
if ($nameFilter) {
    $sql .= " AND p.name LIKE ?";
    $params[] = "%$nameFilter%";
    $types .= "s";
}
if ($addressFilter) {
    $sql .= " AND p.address LIKE ?";
    $params[] = "%$addressFilter%";
    $types .= "s";
}
if ($countryFilter) {
    $sql .= " AND p.country LIKE ?";
    $params[] = "%$countryFilter%";
    $types .= "s";
}
if ($regionFilter) {
    $sql .= " AND p.region LIKE ?";
    $params[] = "%$regionFilter%";
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($type); ?> - WatAtlas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container my-5">
  <h2 class="mb-4"><?php echo htmlspecialchars($type); ?></h2>

  <!-- Filter Form -->
  <form method="get" class="mb-4">
    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
    <div class="row g-3">
      <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="Filter by name" value="<?php echo htmlspecialchars($nameFilter); ?>">
      </div>
      <div class="col-md-3">
        <input type="text" name="address" class="form-control" placeholder="Filter by address" value="<?php echo htmlspecialchars($addressFilter); ?>">
      </div>
      <div class="col-md-3">
        <input type="text" name="country" class="form-control" placeholder="Filter by country" value="<?php echo htmlspecialchars($countryFilter); ?>">
      </div>
      <div class="col-md-3">
        <input type="text" name="region" class="form-control" placeholder="Filter by region" value="<?php echo htmlspecialchars($regionFilter); ?>">
      </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Apply Filters</button>
  </form>

  <div class="row">
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <?php $desc = strlen($row["description"]) > 100 ? substr($row["description"], 0, 100) . "..." : $row["description"]; ?>
        <div class="col-md-3 mb-4">
          <a href="features/place.php?id=<?php echo urlencode($row["id"]); ?>" class="text-decoration-none text-dark">
            <div class="card shadow-sm h-100">
              <img src="../../<?php echo htmlspecialchars($row["picture"]); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row["name"]); ?>">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row["name"]); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($desc); ?></p>
                <p class="small text-muted">
                  <i class="fas fa-map-marker-alt mb-2"></i> <?php echo htmlspecialchars($row["region"]); ?><br>
                  <i class="fas fa-flag"></i> <?php echo htmlspecialchars($row["country"]); ?>
                </p>
              </div>
            </div>
          </a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No places found.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
