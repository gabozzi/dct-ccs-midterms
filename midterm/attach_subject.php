<?php
session_start();
require_once 'functions.php';

if (!checkUserSessionIsActive()) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success_message = "";

// Get the student ID from the URL
$student_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$student_id) {
    header("Location: register_student.php");
    exit;
}

// Retrieve student from session
$students = isset($_SESSION['students']) ? $_SESSION['students'] : [];
$student = null;
foreach ($students as $s) {
    if ($s['id'] == $student_id) {
        $student = $s;
        break;
    }
}

if (!$student) {
    header("Location: register_student.php");
    exit;
}

// Example subjects (replace with your actual subjects from session or database)
$subjects = [
    ['code' => '1001', 'name' => 'English'],
    ['code' => '1002', 'name' => 'Math'],
    ['code' => '1003', 'name' => 'Science']
];

// Handle form submission for attaching subjects
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'attach') {
    $selected_subjects = $_POST['subjects'] ?? [];

    if (!empty($selected_subjects)) {
        // Attach selected subjects to the student
        $student['subjects'] = array_merge($student['subjects'], $selected_subjects);

        // Update the student in the session
        foreach ($students as &$s) {
            if ($s['id'] == $student_id) {
                $s = $student;
                break;
            }
        }
        $_SESSION['students'] = $students;

        $success_message = "Subjects attached successfully!";
    } else {
        $errors[] = "Please select at least one subject.";
    }
}

// Handle subject detachment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'detach') {
    $subject_to_detach = $_POST['subject_code'] ?? '';

    // Detach the selected subject from the student's list
    if ($subject_to_detach) {
        $student['subjects'] = array_filter($student['subjects'], function ($subject) use ($subject_to_detach) {
            return $subject !== $subject_to_detach;
        });

        // Update the student in the session
        foreach ($students as &$s) {
            if ($s['id'] == $student_id) {
                $s = $student;
                break;
            }
        }
        $_SESSION['students'] = $students;

        $success_message = "Subject detached successfully!";
    } else {
        $errors[] = "No subject selected for detachment.";
    }
}

// Display error messages if any
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attach Subjects to Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register_student.php">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attach Subjects</li>
            </ol>
        </nav>

        <h2 class="mb-4 text-center">Attach Subjects to Student: <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>

        <!-- Display success message if the subjects were attached or detached successfully -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Display error messages if any -->
        <?php displayErrors($errors); ?>

        <!-- Student Information -->
        <div class="mb-4">
            <h5><strong>Student Information:</strong></h5>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['id']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        </div>

        <!-- Subject Attachment Form -->
        <form method="POST" action="attach_subject.php?id=<?php echo urlencode($student_id); ?>">
            <input type="hidden" name="action" value="attach">

            <!-- Checkbox options for selecting subjects -->
            <div class="mb-4">
                <h5><strong>Select Subjects:</strong></h5>
                <?php foreach ($subjects as $subject): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="subjects[]" value="<?php echo htmlspecialchars($subject['code']); ?>" 
                            <?php echo in_array($subject['code'], $student['subjects']) ? 'disabled checked' : ''; ?>>
                        <label class="form-check-label">
                            <?php echo htmlspecialchars($subject['code'] . ' - ' . $subject['name']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">Attach Subjects</button>
        </form>

        <!-- Subject List (Attached Subjects) -->
        <div class="mt-4">
            <h5><strong>Subject List:</strong></h5>
            <?php if (!empty($student['subjects'])): ?>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Subject ID</th>
                            <th>Subject Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student['subjects'] as $subject_code): ?>
                            <?php 
                            // Find the subject name by its code
                            $subject_name = '';
                            foreach ($subjects as $subject) {
                                if ($subject['code'] == $subject_code) {
                                    $subject_name = $subject['name'];
                                    break;
                                }
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject_code); ?></td>
                                <td><?php echo htmlspecialchars($subject_name); ?></td>
                                <td>
                                    <form method="POST" action="attach_subject.php?id=<?php echo urlencode($student_id); ?>" class="d-inline">
                                        <input type="hidden" name="action" value="detach">
                                        <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Detach</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No subjects attached yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
