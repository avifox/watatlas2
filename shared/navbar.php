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

    </div>
  </div>
</nav>