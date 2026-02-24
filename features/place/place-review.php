<?php
include '../shared/navbar.php';
include '../shared/dbConfig.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$placeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* =========================
   GET PLACE DETAILS
========================= */
$stmt = $conn->prepare("
    SELECT p.id, p.name, p.description, p.address, 
           p.country, p.location, p.phone, p.picture, 
           t.typename
    FROM places p
    JOIN placetypes t ON p.typeid = t.typeid
    WHERE p.id = ?
");
$stmt->bind_param("i", $placeId);
$stmt->execute();
$result = $stmt->get_result();
$place = $result->fetch_assoc();
$stmt->close();

/* Extract coordinates */
$lat = 0;
$lng = 0;
if (!empty($place['location'])) {
    $coords = explode('/', $place['location']);
    $lat = floatval($coords[0]);
    $lng = floatval($coords[1]);
}

/* =========================
   GALLERY
========================= */
$pictures = [];
$picStmt = $conn->prepare("SELECT pictureUrl FROM placepictures WHERE placeid = ?");
$picStmt->bind_param("i", $placeId);
$picStmt->execute();
$picResult = $picStmt->get_result();
while ($row = $picResult->fetch_assoc()) {
    $pictures[] = $row['pictureUrl'];
}
$picStmt->close();

/* =========================
   REVIEWS
========================= */
$reviews = [];
$revStmt = $conn->prepare("
    SELECT r.id, r.userid, r.rating, r.reviewtext, r.createdat,
           u.firstname, u.lastname
    FROM reviews r
    JOIN users u ON r.userid = u.userid
    WHERE r.placeid = ?
    ORDER BY r.createdat DESC
");
$revStmt->bind_param("i", $placeId);
$revStmt->execute();
$revResult = $revStmt->get_result();
while ($row = $revResult->fetch_assoc()) {
    $reviews[] = $row;
}
$revStmt->close();

/* =========================
   CHECK IF USER REVIEWED THIS YEAR
========================= */
$alreadyReviewedThisYear = false;

if (isset($_SESSION['userid'])) {
    $currentYear = date("Y");
    $checkStmt = $conn->prepare("
        SELECT id FROM reviews
        WHERE placeid = ?
        AND userid = ?
        AND YEAR(createdat) = ?
    ");
    $checkStmt->bind_param("iii", $placeId, $_SESSION['userid'], $currentYear);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        $alreadyReviewedThisYear = true;
    }
    $checkStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($place['name']); ?> - Details</title>

<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/place.css">
</head>

<body>
<div class="container py-4">

<!-- HERO -->
<div class="place-hero mb-4">
    <img src="<?php echo '../'.htmlspecialchars($place['picture']); ?>">
    <div class="hero-overlay">
        <h2><?php echo htmlspecialchars($place['name']); ?></h2>
        <span class="badge bg-light text-dark">
            <?php echo htmlspecialchars($place['typename']); ?>
        </span>
    </div>
</div>

<div class="row g-4">

<!-- LEFT -->
<div class="col-lg-8">

<div class="card info-card p-4 mb-4">
<p><?php echo nl2br(htmlspecialchars($place['description'])); ?></p>
</div>

<?php if (!empty($pictures)): ?>
<div class="card info-card p-4 mb-4">
<h5 class="section-title">Gallery</h5>
<div class="row gallery g-3">
<?php foreach ($pictures as $pic): ?>
<div class="col-md-4">
<img src="<?php echo htmlspecialchars($pic); ?>" 
     class="w-100 gallery-img"
     data-img="<?php echo htmlspecialchars($pic); ?>">
</div>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>

<div class="card info-card p-4">
<h5 class="section-title">Reviews</h5>

<?php if (!empty($reviews)): ?>
<?php foreach ($reviews as $rev): ?>
<div class="review-card p-3 mb-3 bg-white">

<div class="d-flex justify-content-between align-items-start">
<div>
<strong><?php echo htmlspecialchars($rev['firstname'].' '.$rev['lastname']); ?></strong>
<span class="badge bg-warning text-dark ms-2"><?php echo $rev['rating']; ?>/5</span>
</div>

<?php if (isset($_SESSION['userid']) && $_SESSION['userid'] == $rev['userid']): ?>
<form method="post" action="delete_review.php"
onsubmit="return confirm('Delete your review?');">
<input type="hidden" name="reviewid" value="<?php echo $rev['id']; ?>">
<input type="hidden" name="placeid" value="<?php echo $placeId; ?>">
<button class="btn btn-sm btn-outline-danger">
<i class="bi bi-trash"></i>
</button>
</form>
<?php endif; ?>

</div>

<p class="mt-2"><?php echo nl2br(htmlspecialchars($rev['reviewtext'])); ?></p>
<div class="text-muted small"><?php echo $rev['createdat']; ?></div>

</div>
<?php endforeach; ?>
<?php else: ?>
<p class="text-muted">No reviews yet.</p>
<?php endif; ?>

<?php if (isset($_SESSION['userid'])): ?>
<hr>

<?php if ($alreadyReviewedThisYear): ?>
<div class="alert alert-warning">
You have already reviewed this place in <?php echo date("Y"); ?>.
You can post another review next year.
</div>
<?php else: ?>

<h6 class="mt-3">Leave a Review</h6>
<form method="post" action="submit_review.php">
<input type="hidden" name="placeid" value="<?php echo $placeId; ?>">
<input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">

<div class="mb-3">
<label class="form-label">Rating</label>
<div class="star-rating">
<input type="hidden" name="rating" id="rating" required>
<?php for ($i=1;$i<=5;$i++): ?>
<span class="star" data-value="<?php echo $i; ?>">★</span>
<?php endfor; ?>
</div>
</div>

<textarea name="reviewtext" class="form-control mb-3" required></textarea>
<button class="btn btn-success">Submit Review</button>
</form>

<?php endif; ?>
<?php else: ?>
<hr>
<p class="text-muted">Sign in to leave a review.</p>
<?php endif; ?>

</div>
</div>

<!-- RIGHT -->
<div class="col-lg-4">
<div class="card info-card p-4 mb-4">
<h6 class="section-title">Details</h6>
<p><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($place['address']); ?>, <?php echo htmlspecialchars($place['country']); ?></p>
<p><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($place['phone']); ?></p>
<div id="map"></div>
</div>
</div>

</div>
</div>

<!-- IMAGE MODAL -->
<div class="modal fade" id="imageModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <button type="button" class="btn-close btn-close-white ms-auto m-2" data-bs-dismiss="modal"></button>
      <img id="modalImage" class="w-100 rounded">
    </div>
  </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
var map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>]).addTo(map);

/* Star Rating */
const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('rating');
stars.forEach((star, index) => {
    star.addEventListener('click', () => {
        ratingInput.value = index + 1;
        stars.forEach(s => s.classList.remove('selected'));
        for (let i = 0; i <= index; i++) {
            stars[i].classList.add('selected');
        }
    });
});

/* Gallery Popup */
const galleryImages = document.querySelectorAll('.gallery-img');
const modalImage = document.getElementById('modalImage');
const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));

galleryImages.forEach(img => {
    img.addEventListener('click', function() {
        modalImage.src = this.getAttribute('data-img');
        imageModal.show();
    });
});
</script>

</body>
</html>