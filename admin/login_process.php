<?php
session_start();
include __DIR__ . "/../shared/dbconfig.php"; // adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = $_POST['username'];
    $organization = $_POST['organization'];
    $password     = $_POST['password'];

    // Fetch user by username + organization
    $stmt = $conn->prepare("SELECT * FROM managedusers WHERE username=? AND organization=?");
    $stmt->bind_param("ss", $username, $organization);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();

    if ($user) {
        // Check if account is blocked
        if ($user['status'] === 'Blocked') {
            echo "<div class='alert alert-danger'>Your account is blocked due to multiple failed login attempts.</div>";
            echo "<a href='login.php'>Back to login</a>";
            exit;
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Reset passwordcount on successful login
            $stmt = $conn->prepare("UPDATE managedusers SET passwordcount=0 WHERE id=?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();

            // ✅ Create session with username and organization
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['organization'] = $user['organization']; // stored here
            $_SESSION['email']     = $user['emailaddress'];


            // Redirect based on organization
            if (strtolower($user['organization']) === 'watatlas') {
                header("Location: manage_place.php");
            } else {
                header("Location: manage_my_place.php");
            }
            exit;
        } else {
            // Wrong password: increment counter
            $newCount = $user['passwordcount'] + 1;

            if ($newCount >= 3) {
                // Block the account
                $stmt = $conn->prepare("UPDATE managedusers SET status='Blocked', passwordcount=? WHERE id=?");
                $stmt->bind_param("ii", $newCount, $user['id']);
                $stmt->execute();

                echo "<div class='alert alert-danger'>Your account has been blocked after 3 failed attempts.</div>";
                echo "<a href='login.php'>Back to login</a>";
            } else {
                // Update count only
                $stmt = $conn->prepare("UPDATE managedusers SET passwordcount=? WHERE id=?");
                $stmt->bind_param("ii", $newCount, $user['id']);
                $stmt->execute();

                echo "<div class='alert alert-danger'>Invalid password. Attempt $newCount of 3.</div>";
                echo "<a href='login.php'>Back to login</a>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>User not found.</div>";
        echo "<a href='login.php'>Back to login</a>";
    }
}
?>
