<head>
    <?php include './shared/head.php'; ?>
    <title>WatAtlas</title>
</head>
<body>
    <!-- Navbar -->
    <?php include './shared/navbar.php'; ?>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-content text-center">
                <h1 class="display-5 fw-bold text-white mb-4">Where Do You Want To Go</h1>
                <form class="d-flex justify-content-center" action="search.php" method="get">
                    <input class="form-control me-2" style="max-width: 380px;" type="search" name="q"
                        placeholder="Enter your destination?" aria-label="Search">
                    <button class="btn btn-light px-4" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <?php include 'features/home/categories.php'; ?>
    <?php include 'features/home/top-attractions.php'; ?>

    <!-- Footer -->
    <?php include './shared/footer.php'; ?>

    <!-- Scripts (deferred for performance) -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</body>