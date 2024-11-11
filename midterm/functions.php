<?php

// User Authentication Functions
function getUsers() {
    return [
        ['email' => 'user1@example.com', 'password' => 'password1'],
        ['email' => 'user2@example.com', 'password' => 'password2'],
        ['email' => 'user3@example.com', 'password' => 'password3'],
        ['email' => 'user4@example.com', 'password' => 'password4'],
        ['email' => 'user5@example.com', 'password' => 'password5']
    ];
}

function validateLoginCredentials($email, $password) {
    $errors = [];
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }
    return $errors;
}

function checkLoginCredentials($email, $password, $users) {
    foreach ($users as $user) {
        if ($email === $user['email'] && $password === $user['password']) {
            return true;
        }
    }
    return false;
}

function checkUserSessionIsActive() {
    return isset($_SESSION['user_email']);
}

function guard() {
    if (!checkUserSessionIsActive()) {
        header("Location: login.php");
        exit;
    }
}

function displayErrors($errors) {
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}

function renderErrorsToView($error) {
    return "<div class='alert alert-danger'>$error</div>";
}

// Validate student data (only checks student ID now)
// Validate student data
function validateStudentData($data) {
    $errors = [];

    // Validate student ID
    if (empty($data['id'])) {
        $errors[] = 'Student ID is required.';
    }

    // Validate first name
    if (empty($data['first_name'])) {
        $errors[] = 'First Name is required.';
    }

    // Validate last name
    if (empty($data['last_name'])) {
        $errors[] = 'Last Name is required.';
    }

    return $errors;
}

// Check for duplicate student ID
function checkDuplicateStudentId($id) {
    if (isset($_SESSION['students'])) {
        foreach ($_SESSION['students'] as $student) {
            if ($student['id'] === $id) {
                return 'A student with this ID already exists.';
            }
        }
    }
    return false;
}


function getSelectedStudentIndex($student_id) {
    if (!isset($_SESSION['students'])) {
        return null;
    }

    foreach ($_SESSION['students'] as $index => $student) {
        if ($student['id'] === $student_id) {
            return $index;
        }
    }
    return null;
}
function getSelectedStudentData($index) {
    return isset($_SESSION['students'][$index]) ? $_SESSION['students'][$index] : null;
}
function validateSubjectData($subject_data) {
    $errors = [];

    // Check for empty fields
    if (empty($subject_data['name'])) {
        $errors[] = 'Subject name is required.';
    }

    if (empty($subject_data['code'])) {
        $errors[] = 'Subject code is required.';
    }

    if (empty($subject_data['description'])) {
        $errors[] = 'Subject description is required.';
    }

    return $errors;
}

// Check if a subject already exists in the session
function checkDuplicateSubjectData($subject_data, $exclude_code = null) {
    // Loop through all subjects in the session
    if (isset($_SESSION['subjects']) && !empty($_SESSION['subjects'])) {
        foreach ($_SESSION['subjects'] as $subject) {
            // Skip the current subject that is being updated by excluding the subject code
            if ($exclude_code && $subject['code'] === $exclude_code) {
                continue;
            }

            // If the subject code matches the new data, it's a duplicate
            if ($subject['code'] === $subject_data['code']) {
                return "Subject with this code already exists.";
            }
        }
    }
    return null; // No duplicates found
}


function getSelectedSubjectIndex($subject_code) {
    if (!isset($_SESSION['subjects'])) {
        return null;
    }

    foreach ($_SESSION['subjects'] as $index => $subject) {
        if ($subject['code'] === $subject_code) {
            return $index;
        }
    }
    return null;
}
function getSelectedSubjectData($index) {
    return isset($_SESSION['subjects'][$index]) ? $_SESSION['subjects'][$index] : null;
}
function validateAttachedSubject($subject_data) {
    $errors = [];
    if (empty($subject_data['code'])) {
        $errors[] = "Subject code is required to attach.";
    }
    return $errors;
}

?>

