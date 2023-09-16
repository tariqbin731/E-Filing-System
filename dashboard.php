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

		<title>E-Filing System of NSTU - Dashboard</title>

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

			<!-- old feature start -->


			<div class="page-header">
				<h1>Dashboard</h1>
				<small>Home / Dashboard</small>
			</div>

			<div class="page-content">

				<div class="analytics">

					<div class="card">
						<div class="card-head">
							<h2>Application Form</h2>
							<span class="las la-folder-open"></span>
						</div>
						<div class="card-progress">
							<small>
								<h3>
									<ul>
										<li><a href="application_write.php">1. NOC</a></li>
										<li><a href="application_write.php">2. Financial Application</a></li>
										<li><a href="application_write.php">3. Administrative Application</a></li>
									</ul>
								</h3>
							</small>
							<div class="card-indicator">
								<div class="indicator one" style="width: 60%"></div>
							</div>
							<div>
								<ul class="pagination justify-content-center">
									<li class="page-item disabled">
										<a class="page-link" href="#" aria-label="Previous">
											<span aria-hidden="true">&laquo;</span>
										</a>
									</li>
									<li class="page-item active"><a class="page-link" href="#">1</a></li>
									<li class="page-item"><a class="page-link" href="#">2</a></li>
									<li class="page-item"><a class="page-link" href="#">3</a></li>
									<!-- Add more page items as needed -->
									<li class="page-item">
										<a class="page-link" href="#" aria-label="Next">
											<span aria-hidden="true">&raquo;</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-head">
							<h2>File Status</h2>
							<span class="las la-eye"></span>
						</div>
						<div class="card-progress">
							<small>
								<h3>
									<ul>
										<li><a href="all-application.php">1. NOC</a> </li>
										<li><a href="all-application.php">2. Financial</a> </li>
										<li><a href="all-application.php">3. Administrative</a> </li>
									</ul>
								</h3>
							</small>
							<div class="card-indicator">
								<div class="indicator two" style="width: 80%"></div>
							</div>
							<div>
								<ul class="pagination justify-content-center">
									<li class="page-item disabled">
										<a class="page-link" href="#" aria-label="Previous">
											<span aria-hidden="true">&laquo;</span>
										</a>
									</li>
									<li class="page-item active"><a class="page-link" href="#">1</a></li>
									<li class="page-item"><a class="page-link" href="#">2</a></li>
									<li class="page-item"><a class="page-link" href="#">3</a></li>
									<!-- Add more page items as needed -->
									<li class="page-item">
										<a class="page-link" href="#" aria-label="Next">
											<span aria-hidden="true">&raquo;</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>

				</div>

				<div class="records table-responsive">
					<div class="col-md-6 col-sm-6">
						<div class="widget stats-widget">
							<div class="widget-body clearfix">
								<?php
								$dhid = $_SESSION['efileid'];
								;
								$dept_id = $_SESSION['efiledeptid'];
								$role_id = $_SESSION['efileroleid'];
								$sql = "SELECT * from  logs where status='Accepted' && to_dept_id=:dept_id && to_role_id=:role_id";
								$query = $dbh->prepare($sql);
								$query->bindParam(':dept_id', $dept_id, PDO::PARAM_STR);
								$query->bindParam(':role_id', $role_id, PDO::PARAM_STR);
								$query->execute();
								$results = $query->fetchAll(PDO::FETCH_OBJ);
								$totappapt = $query->rowCount();
								?>
								<div class="pull-left">
									<h3 class="widget-title text-success"><span class="counter" data-plugin="counterUp">
											<?php echo htmlentities($totappapt); ?>
										</span></h3>
									<small class="text-color">Total Approved</small>
								</div>
								<span class="pull-right big-icon watermark"><i class="fa fa-unlock-alt"></i></span>

							</div>
							<footer class="widget-footer bg-success">
								<a href="approved-application.php"><small> View Detail</small></a>
								<span class="small-chart pull-right" data-plugin="sparkline"
									data-options="[1,2,3,5,4], { type: 'bar', barColor: '#ffffff', barWidth: 5, barSpacing: 2 }"></span>
							</footer>
						</div><!-- .widget -->
					</div>

					<div class="col-md-6 col-sm-6">
						<div class="widget stats-widget">
							<div class="widget-body clearfix">
								<div class="pull-left">
									<?php
									$dhid = $_SESSION['efileid'];
									;
									$dept_id = $_SESSION['efiledeptid'];
									$role_id = $_SESSION['efileroleid'];
									$sql = "SELECT * from  logs where status='Rejected' && to_dept_id=:dept_id && to_role_id=:role_id";
									$query = $dbh->prepare($sql);
									$query->bindParam(':dept_id', $dept_id, PDO::PARAM_STR);
									$query->bindParam(':role_id', $role_id, PDO::PARAM_STR);
									$query->execute();
									$results = $query->fetchAll(PDO::FETCH_OBJ);
									$totncanapt = $query->rowCount();
									?>
									<h3 class="widget-title text-danger"><span class="counter" data-plugin="counterUp">
											<?php echo htmlentities($totncanapt); ?>
										</span></h3>
									<small class="text-color">Cancelled Application</small>
								</div>
								<span class="pull-right big-icon watermark"><i class="fa fa-ban"></i></span>
							</div>
							<footer class="widget-footer bg-danger">
								<a href="cancelled-application.php"><small> View Detail</small></a>
								<span class="small-chart pull-right" data-plugin="sparkline"
									data-options="[2,4,3,4,3], { type: 'bar', barColor: '#ffffff', barWidth: 5, barSpacing: 2 }"></span>
							</footer>
						</div><!-- .widget -->
					</div>

					<div class="col-md-6 col-sm-6">
						<div class="widget stats-widget">
							<div class="widget-body clearfix">

								<div class="pull-left">
									<?php
									$dhid = $_SESSION['efileid'];
									;
									$dept_id = $_SESSION['efiledeptid'];
									$role_id = $_SESSION['efileroleid'];
									$sql = "SELECT * from  logs where to_dept_id=:dept_id && to_role_id=:role_id";
									$query = $dbh->prepare($sql);
									$query->bindParam(':dept_id', $dept_id, PDO::PARAM_STR);
									$query->bindParam(':role_id', $role_id, PDO::PARAM_STR);

									$query->execute();
									$results = $query->fetchAll(PDO::FETCH_OBJ);
									$totapt = $query->rowCount();
									?>
									<h3 class="widget-title text-primary"><span class="counter" data-plugin="counterUp">
											<?php echo htmlentities($totapt); ?>
										</span></h3>
									<small class="text-color">Total Application</small>
								</div>
								<span class="pull-right big-icon watermark"><i class="fa fa-file-text-o"></i></span>
							</div>
							<footer class="widget-footer bg-primary">
								<a href="all-application.php"><small> View Detail</small></a>
								<span class="small-chart pull-right" data-plugin="sparkline"
									data-options="[5,4,3,5,2],{ type: 'bar', barColor: '#ffffff', barWidth: 5, barSpacing: 2 }"></span>
							</footer>
						</div><!-- .widget -->
					</div>
				</div><!-- .row -->



				<div class="row">

					</section><!-- #dash-content -->
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