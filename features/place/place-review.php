<?php
include '../../shared/navbar.php';
include '../../shared/dbConfig.php';

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

if (!empty($place['location']) && strpos($place['location'], '/') !== false) {
    $coords = explode('/', $place['location']);
    if (count($coords) === 2) {
        $lat = floatval($coords[0]);
        $lng = floatval($coords[1]);
    }
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
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;

$revStmt = $conn->prepare("
    SELECT r.id, r.userid, r.rating, r.reviewtext, r.flagged, r.createdat,
           u.firstname, u.lastname,
           IF(urf.userid IS NULL, 0, 1) AS already_flagged
    FROM reviews r
    JOIN users u ON r.userid = u.userid
    LEFT JOIN user_review_flag urf 
           ON urf.reviewid = r.id 
           AND urf.userid = ?
    WHERE r.placeid = ?
    ORDER BY r.createdat DESC
");

$revStmt->bind_param("ii", $userid, $placeId);
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
    <title><?php echo htmlspecialchars($place['name']); ?></title>
    <?php include '../../shared/head.php'; ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="../../assets/css/place.css">
</head>

<body>
    <div class="container py-4">

        <!-- HERO -->
        <div class="place-hero mb-4">
            <img src="<?php echo '../' . htmlspecialchars($place['picture']); ?>">
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
    <p class="text-justify">
        <?php echo nl2br(htmlspecialchars($place['description'])); ?>
    </p>
</div>
                <?php if (!empty($pictures)): ?>
                    <div class="card info-card p-4 mb-4">
                        <h5 class="section-title">Gallery</h5>
                        <div class="row gallery g-3">
                            <?php foreach ($pictures as $pic): ?>
                                <div class="col-md-4">
                                    <img src="<?php echo './../' . htmlspecialchars($pic); ?>" class="w-100 gallery-img"
                                        data-img="./../<?php echo htmlspecialchars($pic); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>


            </div>

            <!-- RIGHT -->
            <div class="col-lg-4">
                <div class="card info-card p-4 mb-4">
                    <h6 class="section-title">Details</h6>
                    <p><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($place['address']); ?>,
                        <?php echo htmlspecialchars($place['country']); ?>
                    </p>
                    <?php if (!empty($place['phone'])): ?>
                        <p><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($place['phone']); ?></p>
                    <?php endif; ?>

<?php if ($lat != 0 && $lng != 0): ?>
    <a target="_blank"
       href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $lat . ',' . $lng; ?>"
       class="d-inline-flex align-items-center gap-2 mt-2 px-3 py-2 rounded text-decoration-none bg-light border text-primary">
        <i class="bi bi-signpost-2"></i>
        Get Directions
    </a>
<?php endif; ?>
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
            img.addEventListener('click', function () {
                modalImage.src = this.getAttribute('data-img');
                imageModal.show();
            });
        });
    </script>
    <!-- Footer -->
    <?php include '../../shared/footer.php'; ?>
</body>

</html>