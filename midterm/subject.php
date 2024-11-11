<?php
session_start();
require_once 'functions.php';

if (!checkUserSessionIsActive()) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$errors = [];
$success_message = "";

// Retrieve all subjects from session
$subjects = isset($_SESSION['subjects']) ? $_SESSION['subjects'] : [];

// Handle form submission for adding a subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $subject_name = trim($_POST['subject_name']);
    $subject_code = trim($_POST['subject_code']);
    $subject_description = trim($_POST['subject_description']);

    // Validate subject data
    $subject_data = [
        'name' => $subject_name,
        'code' => $subject_code,
        'description' => $subject_description
    ];

    // Validate subject
    $errors = validateSubjectData($subject_data);
    if (empty($errors)) {
        // Check for duplicates
        $duplicate_error = checkDuplicateSubjectData($subject_data);
        if ($duplicate_error) {
            $errors[] = $duplicate_error;
        } else {
            // Add the subject to the session
            $_SESSION['subjects'][] = $subject_data; // Add the new subject to the session
            $success_message = "Subject added successfully!";
            
            // Refresh the subjects from session after adding
            $subjects = $_SESSION['subjects']; // Ensure subjects are updated
        }
    }
}

// Display error messages if any (using the function from functions.php)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
            </ol>
        </nav>

        <h2 class="mb-4 text-center">Add New Subject</h2>

        <!-- Display success message if the subject was added successfully -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Display error messages if validation fails -->
        <?php displayErrors($errors); ?>

        <!-- Subject Form -->
        <div class="mb-4">
            <form method="POST" action="subject.php">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Subject Name</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo isset($subject_name) ? htmlspecialchars($subject_name) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="subject_code" class="form-label">Subject Code</label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo isset($subject_code) ? htmlspecialchars($subject_code) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="subject_description" class="form-label">Subject Description</label>
                    <textarea class="form-control" id="subject_description" name="subject_description" rows="3" required><?php echo isset($subject_description) ? htmlspecialchars($subject_description) : ''; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Subject</button>
            </form>
        </div>

        <!-- Subjects Table -->
        <div class="table-responsive">
            <h3 class="text-center">Existing Subjects</h3>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Subject Code</th>
                        <th scope="col">Subject Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($subject['code']); ?></td>
                            <td><?php echo htmlspecialchars($subject['name']); ?></td>
                            <td><?php echo htmlspecialchars($subject['description']); ?></td>
                            <td>
                                <a href="edit_subject.php?code=<?php echo urlencode($subject['code']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_subject.php?code=<?php echo urlencode($subject['code']); ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
