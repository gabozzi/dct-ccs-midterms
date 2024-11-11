<?php
session_start();
require_once 'functions.php';

if (!checkUserSessionIsActive()) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success_message = "";
$student_data = null;

// Check if student ID is provided in URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Find student data from the session
    foreach ($_SESSION['students'] as $student) {
        if ($student['id'] === $student_id) {
            $student_data = $student;
            break;
        }
    }

    // If student is not found, redirect to register_student.php
    if ($student_data === null) {
        header("Location: register_student.php");
        exit;
    }
}

// Handle form submission to update student details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Validate student data
    $student_data = [
        'id' => $student_id,
        'first_name' => $first_name,
        'last_name' => $last_name
    ];

    $errors = validateStudentData($student_data);

    if (empty($errors)) {
        // Find and update the student in the session
        foreach ($_SESSION['students'] as $index => $student) {
            if ($student['id'] === $student_id) {
                $_SESSION['students'][$index] = $student_data;
                $success_message = "Student details updated successfully!";
                break;
            }
        }
    }
}

// Display error messages if any
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register_student.php">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
            </ol>
        </nav>

        <h2 class="mb-4 text-center">Edit Student</h2>

        <!-- Display success message if the student details were updated successfully -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Display error messages if validation fails -->
        <?php displayErrors($errors); ?>

        <!-- Student Edit Form -->
        <div class="mb-4">
            <form method="POST" action="edit_student.php?id=<?php echo urlencode($student_data['id']); ?>">
                <input type="hidden" name="action" value="edit">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_data['id']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student_data['first_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student_data['last_name']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Student</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
