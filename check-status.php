<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['efileid'] == 0)) {
	header('location:logout.php');
} else {

	?>
	<!DOCTYPE html>
	<html lang="en">

	<head>

		<title>E-Filing System || All Application Detail</title>

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
		<script>
			Breakpoints();
		</script>
	</head>

	<body class="menubar-left menubar-unfold menubar-light theme-primary">
		<!--============= start main area -->
		<?php include_once('includes/header.php'); ?>
		<?php include_once('includes/sidebar.php'); ?>
		<!-- APP MAIN ==========-->
		<main id="app-main" class="app-main">
			<div class="wrap">
				<section class="app-content">
					<div class="row">
						<!-- DOM dataTable -->
						<div class="col-md-12">
							<div class="widget">
								<header class="widget-header">
									<h4 class="widget-title">All Application</h4>
								</header><!-- .widget-header -->
								<hr class="widget-separator">
								<div class="widget-body">
									<div class="table-responsive">
										<table
											class="table table-bordered table-hover js-basic-example dataTable table-custom">
											<thead>
												<tr>
													<th>S.No</th>
													<th>Application Number</th>
													<th>Creator Name</th>
													<th>From Department</th>
													<th>To Department</th>
													<th>DateTime</th>
													<th>Current Department</th>
													<th>Final Status</th>
													<th>Action</th>

												</tr>
											</thead>
											<tbody>
												<?php
												$dhid = $_SESSION['efileid'];
												$dept_id = $_SESSION['efiledeptid'];
												$role_id = $_SESSION['efileroleid'];

												// Existing code for database connection and query here
											
												$sql = "SELECT logs.*, users.fullname as username from logs 
        JOIN documents ON documents.doc_id = logs.doc_id 
        JOIN users ON users.userid = documents.user_id 
        WHERE to_dept_id = :dept_id or from_dept_id = :dept_id  AND to_role_id = :role_id  or from_role_id = :role_id 
        OR logs.from_user_id = :dhid 
        ORDER BY datetime DESC";

												$query = $dbh->prepare($sql);
												$query->bindParam(':dept_id', $dept_id, PDO::PARAM_STR);
												$query->bindParam(':role_id', $role_id, PDO::PARAM_STR);
												$query->bindParam(':dhid', $dhid, PDO::PARAM_STR);
												$query->execute();
												$results = $query->fetchAll(PDO::FETCH_OBJ);

												include 'apicall.php';
												$url = 'http://localhost/SPL/Efile/nstu/api/department/index.php';
												$deptdata = getapidata($url);

												$cnt = 1;
												if ($query->rowCount() > 0) {
													foreach ($results as $row) { ?>
														<tr>
															<td>
																<?php echo htmlentities($cnt); ?>
															</td>
															<td>
																<?php echo htmlentities($row->application_number); ?>
															</td>
															<td>
																<?php echo htmlentities($row->username); ?>
															</td>
															<td>
																<?php
																foreach ($deptdata as $dd) {
																	if ($dd['deptid'] == $row->from_dept_id) {
																		echo $dd['name'];
																		break;
																	}
																}
																// echo htmlentities($row->deptname); ?>
															</td>
															<td>
																<?php
																foreach ($deptdata as $dd) {
																	if ($dd['deptid'] == $row->to_dept_id) {
																		echo $dd['name'];
																		break;
																	}
																}
																// echo htmlentities($row->deptname); ?>
															</td>

															<td>
																<?php echo htmlentities($row->datetime); ?>
															</td>

															<?php
															// New code to retrieve to_dept_id and status
															$doc_id = $row->doc_id;
															$sql = "SELECT to_dept_id, status FROM logs WHERE doc_id = :doc_id ORDER BY datetime DESC LIMIT 1";
															$query = $dbh->prepare($sql);
															$query->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
															$query->execute();
															$result = $query->fetch(PDO::FETCH_ASSOC);

															if ($result) {
																$to_dept_id = $result['to_dept_id'];
																$status = $result['status'];

																// Retrieve the department name for to_dept_id
																$dept_name = "";
																foreach ($deptdata as $dd) {
																	if ($dd['deptid'] == $to_dept_id) {
																		$dept_name = $dd['name'];
																		break;
																	}
																}

																echo "<td>" . htmlentities($dept_name) . "</td>"; // Display the department name
																echo "<td>" . htmlentities($status) . "</td>";
															} else {
																echo "<td></td>"; // Display empty cell if no record found
																echo "<td></td>"; // Display empty cell if no record found
															}
															?>

															<td><a href="view-application-detail.php?lid=<?php echo $row->id; ?>&editid=<?php echo htmlentities($row->doc_id); ?>&&aptid=<?php echo htmlentities($row->application_number); ?>"
																	class="btn btn-primary">View</a></td>

														</tr>
														<?php $cnt = $cnt + 1;
													}
												}
												?>

											<tfoot>
												<tr>
													<th>S.No</th>
													<th>Application Number</th>
													<th>Creator Name</th>
													<th>From Department</th>
													<th>To Department</th>
													<th>DateTime</th>
													<th>Current Department</th>
													<th>Final Status</th>
													<th>Action</th>
												</tr>
											</tfoot>
										</table>
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
	</body>

	</html>
<?php } ?>