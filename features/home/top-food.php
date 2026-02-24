<?php include __DIR__ . '/../../shared/dbConfig.php'; ?>
<div class="container my-5">
  <h2 class="mb-4">Top Attractions</h2>
  <div class="row">
    <?php
    $sql = "SELECT p.id, p.name, p.description, p.region, p.country, p.location, p.phone, p.picture, t.typename
            FROM places p
            JOIN placetypes t ON p.typeid = t.typeid
            WHERE t.typename = 'Food'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        // Trim description to 100 characters
        $desc = strlen($row["description"]) > 100 
                  ? substr($row["description"], 0, 100) . "..." 
                  : $row["description"];

        echo '
        <div class="col-md-3 mb-4">
          <a href="features/place.php?id='.$row["id"].'" class="text-decoration-none text-dark">
            <div class="card shadow-sm h-100">
              <img src="'.$row["picture"].'" class="card-img-top" alt="'.$row["name"].'">
              <div class="card-body">
                <h5 class="card-title">'.$row["name"].'</h5>
                <p class="card-text">'.$desc.'</p>
                <p class="small text-muted">
                  <i class="fas fa-map-marker-alt mb-2"></i> '.$row["region"].'<br>
                  <i class="fas fa-flag"></i> '.$row["country"].'
                </p>
              </div>
            </div>
          </a>
        </div>';
      }
    } else {
      echo "<p>No places found.</p>";
    }

    $conn->close();
    ?>
  </div>
</div>
