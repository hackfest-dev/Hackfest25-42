<?php
include('config.php');

// Function to create mock exams with hard-coded questions
function createMockExams($original_exid, $exname, $description, $subject, $conn)
{
    // Get current date and time for exam scheduling
    $current_date = date('Y-m-d H:i:s');
    // Set submission time to 7 days from now
    $submission_time = date('Y-m-d H:i:s', strtotime('+7 days'));

    echo "<h2>Creating mock exams for: $exname (ID: $original_exid)</h2>";

    // Check if this exam already has mock exams
    $check_sql = "SELECT COUNT(*) as count FROM mock_exm_list WHERE original_exid = '$original_exid'";
    $check_result = mysqli_query($conn, $check_sql);
    $count = mysqli_fetch_assoc($check_result)['count'];

    if ($count > 0) {
        echo "<p>Mock exams already exist for this exam ($count found). Skipping creation.</p>";
        return;
    }

    // Insert two mock exam entries
    for ($i = 1; $i <= 2; $i++) {
        // Create a mock exam entry
        $mock_exam_name = "Mock Test $i: $exname";
        $mock_exam_desc = "Practice test $i for $exname. $description";

        $sql = "INSERT INTO mock_exm_list (original_exid, mock_number, exname, nq, desp, subt, extime, subject, status) 
                VALUES ('$original_exid', '$i', '$mock_exam_name', '5', '$mock_exam_desc', '$submission_time', '$current_date', '$subject', 'pending')";

        if (mysqli_query($conn, $sql)) {
            $mock_exid = mysqli_insert_id($conn);
            echo "<p>Created mock exam #$i with ID: $mock_exid</p>";

            // Sample questions for this mock exam
            $sample_questions = [
                [
                    'question' => "Sample question 1 for $exname about $subject",
                    'option1' => "Option A for question 1",
                    'option2' => "Option B for question 1",
                    'option3' => "Option C for question 1",
                    'option4' => "Option D for question 1",
                    'correct_answer' => "option1"
                ],
                [
                    'question' => "Sample question 2 for $exname about $subject",
                    'option1' => "Option A for question 2",
                    'option2' => "Option B for question 2",
                    'option3' => "Option C for question 2",
                    'option4' => "Option D for question 2",
                    'correct_answer' => "option2"
                ],
                [
                    'question' => "Sample question 3 for $exname about $subject",
                    'option1' => "Option A for question 3",
                    'option2' => "Option B for question 3",
                    'option3' => "Option C for question 3",
                    'option4' => "Option D for question 3",
                    'correct_answer' => "option3"
                ],
                [
                    'question' => "Sample question 4 for $exname about $subject",
                    'option1' => "Option A for question 4",
                    'option2' => "Option B for question 4",
                    'option3' => "Option C for question 4",
                    'option4' => "Option D for question 4",
                    'correct_answer' => "option4"
                ],
                [
                    'question' => "Sample question 5 for $exname about $subject",
                    'option1' => "Option A for question 5",
                    'option2' => "Option B for question 5",
                    'option3' => "Option C for question 5",
                    'option4' => "Option D for question 5",
                    'correct_answer' => "option1"
                ]
            ];

            // Insert the sample questions
            $success_count = 0;
            for ($j = 0; $j < count($sample_questions); $j++) {
                $question = $sample_questions[$j];

                $qstn = mysqli_real_escape_string($conn, $question['question']);
                $o1 = mysqli_real_escape_string($conn, $question['option1']);
                $o2 = mysqli_real_escape_string($conn, $question['option2']);
                $o3 = mysqli_real_escape_string($conn, $question['option3']);
                $o4 = mysqli_real_escape_string($conn, $question['option4']);

                // Determine correct answer
                $correct_answer = $question['correct_answer'];
                if ($correct_answer == 'option1') {
                    $ans = $o1;
                } elseif ($correct_answer == 'option2') {
                    $ans = $o2;
                } elseif ($correct_answer == 'option3') {
                    $ans = $o3;
                } else {
                    $ans = $o4;
                }

                $ans = mysqli_real_escape_string($conn, $ans);
                $sno = $j + 1;

                $insert_sql = "INSERT INTO mock_qstn_list (mock_exid, qstn, qstn_o1, qstn_o2, qstn_o3, qstn_o4, qstn_ans, sno) 
                            VALUES ('$mock_exid', '$qstn', '$o1', '$o2', '$o3', '$o4', '$ans', '$sno')";

                if (mysqli_query($conn, $insert_sql)) {
                    $success_count++;
                } else {
                    echo "<p>Error inserting question $sno: " . mysqli_error($conn) . "</p>";
                }
            }

            echo "<p>Inserted $success_count questions for mock exam ID $mock_exid</p>";

            // Update mock exam status to ready
            $update_sql = "UPDATE mock_exm_list SET status = 'ready' WHERE mock_exid = '$mock_exid'";
            if (mysqli_query($conn, $update_sql)) {
                echo "<p>Mock exam ID $mock_exid is now ready</p>";
            } else {
                echo "<p>Error updating mock exam status: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p>Error creating mock exam: " . mysqli_error($conn) . "</p>";
        }
    }
}

// If the script is accessed directly with a form submission
if (isset($_POST['generate'])) {
    $exid = $_POST['exid'];
    $exname = $_POST['exname'];
    $description = $_POST['description'];
    $subject = $_POST['subject'];

    createMockExams($exid, $exname, $description, $subject, $conn);
} else if (isset($_GET['exam_id'])) {
    // Get exam details from the database based on ID
    $exam_id = $_GET['exam_id'];
    $sql = "SELECT * FROM exm_list WHERE exid = '$exam_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $exname = $row['exname'];
        $description = $row['desp'];
        $subject = $row['subject'];

        createMockExams($exam_id, $exname, $description, $subject, $conn);
    } else {
        echo "<p>Error: Exam with ID $exam_id not found.</p>";
    }
} else {
    // Show a form to enter exam details
?>
    <h1>Create Mock Exams</h1>

    <h2>Select an Existing Exam</h2>
    <?php
    $sql = "SELECT * FROM exm_list ORDER BY exid DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Subject</th><th>Action</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['exid'] . "</td>";
            echo "<td>" . $row['exname'] . "</td>";
            echo "<td>" . $row['subject'] . "</td>";
            echo "<td><a href='?exam_id=" . $row['exid'] . "'>Create Mock Exams</a></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No exams found in the database.</p>";
    }
    ?>

    <h2>Or Enter Exam Details Manually</h2>
    <form method="post">
        <label for="exid">Exam ID:</label>
        <input type="number" name="exid" required><br><br>

        <label for="exname">Exam Name:</label>
        <input type="text" name="exname" required><br><br>

        <label for="description">Description:</label>
        <textarea name="description" rows="3" cols="50" required></textarea><br><br>

        <label for="subject">Subject:</label>
        <input type="text" name="subject" required><br><br>

        <input type="submit" name="generate" value="Generate Mock Exams">
    </form>
<?php
}

echo "<p><a href='debug_mock_exams.php'>View all mock exams</a></p>";
?>