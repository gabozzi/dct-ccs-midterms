<?php
session_start();
require_once 'functions.php';

if (!checkUserSessionIsActive()) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success_message = "";
$student_id = isset($_GET['id']) ? $_GET['id'] : null;
$student_to_delete = null;

// If the student ID is not provided, redirect to the student page
if (!$student_id) {
    header("Location: register_student.php");
    exit;
}

// Fetch the student details from session
foreach ($_SESSION['students'] as $student) {
    if ($student['id'] == $student_id) {
        $student_to_delete = $student;
        break;
    }
}

// If student not found, redirect to the student registration page
if (!$student_to_delete) {
    header("Location: register_student.php");
    exit;
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    // Loop through the students and remove the one that matches the student ID
    foreach ($_SESSION['students'] as $key => $student) {
        if ($student['id'] == $student_id) {
            unset($_SESSION['students'][$key]); // Remove the student from the session
            $_SESSION['students'] = array_values($_SESSION['students']); // Reindex the array
            $success_message = "Student deleted successfully!";
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register_student.php">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
            </ol>
        </nav>

        <h2 class="mb-4 text-center">Confirm Deletion</h2>

        <!-- Display success message if the student was deleted successfully -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
            <a href="register_student.php" class="btn btn-primary">Go Back</a>
        <?php else: ?>
            <!-- Display error messages if any -->
            <?php displayErrors($errors); ?>

            <div class="alert alert-warning">
                <p>Are you sure you want to delete the following student? This action cannot be undone.</p>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student_to_delete['id']); ?></li>
                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($student_to_delete['first_name']); ?></li>
                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student_to_delete['last_name']); ?></li>
                </ul>

                <!-- Confirmation Form -->
                <form method="POST" action="delete_student.php?id=<?php echo urlencode($student_id); ?>">
                    <input type="hidden" name="confirm_delete" value="1">
                    <button type="submit" class="btn btn-danger">Yes, Delete Student</button>
                    <a href="register_student.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
