<?php
session_start();
require_once 'functions.php';

if (!checkUserSessionIsActive()) {
    header("Location: login.php");
    exit;
}

// Get student ID and subject code from the query parameters
$student_id = isset($_GET['id']) ? $_GET['id'] : '';
$subject_code = isset($_GET['subject_code']) ? $_GET['subject_code'] : '';

// Check if the student ID and subject code are valid
$student = null;
if ($student_id && isset($_SESSION['students'])) {
    foreach ($_SESSION['students'] as $s) {
        if ($s['id'] == $student_id) {
            $student = $s;
            break;
        }
    }
}

// If the student is found and the subject is attached to the student, proceed
if ($student && in_array($subject_code, $student['subjects'])) {
    // Process the detachment if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['detach'])) {
        // Remove the subject from the student's subjects
        $index = array_search($subject_code, $student['subjects']);
        if ($index !== false) {
            unset($_SESSION['students'][$index]['subjects'][$index]);
            $_SESSION['students'][$index]['subjects'] = array_values($_SESSION['students'][$index]['subjects']); // Reindex the array
            $_SESSION['success_message'] = "Subject detached successfully!";
            header("Location: dashboard.php"); // Redirect to dashboard or another page
            exit;
        }
    }
} else {
    $_SESSION['error_message'] = "Student or subject not found!";
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detach Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detach Subject</li>
            </ol>
        </nav>

        <h2 class="mb-4 text-center">Confirm Detach Subject</h2>

        <!-- Display error or success message -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <!-- Detach Subject Confirmation Form -->
        <div class="mb-4">
            <h3>Are you sure you want to detach the subject "<?php echo htmlspecialchars($subject_code); ?>" from this student?</h3>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['id']); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></p>

            <form method="POST" action="detach_subject.php?id=<?php echo urlencode($student['id']); ?>&subject_code=<?php echo urlencode($subject_code); ?>">
                <button type="submit" name="detach" class="btn btn-danger">Detach Subject</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
