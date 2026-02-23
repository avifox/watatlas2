<?php
include '../shared/dbConfig.php'; 

$placeid    = intval($_POST['placeid']);
$userid     = $_POST['userid'];
$rating     = intval($_POST['rating']);
$reviewtext = $_POST['reviewtext'];
$createdat  = date('Y-m-d H:i:s');

$sql = "INSERT INTO reviews (placeid, userid, rating, reviewtext, createdat)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isiss", $placeid, $userid, $rating, $reviewtext, $createdat);

if ($stmt->execute()) {
    header("Location: place.php?id=" . $placeid);
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
