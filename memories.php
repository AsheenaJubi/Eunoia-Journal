<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
$pageTitle = 'Memories';

$stmt = $pdo->prepare("SELECT entry_id, title, mood, file_name, created_at FROM entries WHERE user_id = ? AND file_name IS NOT NULL AND file_name <> '' ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$memories = $stmt->fetchAll();
$moodIcons = ['Happy'=>'😊','Calm'=>'😌','Excited'=>'🤩','Sad'=>'😔','Angry'=>'😤','Grateful'=>'❤️'];

require __DIR__ . '/includes/header.php';
?>
<section class="dashboard-head reveal"><div><span class="eyebrow">YOUR VISUAL JOURNEY</span><h1>Little pieces of <em>life.</em></h1><p>Photographs attached to moments you decided to keep.</p></div><a href="create_entry.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Memory</a></section>
<?php if (!$memories): ?>
<div class="empty-state large reveal"><div class="empty-icon"><i class="fa-regular fa-images"></i></div><h3>No memories yet.</h3><p>Your first beautiful moment is waiting to be saved.</p><a href="create_entry.php" class="btn btn-primary">Create Your First Memory</a></div>
<?php else: ?>
<div class="memory-grid">
<?php foreach ($memories as $memory): ?>
<article class="memory-card reveal"><button class="memory-image-btn lightbox-trigger" type="button" data-image="uploads/<?= htmlspecialchars($memory['file_name']) ?>" data-title="<?= htmlspecialchars($memory['title']) ?>"><img src="uploads/<?= htmlspecialchars($memory['file_name']) ?>" alt="<?= htmlspecialchars($memory['title']) ?>" loading="lazy"><span class="image-zoom"><i class="fa-solid fa-expand"></i></span></button><div class="memory-info"><div><span class="mood-pill"><?= $moodIcons[$memory['mood']] ?? '✦' ?> <?= htmlspecialchars($memory['mood']) ?></span><small><?= date('M d, Y', strtotime($memory['created_at'])) ?></small></div><h3><?= htmlspecialchars($memory['title']) ?></h3></div></article>
<?php endforeach; ?>
</div>
<?php endif; ?>

<div class="modal lightbox" id="lightbox" aria-hidden="true"><div class="lightbox-inner"><button class="modal-close" data-close-modal>&times;</button><img id="lightboxImage" src="" alt=""><p id="lightboxTitle"></p></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>