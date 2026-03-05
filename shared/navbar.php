<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <!-- Use a path relative to index.php (root) -->
    <a class="navbar-brand fw-bold" href="/watatlas/index.php">WatAtlas</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <!-- Example nav items -->
        <!-- <li class="nav-item"><a class="nav-link" href="features/top-attractions.php">Attractions</a></li>
        <li class="nav-item"><a class="nav-link" href="features/top-food.php">Food</a></li> -->
      </ul>

      <div class="d-flex align-items-center ms-3 greet">
        <?php if (isset($_SESSION['username'])): ?>
          <span class="me-3">
            Hi, <?php
              $parts = explode(' ', $_SESSION['username']);
              echo htmlspecialchars($parts[0]);
            ?>
          </span>
          <a href="./shared/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        <?php else: ?>
          <!-- Google Sign-In placeholders (keep the divs, script is loaded in index.php head) -->
          <div id="g_id_onload"
               data-client_id="714262247037-nqtump2fkm13j9il4on9p4q3sj07qabs.apps.googleusercontent.com"
               data-login_uri="http://localhost:8080/watatlas/shared/login_google.php"
               data-auto_prompt="false"></div>
          <div class="g_id_signin"
               data-type="standard"
               data-shape="rectangular"
               data-theme="outline"
               data-text="signin_with"
               data-size="medium"></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>