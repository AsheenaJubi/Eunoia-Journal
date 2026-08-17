<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
require_once __DIR__ . '/config/database.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Please enter a valid email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT user_id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Welcome back, ' . $user['name'] . '!'];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'The email or password you entered is incorrect.';
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
<title>Sign In | EUNOIA</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="auth-body">
<div class="auth-decoration auth-decoration-one"></div><div class="auth-decoration auth-decoration-two"></div>
<a class="auth-brand brand" href="index.php"><span>✦</span> EUNOIA</a>
<section class="auth-card">
    <div class="auth-heading"><span class="eyebrow">YOUR PERSONAL SPACE</span><h1>Welcome <em>back.</em></h1><p>Pick up where your thoughts left off.</p></div>
    <?php if ($error): ?><div class="form-alert error"><i class="fa-solid fa-circle-exclamation"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST" data-validate>
        <div class="field"><label for="email">Email</label><div class="input-wrap"><i class="fa-regular fa-envelope"></i><input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="you@example.com" required></div></div>
        <div class="field"><label for="password">Password</label><div class="input-wrap"><i class="fa-solid fa-lock"></i><input type="password" id="password" name="password" placeholder="Enter your password" required><button type="button" class="password-toggle" data-target="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div></div>
        <button class="btn btn-primary btn-full" type="submit">SIGN IN <i class="fa-solid fa-arrow-right"></i></button>
    </form>
    <p class="auth-switch">Don't have an account? <a href="register.php">Create one</a></p>
    <a class="back-home" href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to EUNOIA</a>
</section>
<script src="js/script.js"></script>
</body>
</html>