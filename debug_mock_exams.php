<?php
include('config.php');

// Display all mock exams
echo "<h2>All Mock Exams</h2>";
$sql = "SELECT * FROM mock_exm_list ORDER BY datetime DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Original Exam ID</th><th>Name</th><th>Status</th><th>Created</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['mock_exid'] . "</td>";
        echo "<td>" . $row['original_exid'] . "</td>";
        echo "<td>" . $row['exname'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['datetime'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No mock exams found.</p>";
}

// Display all mock questions
echo "<h2>Mock Exam Questions</h2>";
$sql = "SELECT * FROM mock_qstn_list";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Mock Exam ID</th><th>Question</th><th>Options</th><th>Correct Answer</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['mock_qid'] . "</td>";
        echo "<td>" . $row['mock_exid'] . "</td>";
        echo "<td>" . $row['qstn'] . "</td>";
        echo "<td>1. " . $row['qstn_o1'] . "<br>2. " . $row['qstn_o2'] . "<br>3. " . $row['qstn_o3'] . "<br>4. " . $row['qstn_o4'] . "</td>";
        echo "<td>" . $row['qstn_ans'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No mock exam questions found.</p>";
}

// Test the mock exam generation manually
if (isset($_POST['generate'])) {
    echo "<h2>Generating Mock Exam</h2>";

    $exid = $_POST['exid'];
    $exname = $_POST['exname'];
    $description = $_POST['description'];
    $subject = $_POST['subject'];

    // Include the generate mock exam function
    include_once('generate_mock_exam.php');

    // Call the function directly
    generateMockExams($exid, $exname, $description, $subject, $conn);

    echo "<p>Generation process initiated. Please check the results above after refreshing the page.</p>";
}

// Check PHP error log
echo "<h2>Recent PHP Errors</h2>";
if (file_exists(ini_get('error_log'))) {
    $log = file_get_contents(ini_get('error_log'));
    $lines = explode("\n", $log);
    $last_lines = array_slice($lines, -20); // Get last 20 lines

    echo "<pre>";
    foreach ($last_lines as $line) {
        if (strpos($line, 'mock') !== false || strpos($line, 'Mock') !== false || strpos($line, 'OpenAI') !== false) {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p>Error log file not found or not accessible.</p>";
}
?>

<h2>Manual Mock Exam Generation</h2>
<form method="post">
    <label for="exid">Exam ID:</label>
    <input type="number" name="exid" required><br><br>

    <label for="exname">Exam Name:</label>
    <input type="text" name="exname" required><br><br>

    <label for="description">Description:</label>
    <textarea name="description" rows="3" cols="50" required></textarea><br><br>

    <label for="subject">Subject:</label>
    <input type="text" name="subject" required><br><br>

    <input type="submit" name="generate" value="Generate Mock Exam">
</form>