<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    $users = [
        ['email' => 'user1@email.com', 'password' => 'password1'],
        ['email' => 'user2@email.com', 'password' => 'password2'],
        ['email' => 'user3@email.com', 'password' => 'password3'],
        ['email' => 'user4@email.com', 'password' => 'password4'],
        ['email' => 'user5@email.com', 'password' => 'password5']
    ];
    $error = $emailError = $passwordError = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        if (empty($email)) {
            $emailError = "Email is required";
        }
        if (empty($password)) {
            $passwordError = "Password is required";
        }
        if (empty($emailError) && empty($passwordError)) {
            $isValidUser = false;
            foreach ($users as $user) {
                if ($email === $user['email'] && $password === $user['password']) {
                    $isValidUser = true;
                    break;
                }
            }
            if ($isValidUser) {
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid email or password!";
            }
        }
    }
    ?>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 22rem;">
            <h3 class="text-center mb-4">Login</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control <?php echo !empty($emailError) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>">
                    <div class="invalid-feedback"><?php echo $emailError; ?></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?php echo !empty($passwordError) ? 'is-invalid' : ''; ?>" id="password" name="password">
                    <div class="invalid-feedback"><?php echo $passwordError; ?></div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
