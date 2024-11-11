<?php
session_start();
require_once 'functions.php';

if (!checkUserSessionIsActive()) {
    header("Location: login.php");
    exit;
}

// Initialize errors array
$errors = [];
$success_message = "";

// Retrieve the subject code from the URL parameter
if (isset($_GET['code'])) {
    $subject_code = $_GET['code'];

    // Find the subject in the session
    $subjects = isset($_SESSION['subjects']) ? $_SESSION['subjects'] : [];
    $subject_index = getSelectedSubjectIndex($subject_code);
    $subject_data = isset($subjects[$subject_index]) ? $subjects[$subject_index] : null;

    if (!$subject_data) {
        header("Location: subject.php"); // Redirect if subject not found
        exit;
    }

    // Initialize variables for the form with the existing subject data
    $subject_name = $subject_data['name'];
    $subject_description = $subject_data['description'];

    // Handle form submission for updating the subject
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
        // Get subject data from POST
        $subject_name = isset($_POST['subject_name']) ? trim($_POST['subject_name']) : '';
        $subject_code = isset($_POST['subject_code']) ? trim($_POST['subject_code']) : ''; // Ensure subject code is set
        $subject_description = isset($_POST['subject_description']) ? trim($_POST['subject_description']) : '';

        // Validate subject data
        $subject_data = [
            'name' => $subject_name,
            'code' => $subject_code,
            'description' => $subject_description
        ];

        // Check if the subject name is empty
        if (empty($subject_name)) {
            $errors[] = "Subject Name is required.";
        }

        if (empty($subject_description)) {
            $errors[] = "Subject Description is required.";
        }

        // If there are no errors, proceed with the update
        if (empty($errors)) {
            // No need to check for duplicates since the code is locked
            // Update the subject in the session
            $_SESSION['subjects'][$subject_index] = $subject_data; // Update the subject data
            $success_message = "Subject updated successfully!";
        }
    }
} else {
    header("Location: subject.php"); // Redirect if no subject code is passed
    exit;
}

// Display error messages if any
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="subject.php">Subjects</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
            </ol>
        </nav>

        <h2 class="mb-4">Edit Subject</h2>

        <!-- Display success message if subject was updated -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Display error messages if validation fails -->
        <?php displayErrors($errors); ?>

        <!-- Edit Subject Form -->
        <form method="POST" action="edit_subject.php?code=<?php echo urlencode($subject_code); ?>">
            <input type="hidden" name="action" value="edit">
            <!-- Hidden input for the subject code -->
            <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">

            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>" required>
            </div>

            <div class="mb-3">
                <label for="subject_code" class="form-label">Subject Code</label>
                <!-- Disabled input for the subject code -->
                <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>" required disabled>
            </div>

            <div class="mb-3">
                <label for="subject_description" class="form-label">Subject Description</label>
                <textarea class="form-control" id="subject_description" name="subject_description" rows="3" required><?php echo htmlspecialchars($subject_description); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Subject</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
