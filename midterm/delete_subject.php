<?php
session_start();
require_once 'functions.php';

if (!checkUserSessionIsActive()) {
    header("Location: login.php");
    exit;
}

// Check if the subject code is provided in the query string
if (isset($_GET['code'])) {
    $subject_code = $_GET['code'];

    // Retrieve subjects from session
    $subjects = isset($_SESSION['subjects']) ? $_SESSION['subjects'] : [];

    // Find the subject to delete by code
    $subject_to_delete = null;
    foreach ($subjects as $subject) {
        if ($subject['code'] == $subject_code) {
            $subject_to_delete = $subject;
            break;
        }
    }

    // If the subject exists, show the confirmation page
    if ($subject_to_delete) {
        // Handle the confirmation action
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
            // Remove the subject from the session
            foreach ($subjects as $index => $subject) {
                if ($subject['code'] == $subject_code) {
                    unset($subjects[$index]);
                    $_SESSION['subjects'] = array_values($subjects);  // Re-index the array
                    header("Location: subject.php");  // Redirect to subject.php after deletion
                    exit;
                }
            }
        }

        // Display the confirmation page with breadcrumbs
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirm Delete Subject</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container py-5">
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="subject.php">Subjects</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
                    </ol>
                </nav>

                <h2 class="text-center">Are you sure you want to delete the following subject?</h2>
                <div class="card mt-4">
                    <div class="card-body">
                        <p><strong>Subject Name:</strong> ' . htmlspecialchars($subject_to_delete['name']) . '</p>
                        <p><strong>Subject Code:</strong> ' . htmlspecialchars($subject_to_delete['code']) . '</p>
                        <p><strong>Description:</strong> ' . htmlspecialchars($subject_to_delete['description']) . '</p>
                        <form method="POST" action="delete_subject.php?code=' . urlencode($subject_code) . '">
                            <button type="submit" name="confirm_delete" value="yes" class="btn btn-danger">Yes, Delete</button>
                            <a href="subject.php" class="btn btn-secondary">No, Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
        exit;
    } else {
        // If subject is not found, redirect to the subject page
        $_SESSION['errors'][] = "Subject not found.";
        header("Location: subject.php");
        exit;
    }
} else {
    // If no code is provided, redirect to the subject page
    header("Location: subject.php");
    exit;
}
?>
