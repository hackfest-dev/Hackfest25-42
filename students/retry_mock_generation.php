<?php
session_start();
if (!isset($_SESSION["uname"])) {
    header("Location: ../login_student.php");
}

include '../config.php';
error_reporting(0);

// If retry button is clicked
if (isset($_POST['retry'])) {
    // Get all pending mock exams
    $pending_sql = "SELECT me.*, e.exname, e.desp, e.subject FROM mock_exm_list me 
                   JOIN exm_list e ON me.original_exid = e.exid 
                   WHERE me.status = 'pending' OR me.status = 'error'";
    $pending_result = mysqli_query($conn, $pending_sql);

    $retry_count = 0;

    if (mysqli_num_rows($pending_result) > 0) {
        // Include our helper file
        require_once('../teachers/mock_exam_helper.php');

        while ($row = mysqli_fetch_assoc($pending_result)) {
            // Get original exam info
            $original_exid = $row['original_exid'];
            $exname = $row['exname'];
            $description = $row['desp'];
            $subject = $row['subject'];

            // Only pass to the function if we have all required information
            if (!empty($original_exid) && !empty($exname) && !empty($description) && !empty($subject)) {
                // Update the mock exam status to pending before retry
                $update_sql = "UPDATE mock_exm_list SET status = 'pending' WHERE original_exid = '$original_exid'";
                mysqli_query($conn, $update_sql);

                // Generate the mock exam
                generateMockExamsHelper($original_exid, $exname, $description, $subject);
                $retry_count++;
            }
        }
    }

    // Redirect back to mock exams page
    if ($retry_count > 0) {
        header("Location: mock_exams.php?retry=success&count=" . $retry_count);
    } else {
        header("Location: mock_exams.php?retry=none");
    }
} else {
    // If accessed directly without pressing retry button
    header("Location: mock_exams.php");
}
