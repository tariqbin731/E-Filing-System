<nav id="app-navbar" class="navbar navbar-inverse navbar-fixed-top primary">

  <!-- navbar header -->
  <div class="navbar-header">
    <button type="button" id="menubar-toggle-btn"
      class="navbar-toggle visible-xs-inline-block navbar-toggle-left hamburger hamburger--collapse js-hamburger">
      <span class="sr-only">Toggle navigation</span>
      <span class="hamburger-box"><span class="hamburger-inner"></span></span>
    </button>

    <button type="button" class="navbar-toggle navbar-toggle-right collapsed" data-toggle="collapse"
      data-target="#app-navbar-collapse" aria-expanded="false">
      <span class="sr-only">Toggle navigation</span>
      <span class="zmdi zmdi-hc-lg zmdi-more"></span>
    </button>

    <button type="button" class="navbar-toggle navbar-toggle-right collapsed" data-toggle="collapse"
      data-target="#navbar-search" aria-expanded="false">
      <span class="sr-only">Toggle navigation</span>
      <span class="zmdi zmdi-hc-lg zmdi-search"></span>
    </button>

    <a href="dashboard.php" class="navbar-brand">
      <span class="brand-icon"><i class="fa fa-gg"></i></span>
      <span class="brand-name">E-Filing System</span>
    </a>
  </div><!-- .navbar-header -->

  <div class="navbar-container container-fluid">
    <div class="collapse navbar-collapse" id="app-navbar-collapse">
      <ul class="nav navbar-toolbar navbar-toolbar-left navbar-left">
        <li class="hidden-float hidden-menubar-top">
          <a href="javascript:void(0)" role="button" id="menubar-fold-btn"
            class="hamburger hamburger--arrowalt is-active js-hamburger">
            <span class="hamburger-box"><span class="hamburger-inner"></span></span>
          </a>
        </li>
        <li>
          <h5 class="page-title hidden-menubar-top hidden-float"></h5>
        </li>
      </ul>
      <?php
      $dhid = $_SESSION['efileid'];
      $dept_id = $_SESSION['efiledeptid'] ?: null;
      $role_id = $_SESSION['efileroleid'];
      $total_notification = "SELECT COUNT(*) as total FROM notifications WHERE to_dept_id = :dept_id AND to_role_id = :role_id and is_read=0";
      $query1 = $dbh->prepare($total_notification);

      if ($dept_id) {
        $dept_id = intval($dept_id);
        $query1->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
      } else {
        $query1->bindValue(':dept_id', null);
      }
      $query1->bindParam(':role_id', $role_id, PDO::PARAM_INT);
      $query1->execute();
      $count_notification = $query1->fetchAll(PDO::FETCH_OBJ);
      ?>

      <ul class="nav navbar-toolbar navbar-toolbar-right navbar-right">


        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
            aria-expanded="false">
            <i class="zmdi zmdi-hc-lg zmdi-notifications"></i>
            <span class="badge badge-danger" id="notificationCount">
              <?php echo $count_notification[0]->total; ?>
            </span>
          </a>
          <div class="media-group dropdown-menu animated flipInY">
            <?php
            $dhid = $_SESSION['efileid'];
            $dept_id = $_SESSION['efiledeptid'] ?: null;
            $role_id = $_SESSION['efileroleid'];

            $role_id = intval($role_id);
            $sql = "SELECT * from notifications where to_dept_id=:dept_id and to_role_id=:role_id and is_read=0 order by datetime DESC limit 5";

            $query = $dbh->prepare($sql);


            if ($dept_id) {
              $dept_id = intval($dept_id);
              $query->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
            } else {
              $query->bindValue(':dept_id', null);
            }
            $query->bindParam(':role_id', $role_id, PDO::PARAM_INT);


            $query->execute();


            $results = $query->fetchAll(PDO::FETCH_OBJ);



            $cnt = 1;
            $totalappintments = $query->rowCount();
            foreach ($results as $row) {
              
          
              ?>
             
              <a href="<?php echo $row->action; ?>" onclick="read_notification(<?php echo $row->notification_id; ?>)"
                class="media-group-item">
                <div class="media">
                  <div class="media-left">
                    <div class="avatar avatar-xs avatar-circle">
                      <!-- <img src="assets/images/<?php $image; ?>" alt=""> -->
                      <i class="status status-online"></i>
                    </div>
                  </div>
                  <div class="media-body">
                    <h5 class="media-heading">New Application</h5>
                    <small class="media-meta">
                      <?php echo $row->message; ?> at (
                      <?php echo $row->datetime; ?>)
                    </small>
                  </div>
                </div>
              </a><!-- .media-group-item -->
            <?php } ?>
          </div>
        </li>

        <li class="dropdown">
          <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
            aria-expanded="false"><i class="zmdi zmdi-hc-lg zmdi-settings"></i></a>
          <ul class="dropdown-menu animated flipInY">
            <li><a href="profile.php"><i class="zmdi m-r-md zmdi-hc-lg zmdi-account-box"></i>My Profile</a></li>
            <li><a href="change-password.php"><i class="zmdi m-r-md zmdi-hc-lg zmdi-balance-wallet"></i>Change
                Password</a></li>
            <li><a href="logout.php"><i class="zmdi m-r-md zmdi-hc-lg zmdi-sign-in"></i>Logout</a></li>

          </ul>
        </li>

      </ul>
    </div>
  </div><!-- navbar-container -->
</nav>
