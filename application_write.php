<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
include 'apicall.php';
if (strlen($_SESSION['efileid'] == 0)) {
    header('location:logout.php');
} else {

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>

        <title>E-Filing System of NSTU - Application Write</title>

        <link rel="stylesheet" href="libs/bower/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="libs/bower/material-design-iconic-font/dist/css/material-design-iconic-font.css">
        <!-- build:css assets/css/app.min.css -->
        <link rel="stylesheet" href="libs/bower/animate.css/animate.min.css">
        <link rel="stylesheet" href="libs/bower/fullcalendar/dist/fullcalendar.min.css">
        <link rel="stylesheet" href="libs/bower/perfect-scrollbar/css/perfect-scrollbar.css">
        <link rel="stylesheet" href="assets/css/bootstrap.css">
        <link rel="stylesheet" href="assets/css/core.css">
        <link rel="stylesheet" href="assets/css/app.css">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet"
            href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
        <!-- endbuild -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
        <script src="libs/bower/breakpoints.js/dist/breakpoints.min.js"></script>
        <!-- <script>
            Breakpoints();
        </script> -->


        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>


        <style>
            .summernote {
                margin-left: 500px;
            }

            .form-group {
                padding-left: 5px;
                word-spacing: 5;
            }
        </style>

    </head>

    <body class="menubar-left menubar-unfold menubar-light theme-primary">
        <!--============= start main area -->

        <?php include_once('includes/header.php'); ?>

        <?php include_once('includes/sidebar.php'); ?>

        <!-- APP MAIN ==========-->
        <main id="app-main" class="app-main">
            <div class="records table-responsive">

                <div class="page-header">
                    <h1>Application</h1>
                    <small>Home / Dashboard / Write Application</small>
                </div>

                <form action="submit-file.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="app_no">Application Number</label>
                        <input type="number" class="form-control" id="app_no" name="app_no" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <textarea id="summernote" name="description"><b>
                      <p>Date <br> To, <br>From, <br> Subject: Application for financial approval.<br> <br>Dear [Recipient's Name],
                        <br><br><br>Body<br>.....<br>......<br>......<br><br>
                      <p>Your Faithfully,</p>
                      <p>[Your Name]</p>
                      </p>
                    </b></textarea>

                    <div class="form-group">
                        <label for="fileUpload">Attach Files:</label>
                        <input type="file" class="form-control-file" id="fileUpload" name="fileUpload[]" multiple required>
                    </div>
                    <div class="form-group">


                        <label for="role">Select Destination:</label>
                        <select id="role" name="role" onchange="">
                            <option value="" disabled>Select Role</option>
                            <?php
                            // Include database connection code
                            include('includes/dbconnection.php');

                            // Query to fetch departments from the database
                            $department_query = "SELECT rid, name FROM role";
                            $result = $dbh->query($department_query);

                            // Loop through the departments and create options
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['rid']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                        <!-- Department Selection -->
                        <label for="department">Select Department: </label>
                        <select id="department" name="department" onchange="">
                            <option value="" disabled>Select Department</option>
                            <?php

                            $url = 'http://localhost/SPL/Efile/nstu/api/department/index.php';
                            foreach (getapidata($url) as $row) {
                                echo "<option value='{$row['deptid']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                        <input type="submit" name="submit" class="btn btn-primary" id="sendButton" value="send">
                        <button type="button" name="cancel" onclick="remove()" class="btn btn-danger"
                            id="cancelBtn">Cancel</button>
                    </div>


                </form>
                <script>
                    $(document).ready(function () {
                        $('#summernote').summernote();
                    });
                </script>
                <script>
                    $('#summernote').summernote({
                        placeholder: 'Hello Bootstrap 5',
                        tabsize: 4,
                        height: 400
                    });
                </script>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

                <?php include_once('includes/footer.php'); ?>
                <!-- /#app-footer -->
        </main>
        <!--========== END app main -->

        <!-- build:js assets/js/core.min.js -->
        <script src="libs/bower/jquery/dist/jquery.js"></script>
        <script src="libs/bower/jquery-ui/jquery-ui.min.js"></script>
        <script src="libs/bower/jQuery-Storage-API/jquery.storageapi.min.js"></script>
        <script src="libs/bower/bootstrap-sass/assets/javascripts/bootstrap.js"></script>
        <script src="libs/bower/jquery-slimscroll/jquery.slimscroll.js"></script>
        <script src="libs/bower/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
        <script src="libs/bower/PACE/pace.min.js"></script>
        <!-- endbuild -->

        <!-- build:js assets/js/app.min.js -->
        <script src="assets/js/library.js"></script>
        <script src="assets/js/plugins.js"></script>
        <script src="assets/js/app.js"></script>
        <!-- endbuild -->
        <script src="libs/bower/moment/moment.js"></script>
        <script src="libs/bower/fullcalendar/dist/fullcalendar.min.js"></script>
        <script src="assets/js/fullcalendar.js"></script>

        <!-- <script src="dynamic_dropdown.js"></script> -->
    </body>

    </html>
<?php } ?>