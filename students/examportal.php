<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
if (!isset($_SESSION["uname"])) {
  header("Location: ../login_student.php");
}

include '../config.php';
error_reporting(0);
$exid = $_POST['exid'];

if (!isset($_POST["edit_btn"])) {
  header("Location: exams.php");
}

if (isset($_POST["edit_btn"])) {
  $sql = "SELECT * FROM exm_list WHERE exid='$exid'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $ogtime = $row['extime'];
  $subt = $row['subt'];
  $cmtime = date("Y-m-d H:i:s");

  $letters = array('-', ' ', ':');
  $ogtime = str_replace($letters, '', $ogtime);
  $cmtime = str_replace($letters, '', $cmtime);
  if ($ogtime > $cmtime) {
    header("Location: exams.php");
  }
  if ($cmtime > $subt) {
    echo "<script>st();</script>";
  }
}



$sql = "SELECT qid, qstn, qstn_o1, qstn_o2, qstn_o3, qstn_o4 FROM qstn_list WHERE exid='$exid'";
$result = mysqli_query($conn, $sql);

$details = "SELECT * FROM exm_list WHERE exid='$exid'";
$res = mysqli_query($conn, $details);
while ($rowd = mysqli_fetch_array($res)) {
  $nq = $rowd['nq'];
  $exname = $rowd['exname'];
  $desp = $rowd['desp'];
}


?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <title>Exams</title>
  <link rel="stylesheet" href="css/dash.css">
  <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Styles for question navigation */
    .navigation-buttons {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 20px 0;
      width: 100%;
    }

    .nav-btn {
      padding: 8px 15px;
      background-color: #0A2558;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .nav-btn:hover {
      background-color: #0d3a80;
    }

    .nav-btn:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
    }

    #question-number {
      font-size: 16px;
      font-weight: bold;
    }

    .question {
      min-height: 250px;
      padding: 10px;
      border-radius: 5px;
    }
  </style>
  <?php
  $td = $subt;
  ?>
  <script type="text/javascript">
    function st() {
      document.getElementById("form1").submit();
    }
    //set the date we are counting down to 
    var count_id = "<?php echo $td; ?>";
    var countDownDate = new Date(count_id).getTime();
    //Update the count down every 1 second 
    var x = setInterval(function() {
      //Get today's date and time 
      var now = new Date().getTime();
      //Fir thn dist2nr 'nnn now and the count down date 
      var distance = countDownDate - now;
      //Ti ilculations fr k-rt—s,minutes and seconds 
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);
      document.getElementById("time").innerHTML = "Timer: " + hours + "h " + minutes + "m " + seconds + "s";
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("form1").submit();
      }
    }, 1000);
  </script>

  <!-- Anti-cheat system: Phase 1 - Tab Switching Detection -->
  <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
      // Get exam ID from hidden field
      const examId = document.querySelector('input[name="exid"]').value;
      let warningShown = false;
      let examInitialized = false; // Add flag to track if exam is initialized
      let isFullScreen = false;
      let currentIntegrityScore = 100; // Initial integrity score
      let currentIntegrityCategory = 'Good'; // Initial category

      // Create integrity score display container
      const integrityContainer = document.createElement('div');
      integrityContainer.id = 'integrity-score-display';
      integrityContainer.style.cssText = `
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        z-index: 1000;
        border-left: 4px solid #28a745;
      `;
      integrityContainer.innerHTML = `
        <div style="font-weight: bold; margin-bottom: 5px;">Integrity Monitor</div>
        <div>Score: <span id="integrity-score-value" style="font-weight: bold; color: #28a745;">100/100</span></div>
        <div>Status: <span id="integrity-category" style="font-weight: bold; color: #28a745;">Good</span></div>
      `;
      document.body.appendChild(integrityContainer);

      // Function to update integrity score display
      function updateIntegrityDisplay(score, category) {
        currentIntegrityScore = score;
        currentIntegrityCategory = category;

        const scoreElement = document.getElementById('integrity-score-value');
        const categoryElement = document.getElementById('integrity-category');

        scoreElement.textContent = `${score}/100`;
        categoryElement.textContent = category;

        // Update colors based on category
        let categoryColor, borderColor;
        if (category === 'Good') {
          categoryColor = '#28a745'; // green
          borderColor = '#28a745';
        } else if (category === 'At-Risk') {
          categoryColor = '#ffc107'; // yellow
          borderColor = '#ffc107';
        } else {
          categoryColor = '#dc3545'; // red
          borderColor = '#dc3545';
        }

        scoreElement.style.color = categoryColor;
        categoryElement.style.color = categoryColor;
        integrityContainer.style.borderLeft = `4px solid ${borderColor}`;
      }

      // Initialize anti-cheat warning container
      const warningContainer = document.createElement('div');
      warningContainer.id = 'anti-cheat-warning';
      warningContainer.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        z-index: 1000;
        max-width: 300px;
        display: none;
        font-weight: bold;
      `;
      document.body.appendChild(warningContainer);

      // Create full screen button and container
      const fullScreenContainer = document.createElement('div');
      fullScreenContainer.id = 'fullscreen-prompt';
      fullScreenContainer.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
      `;

      const fullScreenMessage = document.createElement('div');
      fullScreenMessage.style.cssText = `
        color: white;
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
        max-width: 600px;
      `;
      fullScreenMessage.innerHTML = '<strong>⚠️ EXAM SECURITY NOTICE</strong><br><br>This exam requires full screen mode to maintain integrity.<br>Please click the button below to enter full screen mode.';

      const fullScreenButton = document.createElement('button');
      fullScreenButton.textContent = 'Enter Full Screen Mode';
      fullScreenButton.style.cssText = `
        background-color: #0A2558;
        color: white;
        border: none;
        padding: 12px 24px;
        font-size: 18px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
      `;

      fullScreenContainer.appendChild(fullScreenMessage);
      fullScreenContainer.appendChild(fullScreenButton);
      document.body.appendChild(fullScreenContainer);

      // Function to request full screen
      function requestFullScreen() {
        const element = document.documentElement;

        if (element.requestFullscreen) {
          element.requestFullscreen();
        } else if (element.mozRequestFullScreen) { // Firefox
          element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) { // Chrome, Safari and Opera
          element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) { // IE/Edge
          element.msRequestFullscreen();
        }

        isFullScreen = true;
        fullScreenContainer.style.display = 'none';
        showWarning('Full screen mode activated. Do not exit full screen during the exam.');
      }

      // Function to exit full screen
      function exitFullScreen() {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { // Firefox
          document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { // IE/Edge
          document.msExitFullscreen();
        }

        isFullScreen = false;
      }

      // Event listener for full screen button
      fullScreenButton.addEventListener('click', requestFullScreen);

      // Function to check if browser is in full screen mode
      function isInFullScreen() {
        return (
          document.fullscreenElement ||
          document.webkitFullscreenElement ||
          document.mozFullScreenElement ||
          document.msFullscreenElement
        );
      }

      // Listen for fullscreen change events
      document.addEventListener('fullscreenchange', handleFullScreenChange);
      document.addEventListener('webkitfullscreenchange', handleFullScreenChange);
      document.addEventListener('mozfullscreenchange', handleFullScreenChange);
      document.addEventListener('MSFullscreenChange', handleFullScreenChange);

      function handleFullScreenChange() {
        if (examInitialized && !isInFullScreen()) {
          showWarning('⚠️ WARNING: You exited full screen mode! This may be flagged as suspicious behavior.');
          fullScreenContainer.style.display = 'flex';
        } else if (isInFullScreen()) {
          fullScreenContainer.style.display = 'none';
        }
      }

      // Function to show warning message
      function showWarning(message) {
        warningContainer.innerHTML = message;
        warningContainer.style.display = 'block';
        warningShown = true;

        // Hide warning after 5 seconds
        setTimeout(() => {
          warningContainer.style.display = 'none';
          warningShown = false;
        }, 5000);
      }

      // Phase 3: Variables to track combined violations
      let lastViolationTime = 0;
      let lastViolationType = null;

      // Track visibility change events
      document.addEventListener('visibilitychange', function() {
        if (examInitialized && document.visibilityState === 'hidden') {
          // User switched tabs or minimized window
          checkCombinedViolation('tab_switch');
          logViolation('tab_switch');
        }
      });

      // Phase 2: Track window blur/focus events
      let blurTime = null;
      let isBlurred = false;

      window.addEventListener('blur', function() {
        if (examInitialized && !isBlurred) {
          isBlurred = true;
          blurTime = new Date().getTime();
          // Log window blur violation
          checkCombinedViolation('window_blur');
          logViolation('window_blur');
        }
      });

      window.addEventListener('focus', function() {
        if (examInitialized && isBlurred) {
          isBlurred = false;
          // Calculate the time spent outside the window
          const focusTime = new Date().getTime();
          const timeSpentOutside = focusTime - blurTime;

          // If time spent outside is very short (less than 1 second),
          // it could be a false positive, so we don't need to do anything

          // If time spent outside is significant (more than 5 seconds),
          // it could be considered as an app switching attempt (will be part of Phase 3)
          if (timeSpentOutside > 5000) {
            // This will be used in Phase 3 for app switching detection
            console.log('Potential app switching detected:', timeSpentOutside / 1000, 'seconds');
          }
        }
      });

      // Function to check for combined violations (Phase 3)
      function checkCombinedViolation(currentViolationType) {
        const now = new Date().getTime();

        // If we had a different violation type within the last 2 seconds
        if (lastViolationType &&
          lastViolationType !== currentViolationType &&
          now - lastViolationTime < 2000) {

          // Log a combined violation
          logViolation('combined');
          console.log('Combined violation detected: ' + lastViolationType + ' and ' + currentViolationType + ' within 2 seconds');
        }

        // Update the last violation info
        lastViolationTime = now;
        lastViolationType = currentViolationType;
      }

      // Function to log violations - updated to handle different violation types
      function logViolation(violationType) {
        fetch('log_violation.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              exam_id: examId,
              violation_type: violationType
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              // Update the integrity score display
              updateIntegrityDisplay(data.integrity_score, data.integrity_category);

              // Force submit if integrity score falls below threshold
              if (data.integrity_score < 20) {
                showWarning(`
                  <strong>⚠️ CRITICAL INTEGRITY VIOLATION!</strong><br>
                  Your integrity score has fallen below the minimum threshold.<br>
                  Your exam will be automatically submitted in 5 seconds.
                `);

                setTimeout(() => {
                  alert("Your exam is being submitted due to critical integrity violations. Your final integrity score is: " + data.integrity_score);
                  document.getElementById("form1").submit();
                }, 5000);

                return; // Skip showing regular violation warning
              }

              let violationTypeText = '';
              switch (violationType) {
                case 'tab_switch':
                  violationTypeText = 'Tab switching';
                  break;
                case 'window_blur':
                  violationTypeText = 'Window focus loss';
                  break;
                case 'combined':
                  violationTypeText = 'Combined violations';
                  break;
                default:
                  violationTypeText = violationType;
              }

              let warningMessage = `
              <strong>⚠️ WARNING: ${violationTypeText} detected!</strong><br>
              Attempt #${data.occurrence}<br>
              Penalty: -${data.penalty} points<br>
              Current integrity score: ${data.integrity_score} (${data.integrity_category})
            `;
              showWarning(warningMessage);

              // If integrity score is in the "Cheating Suspicion" range, notify
              if (data.integrity_category === 'Cheating Suspicion' && !warningShown) {
                alert('WARNING: Your integrity score is critically low due to suspected cheating behavior. Your exam may be disqualified for review.');
              }

              // For combined violations, notify of mandatory review
              if (violationType === 'combined') {
                console.warn('NOTICE: Combined violations trigger mandatory manual review by instructors');
              }
            }
          })
          .catch(error => {
            console.error('Error logging violation:', error);
          });
      }

      // Display initial anti-cheat notification and show full screen prompt
      alert('⚠️ ANTI-CHEAT SYSTEM ACTIVATED: Tab switching, window focus loss, and other suspicious activities will be detected and penalized. Combined violations will trigger mandatory review. Your integrity score starts at 100 points.');

      // Set a small delay before enabling the violation tracking
      setTimeout(() => {
        examInitialized = true;
      }, 2000); // 2 second delay to allow page to fully load and settle
    });
  </script>

</head>

<body>
  <div class="sidebar active">
    <div class="logo-details">
      <i class='bx bx-diamond'></i>
      <span class="logo_name">Welcome</span>
    </div>
    <ul class="nav-links">
      <li>
        <a>
          <i class='bx bx-grid-alt'></i>
          <span class="links_name">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="exams.php" class="active">
          <i class='bx bx-book-content'></i>
          <span class="links_name">Exams</span>
        </a>
      </li>
      <li>
        <a href="mock_exams.php">
          <i class='bx bx-edit'></i>
          <span class="links_name">Mock Exams</span>
        </a>
      </li>
      <li>
        <a href="results.php">
          <i class='bx bxs-bar-chart-alt-2'></i>
          <span class="links_name">Results</span>
        </a>
      </li>
      <li>
        <a>
          <i class='bx bx-message'></i>
          <span class="links_name">Messages</span>
        </a>
      </li>
      <li>
        <a>
          <i class='bx bx-cog'></i>
          <span class="links_name">Settings</span>
        </a>
      </li>
      <li>
        <a>
          <i class='bx bx-help-circle'></i>
          <span class="links_name">Help</span>
        </a>
      </li>
      <li class="log_out">
        <a>
          <i class='bx bx-log-out-circle'></i>
          <span class="links_name">Log out</span>
        </a>
      </li>
    </ul>
  </div>
  <section class="home-section">
    <nav>
      <div class="sidebar-button">
        <i class='bx bx-menu-alt-right sidebarBtn'></i>
        <span class="dashboard">Student Dashboard</span>
      </div>
    </nav>

    <div class="home-content">
      <div class="stat-boxes">
        <div class="recent-stat box">
          <div>
            <h3>Exam name: <?php echo $exname ?><?php echo '
          <p id="time"style="float:right"></p>'; ?></h3>
          </div>
          <span style="font-size: 17px;">Description: <?php echo $desp ?></span>
          <br><br><br>
          <form action="submit.php" id="form1" method="post">
            <div class="radio-container">
              <?php

              if (mysqli_num_rows($result) > 0) {
                $i = 1;
                $questions = array();
                while ($row = mysqli_fetch_assoc($result)) {
                  $questions[] = $row;
                  echo '<input type="hidden" name="qid' . $i . '" value="' . $row['qid'] . '">';
                  $i++;
                }
                $totalQuestions = count($questions);
              ?>

                <div id="question-container">
                  <!-- Questions will be displayed here by JavaScript -->
                </div>

                <div class="navigation-buttons">
                  <button type="button" id="prev-btn" class="nav-btn" disabled>Previous</button>
                  <span id="question-number">Question 1 of <?php echo $totalQuestions; ?></span>
                  <button type="button" id="next-btn" class="nav-btn">Next</button>
                </div>

              <?php
              }
              ?>
            </div>
            <input type="hidden" name="exid" value="<?php echo $exid ?>">
            <input type="hidden" name="nq" value="<?php echo $nq ?>">
            <button type="reset" id="reset-btn" class="rbtn">Reset current</button>
            <br><br>
            <input type="submit" name="ans_sub" value="Submit" class="btn" />
            <!-- <button type="submit" name="ans_sub" class="btn">Submit</button>     -->
          </form>
        </div>
      </div>
    </div>
  </section>

  <script>
    // Store questions data from PHP to JavaScript
    const questions = <?php echo json_encode($questions); ?>;
    const totalQuestions = <?php echo $totalQuestions; ?>;
    let currentQuestion = 0;

    // DOM elements
    const questionContainer = document.getElementById('question-container');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const questionNumber = document.getElementById('question-number');
    const resetBtn = document.getElementById('reset-btn');

    // Function to display current question
    function displayQuestion() {
      const q = questions[currentQuestion];
      const qNum = currentQuestion + 1;

      // Update question number display
      questionNumber.textContent = `Question ${qNum} of ${totalQuestions}`;

      // Enable/disable navigation buttons as needed
      prevBtn.disabled = currentQuestion === 0;
      nextBtn.disabled = currentQuestion === totalQuestions - 1;
      nextBtn.textContent = currentQuestion === totalQuestions - 1 ? "Review" : "Next";

      // Generate question HTML
      let questionHTML = `
        <div class="question" data-index="${qNum}">
          <span><b>Q${qNum}. ${q.qstn}</b></span><br><br>
          
          <input type="radio" id="o1${qNum}" name="o${qNum}" value="${q.qstn_o1}" />
          <label class="lbl" for="o1${qNum}">${q.qstn_o1}</label><br>
          
          <input type="radio" id="o2${qNum}" name="o${qNum}" value="${q.qstn_o2}" />
          <label class="lbl" for="o2${qNum}">${q.qstn_o2}</label><br>
          
          <input type="radio" id="o3${qNum}" name="o${qNum}" value="${q.qstn_o3}" />
          <label class="lbl" for="o3${qNum}">${q.qstn_o3}</label><br>
          
          <input type="radio" id="o4${qNum}" name="o${qNum}" value="${q.qstn_o4}" />
          <label class="lbl" for="o4${qNum}">${q.qstn_o4}</label><br>
        </div>
      `;

      questionContainer.innerHTML = questionHTML;

      // Restore any previously selected answer
      const savedAnswer = localStorage.getItem(`exam_${exid}_q${qNum}`);
      if (savedAnswer) {
        const radioBtn = document.querySelector(`input[name="o${qNum}"][value="${savedAnswer}"]`);
        if (radioBtn) radioBtn.checked = true;
      }

      // Add event listeners to save answers to localStorage
      const radioButtons = document.querySelectorAll(`input[name="o${qNum}"]`);
      radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
          localStorage.setItem(`exam_${exid}_q${qNum}`, this.value);
        });
      });
    }

    // Event listeners for navigation
    prevBtn.addEventListener('click', () => {
      if (currentQuestion > 0) {
        currentQuestion--;
        displayQuestion();
      }
    });

    nextBtn.addEventListener('click', () => {
      if (currentQuestion < totalQuestions - 1) {
        currentQuestion++;
        displayQuestion();
      } else {
        // If on last question, show a summary or allow submission
        alert("You've reached the last question. Review your answers before submitting.");
      }
    });

    // Reset button functionality
    resetBtn.addEventListener('click', function() {
      const qNum = currentQuestion + 1;
      const radioButtons = document.querySelectorAll(`input[name="o${qNum}"]`);
      radioButtons.forEach(radio => radio.checked = false);
      localStorage.removeItem(`exam_${exid}_q${qNum}`);
    });

    // Initialize - show first question
    const exid = <?php echo $exid; ?>;
    displayQuestion();

    // Handle form submission
    document.getElementById('form1').addEventListener('submit', function(e) {
      // Make sure all saved answers are applied to the form
      for (let i = 1; i <= totalQuestions; i++) {
        const savedAnswer = localStorage.getItem(`exam_${exid}_q${i}`);
        if (savedAnswer) {
          // Create a hidden input if not already answered in the visible form
          if (!document.querySelector(`input[name="o${i}"]:checked`)) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `o${i}`;
            hiddenInput.value = savedAnswer;
            this.appendChild(hiddenInput);
          }
        }
      }

      // Clear localStorage after submission
      for (let i = 1; i <= totalQuestions; i++) {
        localStorage.removeItem(`exam_${exid}_q${i}`);
      }
    });

    // Original timer code
    var inputs = document.querySelectorAll("input[type=radio]:checked"),
      x = inputs.length;

    // Function to submit the form when timer expires
    function st() {
      document.getElementById("form1").submit();
    }
  </script>
</body>

</html>