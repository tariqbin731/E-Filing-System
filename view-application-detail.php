<?php
session_start();
error_reporting(0);

include('includes/dbconnection.php');
if (strlen($_SESSION['efileid'] == 0)) {
  header('location:logout.php');
} else {
  $lid = $_GET['lid'];
  $aptid = $_GET['aptid'];
  $eid = $_GET['editid'];
  $dept_id = intval($_SESSION['efiledeptid']);
  $user_id = intval($_SESSION['efileid']);


  if (isset($_POST['submit'])) {
    $status = $_POST['status'];
    $department_name = intval($_POST['department']);
    $comment = $_POST['comment'];
    // Add input for department selection
    $role_name = intval($_POST['role']);

    try {
      if (!empty($_FILES['fileUpload']['name'][0])) {
        $file_directory = 'commentuploads/';

        $tmp_name = $_FILES['fileUpload']['tmp_name'];
        $fileType = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '.' . $fileType;

        $attachment = $file_directory . $file_name;
        move_uploaded_file($tmp_name, $attachment);

        $insert_file_query = "INSERT INTO comment (co_msg, userid, docid, attachment,file_name) VALUES (:comment, :userid, :docid, :attachment, :file_name)";
        $stmt_file = $dbh->prepare($insert_file_query);

        $stmt_file->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt_file->bindParam(':userid', $user_id, PDO::PARAM_STR);
        $stmt_file->bindParam(':docid', $eid, PDO::PARAM_STR);
        $stmt_file->bindParam(':attachment', $attachment, PDO::PARAM_STR);
        $stmt_file->bindParam(':file_name', $file_name, PDO::PARAM_STR);
        if ($stmt_file->execute()) {
          $cid = $dbh->lastInsertId();

          $frid = $_SESSION['efileroleid'];
          $insert_query = "INSERT INTO logs(from_user_id, from_dept_id, to_dept_id,from_role_id, to_role_id, status, comment_id ,doc_id, application_number) VALUES (:user_id, :dept_id, :department_name,:frid,:role_name, :status, :com_id, :docid,:application_no)";

          $stmt = $dbh->prepare($insert_query);
          $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
          $stmt->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
          $stmt->bindParam(':department_name', $department_name, PDO::PARAM_INT);
          $stmt->bindParam(':frid', $frid, PDO::PARAM_INT);
          $stmt->bindParam(':role_name', $role_name, PDO::PARAM_INT);
          $stmt->bindParam(':status', $status, PDO::PARAM_STR);
          $stmt->bindParam(':docid', $eid, PDO::PARAM_INT);
          $stmt->bindParam(':com_id', $cid, PDO::PARAM_INT);
          $stmt->bindParam(':application_no', $aptid, PDO::PARAM_INT);
          $stmt->execute();
          if ($status !== "Pending") {
            $query = "update documents set status=:status where doc_id=:docid";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':docid', $eid, PDO::PARAM_INT);
            $stmt->execute();
          }
          echo "File '$file_name' uploaded and record inserted successfully.<br>";
        } else {
          echo "Error uploading file '$file_name': " . $stmt_file->errorInfo()[2] . "<br>";
        }

        $stmt_file->closeCursor();

      }

      echo "Data submitted successfully!";

    } catch (PDOException $ex) {
      die($ex->getMessage());
    }

    // Assuming you have retrieved the necessary information
    $message = "A new application submitted to your office."; //  the notification message
    $action = "all-application.php"; //  the action URL

    try {
      // SQL query to insert the notification
      $sql = "INSERT INTO notifications (doc_id, message, action, to_role_id, to_dept_id) VALUES (:docid, :message, :action,:role_name,:department_name)";
      $query = $dbh->prepare($sql);
      if (!$query) {
        echo "SQL Error: " . $dbh->errorInfo()[2]; // Display the SQL error message
        exit;
      }
      $query->bindParam(':docid', $eid, PDO::PARAM_INT);
      $query->bindParam(':message', $message, PDO::PARAM_STR);
      $query->bindParam(':action', $action, PDO::PARAM_STR);
      $query->bindParam(':department_name', $department_name, PDO::PARAM_INT);
      $query->bindParam(':role_name', $role_name, PDO::PARAM_INT);
      $query->execute();
    } catch (PDOException $e) {
      // Handle database errors
      echo "Error: " . $e->getMessage();
    }

    echo '<script>alert("Comment and status has been updated")</script>';
    echo "<script>window.location.href ='all-application.php'</script>";
  }

  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>

    <title>E-Filing System || View Application Detail</title>

    <link rel="stylesheet" href="libs/bower/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="libs/bower/material-design-iconic-font/dist/css/material-design-iconic-font.css">
    <!-- build:css assets/css/app.min.css -->
    <link rel="stylesheet" href="libs/bower/animate.css/animate.min.css">
    <link rel="stylesheet" href="libs/bower/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="libs/bower/perfect-scrollbar/css/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/core.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <!-- endbuild -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
    <script src="libs/bower/breakpoints.js/dist/breakpoints.min.js"></script>

  </head>

  <body class="menubar-left menubar-unfold menubar-light theme-primary">
    <!--============= start main area -->

    <?php include_once('includes/header.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <?php include 'apicall.php'; ?>

    <!-- APP MAIN ==========-->
    <main id="app-main" class="app-main">
      <div class="wrap">
        <section class="app-content">
          <div class="row">
            <!-- DOM dataTable -->
            <div class="col-md-12">
              <div class="widget">
                <header class="widget-header">
                  <h4 class="widget-title" style="color: blue">Application Details</h4>
                </header><!-- .widget-header -->
                <hr class="widget-separator">

                <div class="widget-body">
                  <div class="table-responsive">
                    <?php
                    $sql = "SELECT logs.*, documents.title AS title, documents.description AS description, documents.status AS final_status, users.fullname AS username, comment.co_msg AS comment_msg
        FROM logs 
        JOIN documents ON logs.doc_id = documents.doc_id 
        JOIN users ON users.userid = documents.user_id 
        LEFT JOIN comment ON logs.comment_id = comment.commentid
        WHERE logs.id = :lid";


                    $query = $dbh->prepare($sql);

                    if (!$query) {
                      echo "SQL Error: " . $dbh->errorInfo()[2]; // Display the SQL error message
                      exit; // Exit the script
                    }

                    $query->bindParam(':lid', $lid, PDO::PARAM_STR);
                    $ret = $query->execute();

                    $results = $query->fetchAll(PDO::FETCH_OBJ);


                    $cnt = 1;
                    if ($query->rowCount() > 0) {
                      foreach ($results as $row) { ?>
                        <table border="1" class="table table-bordered mg-b-0">
                          <tr>
                            <th>Application Number</th>
                            <td>
                              <?php echo $aptno = ($row->application_number); ?>
                            </td>
                            <th>Creator Name</th>
                            <td>
                              <?php echo $row->username; ?>
                            </td>
                          </tr>

                          <tr>
                            <th>Application Title</th>
                            <td>
                              <?php echo $row->title; ?>
                            </td>
                            <th>Status</th>
                            <td>
                              <?php echo $row->status; ?>
                            </td>
                          </tr>
                          <tr>
                            <th>Apply Date</th>
                            <td>
                              <?php echo $row->datetime; ?>
                            </td>
                            <th>Application</th>
                            <td>
                              <?php echo $row->description; ?>
                            </td>
                          </tr>

                          <tr>

                            <th>Application Final Status</th>

                            <td colspan="4">
                              <?php $status = $row->final_status;

                              if ($status == "Pending") {
                                echo "On Processing";
                              } elseif ($status == "Accepted") {
                                echo "Your application has been approved";
                              } elseif ($status == "Rejected") {
                                echo "Your Application has been cancelled";
                              }
                              ; ?>
                            </td>
                          </tr>
                          <tr>
                            <th>
                              Initial Attachments
                            </th>
                            <td colspan="3">
                              <?php
                              $doc_id = $eid;
                              // Query to fetch attachment details for a specific doc_id
                              $sql = "SELECT file_name FROM attachment WHERE docid = :doc_id";
                              $query = $dbh->prepare($sql);
                              $query->bindParam(':doc_id', $doc_id, PDO::PARAM_INT);
                              $query->execute();
                              $attachmentData = $query->fetchAll(PDO::FETCH_ASSOC);

                              // Check if any attachment records were found
                              if ($attachmentData) {
                                // Loop through the attachment records
                                foreach ($attachmentData as $attachment) {
                                  $file_name = $attachment['file_name'];

                                  // Here, you can display or use $file_name as needed
                        
                                  echo htmlentities($file_name) . " ";
                                  echo "<a href='uploads/" . urlencode($file_name) . "' target='_blank'>Open</a>. or ";
                                  echo "<a href='download.php?file=" . urlencode($file_name) . "'>Download</a>";
                                  echo "<br>";
                                }
                              } else {
                                echo "No attachments found for this doc_id.";
                              }
                              ?>

                            </td>
                          </tr>

                          <tr>
                            <th>Application's State Transition</th>
                            <td colspan="3">
                              <?php
                              $doc_id = $eid;
                              $query = "SELECT datetime , status , from_dept_id, to_dept_id, from_role_id, to_role_id
            FROM logs
            WHERE doc_id = :docid
            ORDER BY datetime DESC";

                              $stmt = $dbh->prepare($query);
                              $stmt->bindParam(':docid', $doc_id, PDO::PARAM_INT);
                              $stmt->execute();
                              $transitions = $stmt->fetchAll(PDO::FETCH_OBJ);


                              $deptdata = getapidata('http://localhost/SPL/Efile/nstu/api/department/index.php');


                              $roledata = getapidata('http://localhost/SPL/Efile/nstu/api/role/index.php');


                              // Check if there are transitions
                              if (!empty($transitions)) {
                                echo "<table border='1'>";
                                echo "<thead>";
                                echo "<tr><th>Date & Time</th><th>From Department</th><th>From Role</th><th>To Department</th><th>To Role</th><th>Status</th></tr>";
                                echo "</thead>";
                                echo "<tbody>";

                                // Loop through the transitions and display them in the table
                                foreach ($transitions as $transition) {
                                  // Retrieve the department name for from_dept_id
                                  $from_dept_id = $transition->from_dept_id;
                                  $to_dept_id = $transition->to_dept_id;
                                  $from_dept_name = "";
                                  $to_dept_name = "";
                                  // Retrieve the role names for from_role_id and to_role_id
                                  $from_role_id = $transition->from_role_id;
                                  $to_role_id = $transition->to_role_id;
                                  $from_role_name = "";
                                  $to_role_name = "";

                                  // Find the department name from the fetched departments
                                  foreach ($deptdata as $dd) {
                                    if ($dd['deptid'] == $from_dept_id) {
                                      $from_dept_name = $dd['name'];
                                      break;
                                    }
                                  }
                                  foreach ($deptdata as $dd) {
                                    if ($dd['deptid'] == $to_dept_id) {
                                      $to_dept_name = $dd['name'];
                                      break;
                                    }
                                  }

                                  foreach ($roledata as $rd) {
                                    if ($rd['rid'] == $from_role_id) {
                                      $from_role_name = $rd['name'];
                                      break;
                                    }
                                  }
                                  foreach ($roledata as $rd) {
                                    if ($rd['rid'] == $to_role_id) {
                                      $to_role_name = $rd['name'];
                                      break;
                                    }
                                  }
                                  // You'll need to fetch role names based on from_role_id and to_role_id here
                                  // using similar logic as fetching department names
                        
                                  echo "<tr>";
                                  echo "<td>{$transition->datetime}</td>";
                                  echo "<td>{$from_dept_name}</td>";
                                  echo "<td>{$from_role_name}</td>";
                                  echo "<td>{$to_dept_name}</td>";
                                  echo "<td>{$to_role_name}</td>";
                                  echo "<td>{$transition->status}</td>";
                                  echo "</tr>";
                                }

                                echo "</tbody>";
                                echo "</table>";
                              } else {
                                echo "No state transitions found for this document.";
                              }
                              ?>
                            </td>
                          </tr>

                          <tr>
                            <th>
                              Comment Attachments
                            </th>
                            <td colspan="3">
                              <?php
                              $doc_id = $eid;
                              $sql = "SELECT file_name FROM comment WHERE docid = :doc_id";
                              $query = $dbh->prepare($sql);
                              $query->bindParam(':doc_id', $doc_id, PDO::PARAM_INT);
                              $query->execute();
                              $attachmentData = $query->fetchAll(PDO::FETCH_ASSOC);

                              /// Check if any attachment records were found
                              if ($attachmentData) {
                                // Loop through the attachment records
                                foreach ($attachmentData as $attachment) {
                                  $file_name = $attachment['file_name'];

                                  echo htmlentities($file_name) . " ";
                                  echo "<a href='commentuploads/" . urlencode($file_name) . "' target='_blank'>Open</a>. or ";
                                  echo "<a href='download.php?file=" . urlencode($file_name) . "'>Download</a>";
                                  echo "<br>";
                                }
                              } else {
                                echo "No attachments found ";
                              }
                              ?>
                            </td>
                          </tr>
                          <tr>
                            <th>Comment</th>
                            <td colspan="3">
                              <?php
                              $doc_id = $eid;
                              $query = "SELECT comment.commentid, comment.co_msg AS comment_message, comment.datetime AS datetime, users.fullname AS fullname, comment.attachment AS attachment, comment.file_name AS file_name
                  FROM comment
                  JOIN users ON comment.userid = users.userid
                  WHERE comment.docid = :docid
                  ORDER BY comment.datetime DESC";


                              $stmt = $dbh->prepare($query);
                              $stmt->bindParam(':docid', $doc_id, PDO::PARAM_INT);
                              $stmt->execute();
                              $comments = $stmt->fetchAll(PDO::FETCH_OBJ);

                              //Define an array of background colors for comments
                              $backgroundColors = ['#ffcccc', '#ccffcc', '#ccccff']; // You can customize these colors
                              $commentCount = 0; // Initialize comment count
                        
                              // Loop through the comments and display them in a table
                              foreach ($comments as $comment) {
                                $commentCount++;
                                $bgColor = $backgroundColors[($commentCount - 1) % count($backgroundColors)];

                                echo '<div style="background-color: ' . $bgColor . '; padding: 10px; border: 1px solid #ddd; margin-bottom: 10px;">';

                                if (empty($comment->comment_message)) {
                                  echo 'Not Updated Yet';
                                } else {
                                  echo htmlentities($comment->fullname) . '<br>';
                                  echo htmlentities($comment->datetime) . '<br>';
                                  echo htmlentities($comment->comment_message) . '<br>';

                                  if (!empty($comment->attachment)) {
                                    echo ' <a href="' . htmlentities($comment->attachment) . '" download>' . htmlentities($comment->file_name) . '</a><br>';
                                  }
                                }

                                echo '</div>';
                              }
                              ?>
                            </td>
                          </tr>




                          <?php $cnt = $cnt + 1;
                      }
                    } ?>

                    </table>
                    <br>
                    <?php

                    // Query to fetch the user's role ID
                    $sql = "SELECT role_id FROM users WHERE userid = :user_id";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $query->execute();
                    $result = $query->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                      $user_role_id = $result['role_id'];

                      // Check if the user's role is not 1 or 2, and if the status is "Pending"
                      if ($user_role_id != 1 && $user_role_id != 2 && $status == "Pending") {
                        ?>
                        <p align="center" style="padding-top: 20px">
                          <button class="btn btn-primary waves-effect waves-light w-lg" data-toggle="modal"
                            data-target="#myModal">Take Action</button>
                        </p>
                        <?php
                      }
                    } else {
                      // Handle the case where the user ID is not found
                      echo "User not found.";
                    }
                    ?>


                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                      aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Take Action</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <table class="table table-bordered table-hover data-tables">

                              <form method="post" name="submit" enctype="multipart/form-data">



                                <tr>
                                  <th>Comment :</th>
                                  <td>
                                    <textarea name="comment" placeholder="Comment" rows="12" cols="14"
                                      class="form-control wd-450"></textarea>
                                    <label for="attachments">Attachments:</label>
                                    <input type="file" class="form-control-file" id="attachments" name="fileUpload">

                                  </td>
                                </tr>

                                <tr>
                                  <th>Status :</th>
                                  <td>

                                    <select name="status" class="form-control wd-450" required="true">
                                      <option value="Pending" selected="true">Pending</option>
                                      <option value="Accepted">Accepted</option>
                                      <option value="Rejected">Rejected</option>

                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <th>Forward To :</th>
                                  <td>

                                    <!-- <select name="forward" class="form-control wd-450" required="true"> -->
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

                                    <br>

                                    <!-- Department Selection -->
                                    <label for="department">Select Department: </label>
                                    <select id="department" name="department" onchange="">
                                      <option value="" disabled>Select Department</option>
                                      <?php

                                      // Query to fetch departments from the database
                                    

                                      $url = 'http://localhost/SPL/Efile/nstu/api/department/index.php';
                                      foreach (getapidata($url) as $row) {
                                        echo "<option value='{$row['deptid']}'>{$row['name']}</option>";
                                      }
                                      ?>
                                    </select>

                                    <!-- </select> -->
                                  </td>
                                </tr>
                            </table>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="submit" class="btn btn-primary">Submit</button>

                            </form>
                          </div>
                        </div>
                      </div>

                    </div>

                  </div><!-- .widget-body -->


                </div><!-- .widget -->
              </div><!-- END column -->


            </div><!-- .row -->
        </section><!-- .app-content -->
      </div><!-- .wrap -->
      <!-- APP FOOTER -->
      <?php include_once('includes/footer.php'); ?>
      <!-- /#app-footer -->
    </main>
    <!--========== END app main -->

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
  </body>

  </html>
<?php } ?>