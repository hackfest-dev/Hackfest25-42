<?php
session_start();
if (!isset($_POST["exid"])) {
    header("Location: dash.php");
}

include '../config.php';
$j = 0;
if (isset($_POST["exid"])) {
    $nq = mysqli_real_escape_string($conn, $_POST["nq"]);
    $exid = mysqli_real_escape_string($conn, $_POST["exid"]);
    $uname = mysqli_real_escape_string($conn, $_SESSION["uname"]);


    for ($i = 1; $i <= $nq; $i++) {
        $qid = mysqli_real_escape_string($conn, $_POST['qid' . $i]);
        $op = mysqli_real_escape_string($conn, $_POST['o' . $i]);
        $sql = "SELECT * FROM qstn_list WHERE exid='$exid' AND qid='$qid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $ans = $row['qstn_ans'];
            if ($ans == $op) {
                $j = $j + 1;
                $result = NULL;
            }
        }
    }
    $ptg = ($j / $nq) * 100;
    $st = 1;

    // Calculate integrity score
    $sql = "SELECT SUM(penalty) as total_penalty FROM cheat_violations 
            WHERE student_username = ? AND exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $uname, $exid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_penalty = $row['total_penalty'] ?? 0;

    // Calculate integrity score (starting from 100)
    $integrity_score = max(0, 100 - $total_penalty);

    // Determine the integrity category
    $integrity_category = '';
    if ($integrity_score >= 90) {
        $integrity_category = 'Excellent';
    } else if ($integrity_score >= 80) {
        $integrity_category = 'Good';
    } else if ($integrity_score >= 70) {
        $integrity_category = 'Fair';
    } else if ($integrity_score >= 60) {
        $integrity_category = 'Poor';
    } else {
        $integrity_category = 'Very Poor';
    }

    // First, alter the table to add the new columns if they don't exist yet
    $conn->query("ALTER TABLE atmpt_list ADD COLUMN IF NOT EXISTS integrity_score INT DEFAULT 100");
    $conn->query("ALTER TABLE atmpt_list ADD COLUMN IF NOT EXISTS integrity_category VARCHAR(50) DEFAULT 'Good'");

    // Now insert with the integrity data
    $sql = "INSERT INTO atmpt_list (exid, uname, nq, cnq, ptg, status, integrity_score, integrity_category) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiiiiss", $exid, $uname, $nq, $j, $ptg, $st, $integrity_score, $integrity_category);
    $stmt->execute();

    // Get the newly created attempt ID
    $attempt_id = $conn->insert_id;

    // Redirect to certificate generation page with the attempt ID
    header("Location: generate_certificate.php?id=$attempt_id&auto_mint=1");
    exit();
}
