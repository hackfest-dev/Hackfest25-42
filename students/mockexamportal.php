<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
if (!isset($_SESSION["uname"])) {
    header("Location: ../login_student.php");
}

include '../config.php';
error_reporting(0);
$mock_exid = $_POST['mock_exid'];

if (!isset($_POST["edit_btn"])) {
    header("Location: mock_exams.php");
}

if (isset($_POST["edit_btn"])) {
    $sql = "SELECT * FROM mock_exm_list WHERE mock_exid='$mock_exid'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $ogtime = $row['extime'];
    $subt = $row['subt'];
    $cmtime = date("Y-m-d H:i:s");

    $letters = array('-', ' ', ':');
    $ogtime = str_replace($letters, '', $ogtime);
    $cmtime = str_replace($letters, '', $cmtime);
    if ($cmtime > $subt) {
        echo "<script>st();</script>";
    }
}

$sql = "SELECT mock_qid, qstn, qstn_o1, qstn_o2, qstn_o3, qstn_o4 FROM mock_qstn_list WHERE mock_exid='$mock_exid'";
$result = mysqli_query($conn, $sql);

$details = "SELECT * FROM mock_exm_list WHERE mock_exid='$mock_exid'";
$res = mysqli_query($conn, $details);
while ($rowd = mysqli_fetch_array($res)) {
    $nq = $rowd['nq'];
    $exname = $rowd['exname'];
    $desp = $rowd['desp'];
    
    // Set a fixed duration of 10 minutes for all mock tests
    $duration = 10;
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Mock Exam</title>
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

        /* Enhanced option styling */
        .options-container {
            margin-top: 20px;
        }

        .option-item {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .option-item:hover {
            background-color: #f5f5f5;
            border-color: #ccc;
        }

        .option-item input[type="radio"] {
            margin-right: 10px;
            transform: scale(1.2);
            cursor: pointer;
        }

        .option-item label {
            cursor: pointer;
            font-size: 15px;
            flex: 1;
        }

        /* Selected option style */
        .option-item.selected {
            background-color: #e6f7ff;
            border-color: #1890ff;
        }

        /* Integrity Score Display */
        #integrity-score-display {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            z-index: 1000;
            border-left: 4px solid #28a745;
        }
    </style>
    <?php
    $td = $subt;
    ?>
    <script type="text/javascript">
        function st() {
            document.getElementById("form1").submit();
        }
        // Set up timer for mock exam
        var testDuration = <?php echo $duration; ?> * 60 * 1000; // Convert minutes to milliseconds
        var startTime = new Date().getTime();
        var endTime = startTime + testDuration;
        
        // Update the countdown every 1 second
        var x = setInterval(function() {
          // Get current time
          var now = new Date().getTime();
          // Calculate remaining time
          var distance = endTime - now;
          
          // Time calculations for hours, minutes and seconds
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);
          
          // Display the timer
          document.getElementById("time").innerHTML = "Timer: " + hours + "h " + minutes + "m " + seconds + "s";
          
          // If timer expires, submit the form
          if (distance < 0) {
            clearInterval(x);
            document.getElementById("form1").submit();
          }
        }, 1000);
    </script>

    <!-- Anti-cheat system: Tab Switching Detection -->
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Get exam ID from hidden field
            const examId = document.querySelector('input[name="mock_exid"]').value;
            let warningShown = false;
            let examInitialized = false;
            let isFullScreen = false;
            let currentIntegrityScore = 100; // Initial integrity score
            let currentIntegrityCategory = 'Good'; // Initial category
            let tabSwitchCount = 0;
            let focusLossTimeTotal = 0;
            let lastFocusLossTime = null;

            // Create integrity score display container
            const integrityContainer = document.createElement('div');
            integrityContainer.id = 'integrity-score-display';
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

                // Add hidden field for integrity score
                let integrityField = document.getElementById('integrity_score');
                if (!integrityField) {
                    integrityField = document.createElement('input');
                    integrityField.type = 'hidden';
                    integrityField.name = 'integrity_score';
                    integrityField.id = 'integrity_score';
                    document.getElementById('form1').appendChild(integrityField);
                }
                integrityField.value = score;
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
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 1000;
        display: none;
        max-width: 300px;
        font-family: Arial, sans-serif;
      `;
            warningContainer.innerHTML = '<p style="margin: 0; font-weight: bold;">Warning: Potential academic integrity violation detected.</p><p style="margin: 5px 0 0 0; font-size: 14px;">Leaving this tab or using other applications is not allowed during the exam.</p>';
            document.body.appendChild(warningContainer);

            // Function to show warning
            function showWarning() {
                if (!warningShown) {
                    warningContainer.style.display = 'block';
                    warningShown = true;

                    // Reduce integrity score
                    updateIntegrityScore(-10);

                    setTimeout(function() {
                        warningContainer.style.display = 'none';
                        warningShown = false;
                    }, 5000);
                }
            }

            // Function to update integrity score
            function updateIntegrityScore(change) {
                let newScore = Math.max(0, Math.min(100, currentIntegrityScore + change));
                let category = 'Good';

                if (newScore < 70) {
                    category = 'Poor';
                } else if (newScore < 90) {
                    category = 'At-Risk';
                }

                updateIntegrityDisplay(newScore, category);
            }

            // Tab visibility change detection
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden') {
                    // Tab lost focus
                    lastFocusLossTime = new Date().getTime();
                    showWarning();
                    tabSwitchCount++;
                } else if (document.visibilityState === 'visible' && lastFocusLossTime) {
                    // Tab regained focus
                    const focusReturnTime = new Date().getTime();
                    const timeAway = focusReturnTime - lastFocusLossTime;
                    focusLossTimeTotal += timeAway;

                    // Additional penalty for being away for too long
                    if (timeAway > 5000) { // More than 5 seconds
                        updateIntegrityScore(-5);
                    }
                }
            });

            // Window blur/focus events as backup
            window.addEventListener('blur', function() {
                if (document.visibilityState !== 'hidden') {
                    // Window lost focus but tab is still visible
                    lastFocusLossTime = new Date().getTime();
                    showWarning();
                    tabSwitchCount++;
                }
            });

            window.addEventListener('focus', function() {
                if (lastFocusLossTime && document.visibilityState !== 'hidden') {
                    // Window regained focus
                    const focusReturnTime = new Date().getTime();
                    const timeAway = focusReturnTime - lastFocusLossTime;
                    focusLossTimeTotal += timeAway;

                    if (timeAway > 5000) {
                        updateIntegrityScore(-5);
                    }
                }
            });

            // Submit event listener to send integrity data
            document.getElementById('form1').addEventListener('submit', function() {
                // Calculate final integrity score based on various factors
                let finalScore = currentIntegrityScore;

                // Adjust for tab switch count
                if (tabSwitchCount > 0) {
                    finalScore -= Math.min(30, tabSwitchCount * 5);
                }

                // Adjust for total time away
                if (focusLossTimeTotal > 10000) { // More than 10 seconds
                    finalScore -= Math.min(20, Math.floor(focusLossTimeTotal / 5000) * 5);
                }

                // Ensure score doesn't go below 0
                finalScore = Math.max(0, finalScore);

                // Update the integrity score field
                document.getElementById('integrity_score').value = finalScore;
            });
        });
    </script>
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
                <span class="dashboard">Mock Exam Portal</span>
            </div>
            <div class="profile-details">
                <img src="<?php echo $_SESSION['img']; ?>" alt="pro">
                <span class="admin_name"><?php echo $_SESSION['fname']; ?></span>
            </div>
        </nav>

        <div class="home-content">
            <div class="stat-boxes">
                <div class="recent-stat box" style="width:100%">
                    <div class="title" style="text-align:center;">
                        <h2><?php echo $exname; ?></h2>
                        <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                            <p style="font-size: 15px;"><?php echo "Subject: " . $desp; ?></p>
                            <p id="time" style="font-size: 15px; color: #0A2558; font-weight: bold;"></p>
                        </div>
                    </div>
                    <div class="stat-details">
                        <?php
                        $uname = $_SESSION['uname'];
                        $i = 1;
                        $questions = array();

                        while ($row = mysqli_fetch_assoc($result)) {
                            $questions[] = array(
                                'qid' => $row['mock_qid'],
                                'qstn' => $row['qstn'],
                                'o1' => $row['qstn_o1'],
                                'o2' => $row['qstn_o2'],
                                'o3' => $row['qstn_o3'],
                                'o4' => $row['qstn_o4']
                            );
                        }
                        ?>
                        <form id="form1" method="post" action="submit_mock.php">
                            <input type="hidden" name="mock_exid" value="<?php echo $mock_exid; ?>">
                            <input type="hidden" name="nq" value="<?php echo $nq; ?>">

                            <div id="questions-container">
                                <?php
                                for ($i = 0; $i < count($questions); $i++) {
                                    $q = $questions[$i];
                                    $display = ($i === 0) ? 'block' : 'none';
                                    echo '
                    <div id="question-' . ($i + 1) . '" class="question" style="display: ' . $display . ';">
                      <h3>Question ' . ($i + 1) . '</h3>
                      <p style="font-size: 16px; margin-bottom: 20px;">' . $q['qstn'] . '</p>
                      
                      <div class="options-container" style="margin-top: 20px;">
                        <div class="option-item" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.2s ease;">
                          <input type="radio" id="q' . ($i + 1) . 'o1" name="a' . ($i + 1) . '" value="' . $q['o1'] . '" style="margin-right: 10px; transform: scale(1.2);">
                          <label for="q' . ($i + 1) . 'o1" style="cursor: pointer; font-size: 15px;">' . $q['o1'] . '</label>
                        </div>
                        
                        <div class="option-item" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.2s ease;">
                          <input type="radio" id="q' . ($i + 1) . 'o2" name="a' . ($i + 1) . '" value="' . $q['o2'] . '" style="margin-right: 10px; transform: scale(1.2);">
                          <label for="q' . ($i + 1) . 'o2" style="cursor: pointer; font-size: 15px;">' . $q['o2'] . '</label>
                        </div>
                        
                        <div class="option-item" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.2s ease;">
                          <input type="radio" id="q' . ($i + 1) . 'o3" name="a' . ($i + 1) . '" value="' . $q['o3'] . '" style="margin-right: 10px; transform: scale(1.2);">
                          <label for="q' . ($i + 1) . 'o3" style="cursor: pointer; font-size: 15px;">' . $q['o3'] . '</label>
                        </div>
                        
                        <div class="option-item" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.2s ease;">
                          <input type="radio" id="q' . ($i + 1) . 'o4" name="a' . ($i + 1) . '" value="' . $q['o4'] . '" style="margin-right: 10px; transform: scale(1.2);">
                          <label for="q' . ($i + 1) . 'o4" style="cursor: pointer; font-size: 15px;">' . $q['o4'] . '</label>
                        </div>
                      </div>
                    </div>
                  ';
                                }
                                ?>
                            </div>

                            <div class="navigation-buttons">
                                <button type="button" id="prev-btn" class="nav-btn" disabled>Previous</button>
                                <span id="question-number">Question 1 of <?php echo count($questions); ?></span>
                                <button type="button" id="next-btn" class="nav-btn">Next</button>
                            </div>

                            <div style="margin-top: 20px; text-align: center;">
                                <button type="submit" style="padding: 10px 20px; background-color: #0A2558; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">Submit Exam</button>
                                <button type="reset" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-left: 10px;">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalQuestions = <?php echo count($questions); ?>;
            let currentQuestion = 1;

            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const questionNumberSpan = document.getElementById('question-number');

            // Navigation functions
            function showQuestion(questionNumber) {
                // Hide all questions
                for (let i = 1; i <= totalQuestions; i++) {
                    document.getElementById('question-' + i).style.display = 'none';
                }

                // Show the current question
                document.getElementById('question-' + questionNumber).style.display = 'block';

                // Update question number text
                questionNumberSpan.textContent = 'Question ' + questionNumber + ' of ' + totalQuestions;

                // Update button states
                prevBtn.disabled = (questionNumber === 1);
                nextBtn.disabled = (questionNumber === totalQuestions);
            }

            // Button event listeners
            prevBtn.addEventListener('click', function() {
                if (currentQuestion > 1) {
                    currentQuestion--;
                    showQuestion(currentQuestion);
                }
            });

            nextBtn.addEventListener('click', function() {
                if (currentQuestion < totalQuestions) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                }
            });

            // Enhance option selection
            const optionItems = document.querySelectorAll('.option-item');
            optionItems.forEach(function(item) {
                // Add click handler to the entire option div
                item.addEventListener('click', function() {
                    // Find the radio button inside this option item
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;

                        // Remove highlight from all options in this question
                        const questionContainer = this.closest('.question');
                        const allOptionsInQuestion = questionContainer.querySelectorAll('.option-item');
                        allOptionsInQuestion.forEach(function(option) {
                            option.classList.remove('selected');
                        });

                        // Highlight the selected option
                        this.classList.add('selected');
                    }
                });
            });

            // Check if any options were previously selected (e.g., after page refresh)
            const allRadios = document.querySelectorAll('input[type="radio"]');
            allRadios.forEach(function(radio) {
                if (radio.checked) {
                    const parentOption = radio.closest('.option-item');
                    if (parentOption) {
                        parentOption.classList.add('selected');
                    }
                }
            });
        });
    </script>

    <script src="../js/script.js"></script>
</body>

</html>