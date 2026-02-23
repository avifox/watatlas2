<?php include 'shared/dbConfig.php'; ?>

<div class="container my-5">
  <h2 class="mb-4">Looking For Food</h2>
  <div class="row">
    <?php
    $sql = "SELECT p.id, p.name, p.description, p.address, p.country, p.location, p.phone, p.picture, t.typename
            FROM places p
            JOIN placetypes t ON p.typeid = t.typeid
            WHERE t.typename = 'Food'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        echo '
        <div class="col-md-3 mb-4">
          <a href="features/place.php?id='.$row["id"].'" class="text-decoration-none text-dark">
            <div class="card shadow-sm h-100">
              <img src="'.$row["picture"].'" class="card-img-top" alt="'.$row["name"].'">
              <div class="card-body">
                <h5 class="card-title">'.$row["name"].'</h5>
                <p class="card-text">'.$row["description"].'</p>
                <p class="small text-muted">'.$row["address"].', '.$row["country"].'</p>
                <p class="small">Phone: '.$row["phone"].'</p>
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
