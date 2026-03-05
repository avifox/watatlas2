<?php
include '../../shared/dbConfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['userid'])){
    die("Not authorized");
}

$reviewId = intval($_POST['reviewid']);
$placeId  = intval($_POST['placeid']);
$userId   = $_SESSION['userid'];

/*
CHECK IF USER ALREADY FLAGGED
*/
$stmt = $conn->prepare("
    SELECT id 
    FROM user_review_flag
    WHERE userid = ? AND reviewid = ?
");

$stmt->bind_param("ii", $userId, $reviewId);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){

    /*
    UPDATE FLAG COUNT
    */
    $stmt = $conn->prepare("
        UPDATE reviews
        SET flagged = flagged + 1
        WHERE id = ?
    ");
    $stmt->bind_param("i", $reviewId);
    $stmt->execute();
    $stmt->close();

    /*
    INSERT USER FLAG
    */
    $stmt = $conn->prepare("
        INSERT INTO user_review_flag (userid, reviewid, flagdate)
        VALUES (?, ?, NOW())
    ");
    $stmt->bind_param("ii", $userId, $reviewId);
    $stmt->execute();
    $stmt->close();
}

header("Location: place-review.php?id=".$placeId);
exit;
?>