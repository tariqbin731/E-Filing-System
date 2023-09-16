<?php
include('../../../includes/dbconnection.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        $email = $_POST['email'];
        $password = $_POST['password'];

        
        $hashed_password = md5($password);

        $query = "SELECT * FROM users WHERE email=:email AND password=:hashed_password";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':hashed_password', $hashed_password);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            
            $token = bin2hex(random_bytes(16));

           
            $update_query = "UPDATE users SET token=:token WHERE email=:email";
            $update_stmt = $dbh->prepare($update_query);
            $update_stmt->bindParam(':token', $token);
            $update_stmt->bindParam(':email', $email);
            $update_stmt->execute();

           
            http_response_code(200);
            echo json_encode(array('message' => 'Login successful.', 'token' => $token, 'userid' => $user['userid'] , 'dept_id' => $user['dept_id'], 'role_id' => $user['role_id'], 'email' => $user['email'] ));
        } else {
            
            http_response_code(401); 
            echo json_encode(array('message' => 'Invalid credentials.'));
        }
    } catch (PDOException $e) {
        http_response_code(500); 
        echo json_encode(array('message' => 'Database error: ' . $e->getMessage()));
    }
} else {
    http_response_code(405); 
    echo json_encode(array('message' => 'Method not allowed.'));
}
?>
