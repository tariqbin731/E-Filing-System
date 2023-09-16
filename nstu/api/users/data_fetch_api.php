<?php
include('../../../includes/dbconnection.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the POST data
        $userid = $_POST['userid'];
        $token = $_POST['token'];

        // Query to fetch user information
        $query = "SELECT * FROM users WHERE userid=:userid AND token=:token";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Respond with user information as JSON data
            http_response_code(200);
            echo json_encode($user);
        } else {
            // User not found or invalid token
            http_response_code(401); // Unauthorized
            echo json_encode(array('message' => 'User not found or invalid token.'));
        }
    } catch (PDOException $e) {
        // Database error
        http_response_code(500); // Internal Server Error
        echo json_encode(array('message' => 'Database error: ' . $e->getMessage()));
    }
} else {
    // Method not allowed
    http_response_code(405); // Method Not Allowed
    echo json_encode(array('message' => 'Method not allowed.'));
}
?>
