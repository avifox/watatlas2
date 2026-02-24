<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WatAtlas</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Bootstrap CSS (once) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Identity Services (once) -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Your custom CSSs -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/css/categories.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
</head>

<body>
    <!-- Navbar -->
    <?php include 'shared/navbar.php'; ?>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-content text-center">
                <h1 class="display-5 fw-bold text-white mb-4">Where Do You Want To Go</h1>
                <form class="d-flex justify-content-center" action="search.php" method="get">
                    <input class="form-control me-2" style="max-width: 280px;" type="search" name="q"
                        placeholder="Enter your destination?" aria-label="Search">
                    <button class="btn btn-light px-4" type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Content Section -->
    <?php include 'features/categories.php'; ?>
    <?php include 'features/top-attractions.php'; ?>
    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> WatAtlas.com.</p>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>