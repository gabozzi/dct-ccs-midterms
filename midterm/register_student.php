<?php
session_start();
require_once 'functions.php';

if (!checkUserSessionIsActive()) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success_message = "";

// Retrieve subjects from session
$subjects = isset($_SESSION['subjects']) ? $_SESSION['subjects'] : [];

// Handle form submission for registering a student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Validate student data
    $student_data = [
        'id' => $student_id,
        'first_name' => $first_name,
        'last_name' => $last_name
    ];

    // Validation function for student details
    $errors = validateStudentData($student_data);
    if (empty($errors)) {
        // Check for duplicates (you can add your custom check here)
        $duplicate_error = checkDuplicateStudentId($student_id);
        if ($duplicate_error) {
            $errors[] = $duplicate_error;
        } else {
            // Register the student in the session (or database)
            $_SESSION['students'][] = $student_data; // Store the student in session
            $success_message = "Student registered successfully!";
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
    <title>Register Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Register Student</li>
            </ol>
        </nav>

        <h2 class="mb-4 text-center">Register New Student</h2>

        <!-- Display success message if the student was registered successfully -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Display error messages if validation fails -->
        <?php displayErrors($errors); ?>

        <!-- Student Registration Form -->
        <div class="mb-4">
            <form method="POST" action="register_student.php">
                <input type="hidden" name="action" value="register">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo isset($student_id) ? htmlspecialchars($student_id) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register Student</button>
            </form>
        </div>

        <!-- Registered Students Table -->
        <div class="table-responsive mt-4">
            <h3 class="text-center">Registered Students</h3>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($_SESSION['students'])): ?>
                        <?php foreach ($_SESSION['students'] as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['id']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                <td>
                                    <a href="edit_student.php?id=<?php echo urlencode($student['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_student.php?id=<?php echo urlencode($student['id']); ?>" class="btn btn-danger btn-sm">Delete</a>
                                    <a href="attach_subject.php?id=<?php echo urlencode($student['id']); ?>" class="btn btn-info btn-sm">Attach Subject</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
