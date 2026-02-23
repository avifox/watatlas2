<?php
class User {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    // Register or update user
    public function saveUser($email, $firstname, $lastname) {
        $stmt = $this->conn->prepare("
            INSERT INTO users (emailaddress, firstname, lastname)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE firstname = VALUES(firstname), lastname = VALUES(lastname)
        ");
        $stmt->bind_param("sss", $email, $firstname, $lastname);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Get user by ID
    public function getUserById($userid) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE userid = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
