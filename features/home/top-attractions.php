<?php
// Include database config
include __DIR__ . '/../../shared/dbConfig.php';

// Optional: create a helper function for image URLs
function imageUrl($path) {
    return '/watatlas/' . ltrim($path, '/'); // ensures no double slashes
}
?>

<div class="container my-5">
    <h2 class="mb-4">Top Attractions</h2>
    <div class="row">
        <?php
        // Query top attractions
        $sql = "SELECT p.id, p.name, p.description, p.region, p.country, p.picture, t.typename
                FROM places p
                JOIN placetypes t ON p.typeid = t.typeid
                WHERE t.typename = 'Place of Interests'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Trim description to 100 characters safely
                $desc = mb_strlen($row['description']) > 100 
                          ? mb_substr($row['description'], 0, 100) . '...' 
                          : $row['description'];

                // Safe escaping for HTML output
                $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                $region = htmlspecialchars($row['region'], ENT_QUOTES, 'UTF-8');
                $country = htmlspecialchars($row['picture'], ENT_QUOTES, 'UTF-8');

                echo '
                <div class="col-md-3 mb-4">
                  <a href="/watatlas/features/place/place-review.php?id='.$row["id"].'" class="text-decoration-none text-dark">
                    <div class="card shadow-sm h-100">
                      <img src="'.imageUrl($row["picture"]).'" class="card-img-top" alt="'.$name.'">
                      <div class="card-body">
                        <h5 class="card-title">'.$name.'</h5>
                        <p class="card-text">'.$desc.'</p>
                        <p class="small text-muted">
                          <i class="fas fa-map-marker-alt mb-2"></i> '.$region.'<br>
                          <i class="fas fa-flag"></i> '.$country.'
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