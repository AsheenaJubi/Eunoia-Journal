<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
    if (localStorage.getItem('eunoia-theme') === 'dark') {
        document.documentElement.classList.add('dark-mode');
    }
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'EUNOIA') ?> | EUNOIA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="app-body">
<header class="app-header" id="appHeader">
    <a class="brand" href="dashboard.php"><span>✦</span> EUNOIA</a>
    <button class="hamburger" id="hamburger" aria-label="Open navigation" aria-expanded="false"><i class="fa-solid fa-bars"></i></button>
    <nav class="main-nav" id="mainNav">
        <a class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><i class="fa-solid fa-house"></i> Home</a>
        <a class="<?= in_array($currentPage, ['create_entry.php','edit_entry.php']) ? 'active' : '' ?>" href="dashboard.php"><i class="fa-solid fa-book-open"></i> My Journal</a>
        <a class="<?= $currentPage === 'memories.php' ? 'active' : '' ?>" href="memories.php"><i class="fa-solid fa-images"></i> Memories</a>
        <a class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>" href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a class="btn btn-primary btn-small nav-new" href="create_entry.php"><i class="fa-solid fa-plus"></i> New Entry</a>
        <a class="nav-logout" href="logout.php">Logout</a>
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode">
    <i class="fa-solid fa-moon"></i>
</button>
    </nav>
</header>
<?php if ($flash): ?>
    <div class="flash <?= htmlspecialchars($flash['type']) ?>" role="alert"><i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i><span><?= htmlspecialchars($flash['message']) ?></span><button onclick="this.parentElement.remove()">&times;</button></div>
<?php endif; ?>
<main class="page-shell">
