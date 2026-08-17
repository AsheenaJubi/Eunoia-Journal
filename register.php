<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
require_once __DIR__ . '/config/database.php';

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || strlen($name) < 2) $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account created successfully. Please sign in.'];
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
    if (localStorage.getItem('eunoia-theme') === 'dark') {
        document.documentElement.classList.add('dark-mode');
    }
</script>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account | EUNOIA</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="auth-body">
<div class="auth-decoration auth-decoration-one"></div><div class="auth-decoration auth-decoration-two"></div>
<a class="auth-brand brand" href="index.php"><span>✦</span> EUNOIA</a>
<section class="auth-card register-card">
    <div class="auth-heading"><span class="eyebrow">BEGIN YOUR JOURNEY</span><h1>Create your <em>space.</em></h1><p>A few details, then your private journal is ready.</p></div>
    <?php if ($errors): ?><div class="form-alert error"><i class="fa-solid fa-circle-exclamation"></i><div><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div></div><?php endif; ?>
    <form method="POST" data-validate>
        <div class="field"><label for="name">Full Name</label><div class="input-wrap"><i class="fa-regular fa-user"></i><input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Your name" required minlength="2"></div></div>
        <div class="field"><label for="email">Email</label><div class="input-wrap"><i class="fa-regular fa-envelope"></i><input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="you@example.com" required></div></div>
        <div class="field"><label for="password">Password <small>(8+ characters)</small></label><div class="input-wrap"><i class="fa-solid fa-lock"></i><input type="password" id="password" name="password" placeholder="Create a password" required minlength="8"><button type="button" class="password-toggle" data-target="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div></div>
        <div class="field"><label for="confirm_password">Confirm Password</label><div class="input-wrap"><i class="fa-solid fa-shield"></i><input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat your password" required minlength="8"><button type="button" class="password-toggle" data-target="confirm_password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div></div>
        <button class="btn btn-primary btn-full" type="submit">CREATE ACCOUNT <i class="fa-solid fa-arrow-right"></i></button>
    </form>
    <p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p>
    <a class="back-home" href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to EUNOIA</a>
</section>
<script src="js/script.js"></script>
</body>
</html>