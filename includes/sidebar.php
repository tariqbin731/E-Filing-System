<aside id="menubar" class="menubar light">
  <div class="app-user">
    <div class="media">
    <?php
    include 'dbconnection.php';
try {
    $sql = "SELECT avatar FROM users WHERE userid = :userid LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':userid', $dhid, PDO::PARAM_STR);
    $query->execute();

    // Fetch the result directly as an object
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $image = $result->avatar;
    } else {
        // Handle the case where no user with the specified ID was found
        $image = 'default-avatar.jpg'; // You can set a default image path
    }
} catch (PDOException $e) {
    // Handle any database connection or query errors here
    echo "Database Error: " . $e->getMessage();
}
?>

<div class="media-left">
    <div class="avatar avatar-md avatar-circle">
        <a href="javascript:void(0)">
            <img class="img-responsive" src="assets/images/<?php echo $image; ?>" alt="avatar" />
        </a>
    </div>
</div>


      <div class="media-body">
        <div class="foldable">
          <?php
          $dhid = $_SESSION['efileid'];
          $sql = "SELECT fullname, email from users where userid=:dhid";
          $query = $dbh->prepare($sql);
          $query->bindParam(':dhid', $dhid, PDO::PARAM_STR);
          $query->execute();
          $results = $query->fetchAll(PDO::FETCH_OBJ);

          foreach ($results as $row) {
            $email = $row->email;
            $fname = $row->fullname;
          } ?>
          <h5><a href="javascript:void(0)" class="username">
              <?php echo $fname; ?>
            </a></h5>
          <ul>
            <li class="dropdown">
              <a href="javascript:void(0)" class="dropdown-toggle usertitle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <small>
                  <?php echo $email; ?>
                </small>
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu animated flipInY">
                <li>
                  <a class="text-color" href="dashboard.php">
                    <span class="m-r-xs"><i class="fa fa-home"></i></span>
                    <span>Home</span>
                  </a>
                </li>
                <li>
                  <a class="text-color" href="profile.php">
                    <span class="m-r-xs"><i class="fa fa-user"></i></span>
                    <span>Profile</span>
                  </a>
                </li>
                <li>
                  <a class="text-color" href="change-password.php">
                    <span class="m-r-xs"><i class="fa fa-gear"></i></span>
                    <span>Settings</span>
                  </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                  <a class="text-color" href="logout.php">
                    <span class="m-r-xs"><i class="fa fa-power-off"></i></span>
                    <span>logout</span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div><!-- .media-body -->
    </div><!-- .media -->
  </div><!-- .app-user -->

  <div class="menubar-scroll">
    <div class="menubar-scroll-inner">
      <ul class="app-menu">
        <li class="has-submenu">
          <a href="dashboard.php">
            <i class="menu-icon zmdi zmdi-view-dashboard zmdi-hc-lg"></i>
            <span class="menu-text">Dashboard</span>

          </a>

        </li>

        <li class="has-submenu">
          <a href="javascript:void(0)" class="submenu-toggle">
            <i class="menu-icon zmdi zmdi-pages zmdi-hc-lg"></i>
            <span class="menu-text">Handle Application</span>
            <i class="menu-caret zmdi zmdi-hc-sm zmdi-chevron-right"></i>
          </a>
          <ul class="submenu">
           
            <li><a href="approved-application.php"><span class="menu-text">Approved Application</span></a></li>
            <li><a href="cancelled-application.php"><span class="menu-text">Cancelled Application</span></a></li>
            <li><a href="all-application.php"><span class="menu-text">All Application</span></a></li>

          </ul>
        </li>
        <li>
          <a href="check-status.php">
            <i class="menu-icon zmdi zmdi-assignment zmdi-hc-lg"></i>
            <span class="menu-text">Application Tracking</span>
          </a>
        </li>


      </ul><!-- .app-menu -->
    </div><!-- .menubar-scroll-inner -->
  </div><!-- .menubar-scroll -->
</aside>