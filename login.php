<?php
session_start();
error_reporting(0);

if (isset($_SESSION['efileid'])) {
    header("Location: dashboard.php");

}

include('includes/dbconnection.php');

// Google OAuth Configuration
require 'vendor/autoload.php';
$client_id = '650419867810-bbnp61strh8r688oikg0derer9u6iuuv.apps.googleusercontent.com';
$client_secret = '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';
$redirect_uri = 'http://localhost/SPL/Efile/dashboard.php';


// Google Login Handling
$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope('email');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();
    $email = $userInfo->getEmail();
    $name = $userInfo->getName();

    // Check if the user's email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    if ($stmt->execute() && $stmt->rowCount() > 0) {
        header('Location: dashboard.php');
        exit;
    } else {
        require 'logout.php';
        exit;
    }
} else {
    $authUrl = $client->createAuthUrl();
}


if (isset($_POST['email']) && isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];


    if (strpos($email, "@nstu.edu.bd") === false) {
        echo "<script>alert('Invalid Email Format');</script>";
    } else {

        $api_url = 'http://localhost/SPL/Efile/nstu/api/users/login_api.php';


        $data = array(
            'email' => $email,
            'password' => $password,
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL session
        $api_response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            // Handle the API response
            $decoded_response = json_decode($api_response, true);
            // Check if the login was successful
            if (isset($decoded_response['message']) && $decoded_response['message'] === 'Login successful.') {
                $_SESSION['efileid'] = $decoded_response['userid'];
                $_SESSION['efiledeptid'] = $decoded_response['dept_id'];
                $_SESSION['efileroleid'] = $decoded_response['role_id'];
                $_SESSION['efileemailid'] = $decoded_response['email'];
                header('Location: dashboard.php');
                exit;
            } else {
                echo "<script>alert('API Login Failed: " . $decoded_response['message'] . "');</script>";
            }
        }

        curl_close($ch);
    }
}
?>

<!doctype html>
<!DOCTYPE html>
<html lang="en">

<head>

    <title>E-Filing System - Login Page</title>


    <link rel="stylesheet" href="libs/bower/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="libs/bower/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="libs/bower/animate.css/animate.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/core.css">
    <link rel="stylesheet" href="assets/css/misc-pages.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
    <style>
        body {
            background-image: url('assets/images/image/homepage.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
    </style>
</head>

<body class="simple-page">
    <div id="back-to-home">
        <a href="login.php" class="btn btn-outline btn-default"><i class="fa fa-home animated zoomIn"></i></a>
    </div>
    <div class="simple-page-wrap">
        <div class="simple-page-logo animated swing">

            <span style="color: white"><i class="fa fa-gg"></i></span>
            <span style="color: white">E-Filing System</span>

        </div><!-- logo -->
        <div class="simple-page-form animated flipInY" id="login-form">
            <h4 class="form-title m-b-xl text-center">Sign In With Your Institional Mail in EFiling System Of NSTU</h4>
            <form method="post" name="login">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Enter Institutional Email ID" required="true"
                        name="email">
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" name="password" required="true">
                </div>


                <input type="submit" class="btn btn-primary" name="login" value="Sign IN">
                <?php if (isset($authUrl)) { ?>
                    <a href="<?php echo $authUrl; ?>" class="btn btn-danger">Login with Google</a>
                <?php } ?>
            </form>
            <hr />

        </div><!-- #login-form -->

        <div class="simple-page-footer">
            <p><a href="forgot-password.php">FORGOT YOUR PASSWORD ?</a></p>

        </div>
    </div>
</body>

</html>