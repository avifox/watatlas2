<?php
session_start();
include '../shared/dbConfig.php'; // $conn = mysqli connection

if (!isset($_POST['credential'])) {
    exit("No credential received.");
}

$id_token = $_POST['credential'];
$client_id = "714262247037-nqtump2fkm13j9il4on9p4q3sj07qabs.apps.googleusercontent.com";

/* ===============================
   VERIFY GOOGLE TOKEN
================================ */
$response = file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token);
$payload = json_decode($response, true);

if (!$payload || !isset($payload['aud']) || $payload['aud'] !== $client_id) {
    exit("Invalid ID token.");
}

/* ===============================
   EXTRACT USER DATA
================================ */
$email      = $payload['email'] ?? '';
$firstname  = $payload['given_name'] ?? '';
$lastname   = $payload['family_name'] ?? '';
$fullname   = $payload['name'] ?? '';

if (empty($firstname) && !empty($fullname)) {
    $parts = explode(' ', $fullname, 2);
    $firstname = $parts[0];
    $lastname  = $parts[1] ?? '';
}

if (empty($email)) {
    exit("No email returned from Google.");
}

/* ===============================
   CHECK IF USER EXISTS
================================ */
$stmt = $conn->prepare("SELECT userid FROM users WHERE emailaddress = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    // ✅ USER EXISTS
    $row = $result->fetch_assoc();
    $userId = $row['userid'];

    // Optional: update name if changed
    $update = $conn->prepare("
        UPDATE users 
        SET firstname = ?, lastname = ?
        WHERE userid = ?
    ");
    $update->bind_param("ssi", $firstname, $lastname, $userId);
    $update->execute();
    $update->close();

} else {

    // ✅ NEW USER → INSERT
    $insert = $conn->prepare("
        INSERT INTO users (emailaddress, firstname, lastname, accountStatus)
        VALUES (?, ?, ?, 'Active')
    ");
    $insert->bind_param("sss", $email, $firstname, $lastname);
    $insert->execute();

    $userId = $insert->insert_id;
    $insert->close();
}

$stmt->close();

/* ===============================
   STORE SESSION
================================ */
$_SESSION['userid']   = $userId;
$_SESSION['email']    = $email;
$_SESSION['username'] = trim($firstname . ' ' . $lastname);

/* ===============================
   REDIRECT
================================ */
header("Location: ../index.php");
exit;
?>