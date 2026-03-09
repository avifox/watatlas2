<?php
session_start();

// ✅ Access control: only allow if session is active and organization is watatlas
if (!isset($_SESSION['username']) || !isset($_SESSION['organization']) || strtolower($_SESSION['organization']) !== 'watatlas') {
    header("Location: login.php");
    exit;
}
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
          <a class="nav-link" href="manage_users.php">Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="manage_place.php">Places</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="manage_userplaces.php">User-Places</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="manage_reviews.php">Flagged-Reviews</a>
        </li>
      </ul>

      <span class="navbar-text me-3">
        Hi, <?= htmlspecialchars($_SESSION['username']) ?>
      </span>
      <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>
