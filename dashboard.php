<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'My Journal';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM entries WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalEntries = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM entries WHERE user_id = ? AND file_name IS NOT NULL AND file_name <> ''");
$stmt->execute([$_SESSION['user_id']]);
$totalMemories = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT mood, COUNT(*) AS total FROM entries WHERE user_id = ? GROUP BY mood ORDER BY total DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$commonMood = $stmt->fetchColumn() ?: '—';

$stmt = $pdo->prepare("SELECT MAX(created_at) FROM entries WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$latestDate = $stmt->fetchColumn();
$latestDate = $latestDate ? date('M d, Y', strtotime($latestDate)) : '—';
$search = trim($_GET['search'] ?? '');
$moodFilter = trim($_GET['mood'] ?? '');

$sql = "SELECT * FROM entries WHERE user_id = ?";
$params = [$_SESSION['user_id']];
if ($search !== '') {
    $sql .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
$allowedMoods = ['Happy','Calm','Excited','Sad','Angry','Grateful'];
if (in_array($moodFilter, $allowedMoods, true)) {
    $sql .= " AND mood = ?";
    $params[] = $moodFilter;
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$entries = $stmt->fetchAll();

$moodIcons = ['Happy'=>'😊','Calm'=>'😌','Excited'=>'🤩','Sad'=>'😔','Angry'=>'😤','Grateful'=>'❤️'];

require __DIR__ . '/includes/header.php';
?>
<section class="dashboard-head reveal">
    <div><span class="eyebrow">YOUR PERSONAL JOURNAL</span><h1>Good evening, <?= htmlspecialchars($_SESSION['user_name']) ?> <span class="sparkle">✦</span></h1><p>How was your day?</p></div>
    <a href="create_entry.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Entry</a>
</section>

<section class="stats-grid reveal">
    <div class="stat-card"><span class="stat-icon"><i class="fa-solid fa-book-open"></i></span><div><strong><?= $totalEntries ?></strong><small>Total Journal Entries</small></div></div>
    <div class="stat-card"><span class="stat-icon"><i class="fa-solid fa-camera"></i></span><div><strong><?= $totalMemories ?></strong><small>Total Memories</small></div></div>
    <div class="stat-card"><span class="stat-icon"><i class="fa-regular fa-face-smile"></i></span><div><strong><?= htmlspecialchars($commonMood) ?></strong><small>Most Common Mood</small></div></div>
    <div class="stat-card"><span class="stat-icon"><i class="fa-regular fa-calendar"></i></span><div><strong><?= htmlspecialchars($latestDate) ?></strong><small>Latest Entry Date</small></div></div>
</section>

<section class="journal-section">
    <div class="section-heading"><div><span class="eyebrow">YOUR STORIES</span><h2>Recent moments</h2></div></div>
    <div class="filters reveal">
        <form method="GET" class="search-form"><i class="fa-solid fa-magnifying-glass"></i><input id="journalSearch" type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search your thoughts..."></form>
        <div class="mood-filter">
            <label for="moodFilter"><i class="fa-regular fa-face-smile"></i></label>
            <select id="moodFilter" name="mood" onchange="this.form.submit()" form="serverFilter">
                <option value="">All Moods</option>
                <?php foreach ($allowedMoods as $mood): ?><option value="<?= $mood ?>" <?= $moodFilter === $mood ? 'selected' : '' ?>><?= $moodIcons[$mood] ?> <?= $mood ?></option><?php endforeach; ?>
            </select>
            <form id="serverFilter" method="GET" class="hidden-form"><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"></form>
        </div>
    </div>

    <?php if (!$entries): ?>
        <div class="empty-state reveal"><div class="empty-icon">✦</div><h3>No moments found.</h3><p>Start writing, or try a different search or mood.</p><a href="create_entry.php" class="btn btn-primary">Save Your First Moment</a></div>
    <?php else: ?>
        <div class="journal-grid" id="journalGrid">
        <?php foreach ($entries as $entry):
            $preview = trim(preg_replace('/\s+/', ' ', $entry['content']));
            if (strlen($preview) > 170) $preview = substr($preview, 0, 170) . '...';
        ?>
            <article class="journal-card reveal" data-mood="<?= htmlspecialchars($entry['mood']) ?>" data-search="<?= htmlspecialchars(strtolower($entry['title'] . ' ' . $entry['content'])) ?>">
                <div class="entry-meta"><span class="mood-pill"><?= $moodIcons[$entry['mood']] ?? '✦' ?> <?= htmlspecialchars($entry['mood']) ?></span><span><?= date('M d, Y', strtotime($entry['created_at'])) ?></span></div>
                <?php if ($entry['file_name']): ?><div class="entry-thumb"><img src="uploads/<?= htmlspecialchars($entry['file_name']) ?>" alt="Memory for <?= htmlspecialchars($entry['title']) ?>"></div><?php endif; ?>
                <h3><?= htmlspecialchars($entry['title']) ?></h3>
                <p class="entry-preview">“<?= htmlspecialchars($preview) ?>”</p>
                <div class="entry-actions">
                    <button class="text-btn read-entry" type="button" data-title="<?= htmlspecialchars($entry['title']) ?>" data-content="<?= htmlspecialchars($entry['content']) ?>" data-mood="<?= htmlspecialchars($entry['mood']) ?>" data-date="<?= date('M d, Y', strtotime($entry['created_at'])) ?>">Read <i class="fa-solid fa-arrow-right"></i></button>
                    <a class="icon-action" href="edit_entry.php?id=<?= (int)$entry['entry_id'] ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
                    <a class="icon-action danger" href="delete_entry.php?id=<?= (int)$entry['entry_id'] ?>" data-delete title="Delete"><i class="fa-solid fa-trash"></i></a>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<div class="modal" id="readModal" aria-hidden="true"><div class="modal-card"><button class="modal-close" data-close-modal>&times;</button><span class="eyebrow" id="readMood"></span><h2 id="readTitle"></h2><small id="readDate"></small><div class="modal-content" id="readContent"></div></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>