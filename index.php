<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>WatAtlas</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Navbar -->
<?php include 'shared/navbar.php'; ?>
<!-- Hero Section -->
<div class="hero">
  <div class="text-center hero-content">
    <h1 class="display-4">Where Do You Want To Go</h1>
<form class="d-flex mt-3 justify-content-center" action="search.php" method="get">
  <input class="form-control me-2 w-50" type="search" name="q" placeholder="Enter your destination?" aria-label="Search">
  <button class="btn btn-light" type="submit">Search</button>
</form>
</div>
</div>
<!-- Content Section -->
<?php include 'features/attractions.php'; ?>
<!-- Footer -->
<footer>
  <p>&copy; <?php echo date("Y"); ?> WatAtlas.com.</p>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
