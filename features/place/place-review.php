<?php
include '../../shared/navbar.php';
include '../../shared/dbConfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$placeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* GET PLACE */
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
$place = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* COORDS */
$lat = 0;
$lng = 0;
if (!empty($place['location']) && strpos($place['location'], '/') !== false) {
    [$lat, $lng] = explode('/', $place['location']);
}

/* GALLERY */
$pictures = [];
$picStmt = $conn->prepare("SELECT pictureUrl FROM placepictures WHERE placeid = ?");
$picStmt->bind_param("i", $placeId);
$picStmt->execute();
$res = $picStmt->get_result();
while ($row = $res->fetch_assoc()) {
    $pictures[] = $row['pictureUrl'];
}
$picStmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($place['name']); ?></title>

    <?php include '../../shared/head.php'; ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<body>

    <div class="container py-4">

        <!-- HERO -->
        <div class="position-relative mb-4 rounded overflow-hidden" style="height:300px;">
            <img src="<?php echo '../' . htmlspecialchars($place['picture']); ?>" class="w-100 h-100 object-fit-cover">

            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white"
                style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                <h3 class="mb-1"><?php echo htmlspecialchars($place['name']); ?></h3>
                <span class="badge bg-light text-dark">
                    <?php echo htmlspecialchars($place['typename']); ?>
                </span>
            </div>
        </div>

        <div class="row g-4">

            <!-- LEFT -->
            <div class="col-lg-8">

                <!-- DESCRIPTION (no border) -->
                <div class="mb-4">
                    <p class="mb-0">
                        <?php echo nl2br(htmlspecialchars($place['description'])); ?>
                    </p>
                </div>

                <!-- GALLERY -->
                <?php if (!empty($pictures)): ?>
                    <div class="card p-4 mb-4">
                        <div class="row g-3">
                            <?php foreach ($pictures as $index => $pic): ?>
                                <div class="col-md-4">
                                    <img src="<?php echo './../' . htmlspecialchars($pic); ?>" class="w-100 rounded gallery-img"
                                        data-index="<?php echo $index; ?>"
                                        style="height:200px; object-fit:cover; cursor:pointer;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- RIGHT -->
            <div class="col-lg-4">

                <!-- DETAILS (no border) -->
                <div class="mb-4">

                    <p>
                        <i class="bi bi-geo-alt"></i>
                        <?php echo htmlspecialchars($place['address']); ?>,
                        <?php echo htmlspecialchars($place['country']); ?>
                    </p>

                    <?php if (!empty($place['phone'])): ?>
                        <p>
                            <i class="bi bi-telephone"></i>
                            <?php echo htmlspecialchars($place['phone']); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($lat && $lng): ?>
                        <a target="_blank"
                            href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $lat . ',' . $lng; ?>"
                            class="d-inline-flex align-items-center gap-2 mt-2 px-3 py-2 rounded text-decoration-none bg-light border text-primary">
                            <i class="bi bi-signpost-2"></i>
                            Get Directions
                        </a>
                    <?php endif; ?>

                    <div id="map" style="height:250px;" class="mt-3"></div>
                </div>

            </div>

        </div>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark position-relative">

                <button class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3"
                    data-bs-dismiss="modal"></button>

                <img id="modalImage" class="w-100">

                <button id="prevBtn" class="btn btn-light position-absolute top-50 start-0 translate-middle-y ms-2">
                    ‹
                </button>

                <button id="nextBtn" class="btn btn-light position-absolute top-50 end-0 translate-middle-y me-2">
                    ›
                </button>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /* MAP */
        var map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>]).addTo(map);

        /* GALLERY */
        const images = <?php echo json_encode(array_map(fn($p) => './../' . $p, $pictures)); ?>;
        let currentIndex = 0;

        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        const modalImg = document.getElementById('modalImage');

        document.querySelectorAll('.gallery-img').forEach(img => {
            img.addEventListener('click', function () {
                currentIndex = parseInt(this.dataset.index);
                updateImage();
                modal.show();
            });
        });

        function updateImage() {
            modalImg.src = images[currentIndex];
        }

        document.getElementById('nextBtn').onclick = () => {
            currentIndex = (currentIndex + 1) % images.length;
            updateImage();
        };

        document.getElementById('prevBtn').onclick = () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateImage();
        };

        /* KEYBOARD */
        document.addEventListener('keydown', e => {
            if (!document.getElementById('imageModal').classList.contains('show')) return;

            if (e.key === "ArrowRight") {
                currentIndex = (currentIndex + 1) % images.length;
                updateImage();
            }
            if (e.key === "ArrowLeft") {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                updateImage();
            }
        });
    </script>

    <?php include '../../shared/footer.php'; ?>

</body>

</html>