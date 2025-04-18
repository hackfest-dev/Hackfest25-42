<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: ../login_teacher.php");
}
include '../config.php';
error_reporting(0);
$exid = $_POST['exid'];

$sql = "SELECT * FROM atmpt_list WHERE exid='$exid' ORDER BY ptg ASC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <title>Results</title>
  <link rel="stylesheet" href="css/dash.css">
  <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div class="sidebar">
    <div class="logo-details">
      <i class='bx bx-diamond'></i>
      <span class="logo_name">Welcome</span>
    </div>
    <ul class="nav-links">
      <li>
        <a href="dash.php">
          <i class='bx bx-grid-alt'></i>
          <span class="links_name">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="exams.php">
          <i class='bx bx-book-content'></i>
          <span class="links_name">Exams</span>
        </a>
      </li>
      <li>
        <a href="#" class="active">
          <i class='bx bxs-bar-chart-alt-2'></i>
          <span class="links_name">Results</span>
        </a>
      </li>
      <li>
        <a href="records.php">
          <i class='bx bxs-user-circle'></i>
          <span class="links_name">Records</span>
        </a>
      </li>
      <li>
        <a href="messages.php">
          <i class='bx bx-message'></i>
          <span class="links_name">Messages</span>
        </a>
      </li>
      <li>
        <a href="settings.php">
          <i class='bx bx-cog'></i>
          <span class="links_name">Settings</span>
        </a>
      </li>
      <li>
        <a href="help.php">
          <i class='bx bx-help-circle'></i>
          <span class="links_name">Help</span>
        </a>
      </li>
      <li class="log_out">
        <a href="../logout.php">
          <i class='bx bx-log-out-circle'></i>
          <span class="links_name">Log out</span>
        </a>
      </li>
    </ul>
  </div>
  <section class="home-section">
    <nav>
      <div class="sidebar-button">
        <i class='bx bx-menu sidebarBtn'></i>
        <span class="dashboard">Teacher's Dashboard</span>
      </div>
      <div class="profile-details">
        <img src="<?php echo $_SESSION['img']; ?>" alt="pro">
        <span class="admin_name"><?php echo $_SESSION['fname']; ?></span>

      </div>
    </nav>

    <div class="home-content">
      <div class="stat-boxes">
        <div class="recent-stat box" style="padding: 0px 0px;width:100%;">
          <table>
            <thead>
              <tr>
                <th>Sl. no</th>
                <th>Student name</th>
                <th>Total questions</th>
                <th>Correct answers</th>
                <th>Percentage</th>
                <th>Integrity Score</th>
                <th>Certificate</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 1;
              if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
              ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php
                        $uname = $row['uname'];
                        $ex = "SELECT fname FROM student WHERE uname='$uname'";
                        $name = mysqli_query($conn, $ex);
                        $findname = mysqli_fetch_assoc($name);
                        echo $findname['fname']; ?></td>
                    <td><?php echo $row['nq']; ?></td>
                    <td><?php echo $row['cnq']; ?></td>
                    <td><?php echo $row['ptg']; ?></td>
                    <td>
                      <?php
                      // Display integrity score if it exists
                      if (isset($row['integrity_score'])) {
                        echo "<span class='integrity-score ";
                        // Add class based on integrity category
                        if (isset($row['integrity_category'])) {
                          if ($row['integrity_category'] === 'Good') {
                            echo "good";
                          } else if ($row['integrity_category'] === 'At-Risk') {
                            echo "at-risk";
                          } else {
                            echo "cheating";
                          }
                        }
                        echo "'>" . $row['integrity_score'] . " <span class='integrity-category'>(" . ($row['integrity_category'] ?? 'Good') . ")</span></span>";

                        // Add link to view detailed integrity report
                        echo "<br><a href='view_violations.php?id=" . $row['id'] . "' class='violations-link' target='_blank'>
                          <i class='bx bx-detail'></i> View Details
                        </a>";
                      } else {
                        echo "<span class='integrity-score good'>100 <span class='integrity-category'>(Good)</span></span>";
                      }
                      ?>
                    </td>
                    <td>
                      <a href="view_certificate.php?id=<?php echo $row['id']; ?>" class="cert-btn" target="_blank">
                        <i class='bx bx-download'></i> Certificate
                      </a>
                    </td>
                  </tr>
              <?php
                  $i++;
                }
              } else {
                echo "<script>alert('No results available yet. Please try again.');</script>";
                header("Location: results.php");
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <script src="../js/script.js"></script>

  <style>
    .cert-btn {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      background-color: #0A2558;
      color: white;
      border-radius: 4px;
      text-decoration: none;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .cert-btn:hover {
      background-color: #0d3a80;
    }

    .cert-btn i {
      margin-right: 5px;
    }

    .integrity-score {
      font-weight: bold;
      padding: 4px 8px;
      border-radius: 4px;
      display: inline-block;
    }

    .integrity-score.good {
      background-color: #d4edda;
      color: #155724;
    }

    .integrity-score.at-risk {
      background-color: #fff3cd;
      color: #856404;
    }

    .integrity-score.cheating {
      background-color: #f8d7da;
      color: #721c24;
    }

    .integrity-category {
      font-size: 12px;
      opacity: 0.8;
    }

    .violations-link {
      display: inline-flex;
      align-items: center;
      font-size: 12px;
      margin-top: 5px;
      color: #0A2558;
      text-decoration: none;
    }

    .violations-link:hover {
      text-decoration: underline;
    }

    .violations-link i {
      margin-right: 3px;
      font-size: 14px;
    }
  </style>

</body>

</html>