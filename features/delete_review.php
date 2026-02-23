<?php
include '../shared/dbConfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userid'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $reviewId = isset($_POST['reviewid']) ? intval($_POST['reviewid']) : 0;
    $placeId  = isset($_POST['placeid']) ? intval($_POST['placeid']) : 0;
    $userId   = $_SESSION['userid'];

    if ($reviewId > 0) {

        // Delete ONLY if review belongs to logged in user
        $stmt = $conn->prepare("
            DELETE FROM reviews 
            WHERE id = ? 
            AND userid = ?
        ");
        $stmt->bind_param("ii", $reviewId, $userId);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to the place page
    header("Location: place.php?id=" . $placeId);
    exit();
}

// If accessed directly, redirect safely
header("Location: ../index.php");
exit();
?>