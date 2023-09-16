<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['efileid'])) {
    header("Location: login.php");
    exit();
}

include('includes/dbconnection.php');

$content = $_POST['description'];
$user_id = $_SESSION['efileid'];
$application_no = $_POST['app_no'];
$dept_id = $_SESSION['efiledeptid'];
$role_id = $_SESSION['efileroleid'];
$title = $_POST['title'];
$department_name = intval($_POST['department']); // Add input for department selection
$role_name = intval($_POST['role']); // Add input for role selection

// Insert a new document
$insert_query = "INSERT INTO documents (title, description, user_id, date_created, application_number) VALUES (:title, :content, :user_id, NOW(), :application_no)";
$stmt = $dbh->prepare($insert_query);
$stmt->bindParam(':title', $title, PDO::PARAM_STR);
$stmt->bindParam(':content', $content, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':application_no', $application_no, PDO::PARAM_INT);


try {

    if ($stmt->execute()) {
        try {
            $docid = $dbh->lastInsertId();
            $frid=$_SESSION['efileroleid'];
            $insert_query = "INSERT INTO logs(from_user_id, from_dept_id, to_dept_id,from_role_id, to_role_id, status, doc_id, application_number) VALUES (:user_id, :dept_id, :department_name,:frid, :role_name, :status, :docid,:application_no)";
            $status = 'Pending';
            $stmt = $dbh->prepare($insert_query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
            $stmt->bindParam(':department_name', $department_name, PDO::PARAM_INT);
            $stmt->bindParam(':frid', $frid, PDO::PARAM_INT);
            $stmt->bindParam(':role_name', $role_name, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':docid', $docid, PDO::PARAM_INT);
            $stmt->bindParam(':application_no', $application_no, PDO::PARAM_INT);
            $stmt->execute();
            echo "<script>alert('Application sent to the receiver destination');</script>";
            echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";

        } catch (PDOException $ex) {
            echo $ex->getMessage();
            die();
        }

        // Insert attachments into the attachment table
        if (!empty($_FILES['fileUpload']['name'][0])) {
            $file_directory = 'uploads/';

            foreach ($_FILES['fileUpload']['tmp_name'] as $key => $tmp_name) {
                $fileType = pathinfo($_FILES['fileUpload']['name'][$key], PATHINFO_EXTENSION);
                $file_name = time() . '.' . $fileType;

                $file_path = $file_directory . $file_name;
                move_uploaded_file($tmp_name, $file_path);

                $insert_file_query = "INSERT INTO attachment (file_name, file_path, datetime, userid, docid) VALUES (:file_name, :file_path, NOW(), :user_id, :docid)";
                $stmt_file = $dbh->prepare($insert_file_query);
                $stmt_file->bindParam(':file_name', $file_name, PDO::PARAM_STR);
                $stmt_file->bindParam(':file_path', $file_path, PDO::PARAM_STR);
                $stmt_file->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt_file->bindParam(':docid', $docid, PDO::PARAM_INT);

                if ($stmt_file->execute()) {
                    echo "File '$file_name' uploaded and record inserted successfully.<br>";
                } else {
                    echo "Error uploading file '$file_name': " . $stmt_file->errorInfo()[2] . "<br>";
                }

                $stmt_file->closeCursor();
            }
        }

        echo "Data submitted successfully!";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
} catch (PDOException $ex) {
    die($ex->getMessage());
}

// Assuming you have retrieved the necessary information

$message = "A new application submitted to your office."; // Customize the notification message
$action = "all-application.php"; // Customize the action URL

try {
    // SQL query to insert the notification
    $sql = "INSERT INTO notifications (doc_id, message, action, to_role_id, to_dept_id) VALUES (:docid,  :message, :action,:role_name ,:department_name)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':docid', $docid, PDO::PARAM_INT);
    $query->bindParam(':message', $message, PDO::PARAM_STR);
    $query->bindParam(':action', $action, PDO::PARAM_STR);
    $query->bindParam(':department_name', $department_name, PDO::PARAM_INT);
    $query->bindParam(':role_name', $role_name, PDO::PARAM_INT);
    $query->execute();

    // Close the database connection
    $dbh = null;

    // Redirect the user to the action URL (e.g., view_application.php)
    header("Location: $action");
    exit();
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}


$dbh = null; // Close the PDO connection
?>