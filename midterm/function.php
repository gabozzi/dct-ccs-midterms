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
?>