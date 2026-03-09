<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$config = include __DIR__ . '\google-config.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/watatlas/index.php">WatAtlas</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <!--<li class="nav-item"><a class="nav-link" href="features/home/top-food.php">Food</a></li>-->
      </ul>
      <div class="d-flex align-items-center ms-3 text-white">
        <?php if (isset($_SESSION['username'])): ?>
          <span class="me-3">
            Hi, <?php
              $parts = explode(' ', $_SESSION['username']);
              echo htmlspecialchars($parts[0]);
            ?>
          </span>
        <a href="/watatlas/shared/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        <?php else: ?>
          <!-- Google Sign-In placeholders (keep the divs, script is loaded in index.php head) -->
          <div id="g_id_onload"
                data-client_id="<?php echo htmlspecialchars($config['google_client_id']); ?>"
               data-login_uri="<?php echo htmlspecialchars($config['google_login_uri']); ?>"
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