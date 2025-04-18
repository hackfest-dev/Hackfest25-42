<?php
session_start();
if (!isset($_SESSION["uname"])) {
    header("Location: ../login_student.php");
}

include '../config.php';
error_reporting(0);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mock_exid = $_POST['mock_exid'];
    $nq = $_POST['nq'];
    $uname = $_SESSION['uname'];
    $integrity_score = isset($_POST['integrity_score']) ? intval($_POST['integrity_score']) : 100;

    // Count correct answers
    $cnq = 0;

    // Fetch correct answers from database
    $sql = "SELECT sno, qstn_ans FROM mock_qstn_list WHERE mock_exid='$mock_exid' ORDER BY sno";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sno = $row['sno'];
            $correct_ans = $row['qstn_ans'];

            // Check if the user's answer is correct
            if (isset($_POST['a' . $sno]) && $_POST['a' . $sno] == $correct_ans) {
                $cnq++;
            }
        }
    }

    // Calculate percentage
    $ptg = ($cnq / $nq) * 100;

    // Insert result into mock_atmpt_list
    $sql = "INSERT INTO mock_atmpt_list (mock_exid, uname, nq, cnq, ptg, status, integrity_score) 
            VALUES ('$mock_exid', '$uname', '$nq', '$cnq', '$ptg', '1', '$integrity_score')";

    if (mysqli_query($conn, $sql)) {
        // Redirect to results page
        header("Location: mock_exams.php?submitted=1");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    // If not POST request, redirect to mock exams page
    header("Location: mock_exams.php");
}
