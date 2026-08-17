<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
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
    <title>EUNOIA | A quiet place for your thoughts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="landing-page">
<header class="site-header landing-header">
    <a class="brand" href="index.php"><span>✦</span> EUNOIA</a>
    <nav class="landing-nav">
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode">
    <i class="fa-solid fa-moon"></i>
</button>
        <a href="#features">Features</a>
        <a href="#quote">Why EUNOIA</a>
        <?php if ($isLoggedIn): ?>
            <a class="btn btn-primary btn-small" href="dashboard.php">Open Journal</a>
        <?php else: ?>
            <a class="btn btn-outline btn-small" href="login.php">Sign In</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <section class="hero">
        <div class="hero-orb hero-orb-one"></div>
        <div class="hero-orb hero-orb-two"></div>
        <div class="hero-copy reveal">
            <span class="eyebrow">PERSONAL JOURNAL • MEMORY SPACE</span>
            <h1>A quiet place<br>for your <em>thoughts.</em></h1>
            <p>Capture your moments, understand your moods, and preserve the memories that matter.</p>
            <div class="hero-actions">
                <a href="<?= $isLoggedIn ? 'create_entry.php' : 'register.php' ?>" class="btn btn-primary">Start Writing <i class="fa-solid fa-arrow-right"></i></a>
                <a href="#features" class="btn btn-ghost">Explore EUNOIA</a>
            </div>
        </div>
        <div class="thought-card floating-card reveal">
            <div class="card-topline"><span>✦</span> TODAY'S THOUGHT</div>
            <p>“Sometimes the smallest moments become our favorite memories.”</p>
            <div class="thought-line"></div>
            <small>Keep what feels meaningful.</small>
        </div>
    </section>

    <section id="features" class="section">
        <div class="section-heading center reveal">
            <span class="eyebrow">MADE FOR REFLECTION</span>
            <h2>Everything you need to<br><em>remember how you felt.</em></h2>
        </div>
        <div class="feature-grid">
            <article class="feature-card reveal"><div class="icon-box"><i class="fa-solid fa-pen-nib"></i></div><h3>Express</h3><p>Write down your thoughts without limits and give your everyday moments a place to live.</p></article>
            <article class="feature-card reveal"><div class="icon-box"><i class="fa-solid fa-camera-retro"></i></div><h3>Remember</h3><p>Preserve photographs and meaningful moments alongside the words that made them special.</p></article>
            <article class="feature-card reveal"><div class="icon-box"><i class="fa-solid fa-moon"></i></div><h3>Reflect</h3><p>Track your moods and notice the little patterns that shape your personal journey.</p></article>
            <article class="feature-card reveal"><div class="icon-box"><i class="fa-solid fa-lock"></i></div><h3>Private</h3><p>Your journal is connected to your account, with ownership checks protecting personal entries.</p></article>
        </div>
    </section>

    <section class="stats-band">
        <div class="stat-item"><strong>∞</strong><span>Moments to capture</span></div>
        <div class="stat-item"><strong>6</strong><span>Moods to explore</span></div>
        <div class="stat-item"><strong>100%</strong><span>Your personal space</span></div>
        <div class="stat-item"><strong>24/7</strong><span>Ready when you are</span></div>
    </section>

    <section id="quote" class="quote-section section">
        <div class="quote-mark">“</div>
        <blockquote>The things we remember are the stories we choose to keep.</blockquote>
        <a class="text-link" href="<?= $isLoggedIn ? 'create_entry.php' : 'register.php' ?>">Begin Your Journal <i class="fa-solid fa-arrow-right"></i></a>
    </section>
</main>

<footer class="footer">
    <div><a class="brand" href="index.php"><span>✦</span> EUNOIA</a><p>A quiet place for your thoughts.</p></div>
    <div class="footer-links"><a href="index.php">Home</a><?php if ($isLoggedIn): ?><a href="dashboard.php">Journal</a><a href="memories.php">Memories</a><a href="profile.php">Profile</a><?php endif; ?></div>
    <div class="footer-social"><a href="mailto:eunoia@example.com"><i class="fa-solid fa-envelope"></i></a><a href="#"><i class="fa-brands fa-instagram"></i></a><a href="#"><i class="fa-brands fa-facebook-f"></i></a><small>© <?= date('Y') ?> EUNOIA. All Rights Reserved.</small></div>
</footer>
<button id="scrollTopBtn" class="scroll-top" aria-label="Scroll to top"><i class="fa-solid fa-arrow-up"></i></button>
<script src="js/script.js"></script>
</body>
</html>