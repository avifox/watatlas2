<?php
session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#003366;">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">WatAtlas Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="manage_my_place.php">Places</a>
        </li>
      </ul>

      <span class="navbar-text me-3">
        Hi, <?= htmlspecialchars($_SESSION['username']) ?>
      </span>
      <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>
