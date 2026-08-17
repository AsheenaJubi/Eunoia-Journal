<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'New Entry';
$errors = [];
$moods = ['Happy','Calm','Excited','Sad','Angry','Grateful'];
$moodIcons = ['Happy'=>'😊','Calm'=>'😌','Excited'=>'🤩','Sad'=>'😔','Angry'=>'😤','Grateful'=>'❤️'];
$title = '';
$content = '';
$mood = 'Happy';
$dateValue = date('Y-m-d');

function handleImageUpload(array $file, ?string $oldFile = null): array {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return ['name' => $oldFile, 'error' => null];
    if ($file['error'] !== UPLOAD_ERR_OK) return ['name' => $oldFile, 'error' => 'The image upload failed.'];
    if ($file['size'] > 5 * 1024 * 1024) return ['name' => $oldFile, 'error' => 'Image size must be 5 MB or less.'];

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) return ['name' => $oldFile, 'error' => 'Only JPG, JPEG, PNG and WEBP images are allowed.'];

    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $destination = __DIR__ . '/uploads/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $destination)) return ['name' => $oldFile, 'error' => 'Could not save the uploaded image.'];
    return ['name' => $name, 'error' => null];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $mood = $_POST['mood'] ?? 'Happy';
    $dateValue = $_POST['date'] ?? date('Y-m-d');

    if ($title === '' || strlen($title) > 150) $errors[] = 'Title is required and must be under 150 characters.';
    if ($content === '') $errors[] = 'Journal content is required.';
    if (!in_array($mood, $moods, true)) $errors[] = 'Please choose a valid mood.';
    $timestamp = strtotime($dateValue);
    if (!$timestamp || $timestamp > time()) $errors[] = 'Please choose a valid date that is not in the future.';

    $upload = ['name' => null, 'error' => null];
    if (!$errors && isset($_FILES['memory'])) {
        $upload = handleImageUpload($_FILES['memory']);
        if ($upload['error']) $errors[] = $upload['error'];
    }

    if (!$errors) {
        $createdAt = date('Y-m-d H:i:s', $timestamp);
        $stmt = $pdo->prepare("INSERT INTO entries (user_id, title, content, mood, file_name, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$_SESSION['user_id'], $title, $content, $mood, $upload['name'], $createdAt]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Your moment has been saved.'];
        header('Location: dashboard.php');
        exit;
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="form-page-head reveal"><a class="back-link" href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to journal</a><span class="eyebrow">A MOMENT WORTH KEEPING</span><h1>Save your <em>moment.</em></h1><p>There is no perfect way to write. Just begin.</p></section>
<section class="entry-form-card reveal">
<?php if ($errors): ?><div class="form-alert error"><i class="fa-solid fa-circle-exclamation"></i><div><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" data-validate>
    <div class="form-row"><div class="field"><label for="title">Title</label><input class="plain-input" type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" placeholder="Give this moment a name..." required maxlength="150"></div><div class="field"><label for="date">Date</label><input class="plain-input" type="date" id="date" name="date" value="<?= htmlspecialchars($dateValue) ?>" max="<?= date('Y-m-d') ?>" required></div></div>
    <div class="field"><label>Mood</label><div class="mood-options"><?php foreach ($moods as $m): ?><label class="mood-option"><input type="radio" name="mood" value="<?= $m ?>" <?= $mood === $m ? 'checked' : '' ?>><span><?= $moodIcons[$m] ?> <?= $m ?></span></label><?php endforeach; ?></div></div>
    <div class="field"><label for="content">Journal Content</label><textarea class="plain-input textarea" id="content" name="content" rows="10" placeholder="What happened today? How did it make you feel?" required><?= htmlspecialchars($content) ?></textarea><small class="field-hint">Write freely. Your thoughts belong here.</small></div>
    <div class="field"><label for="memory">Upload Memory <small>(JPG, PNG, WEBP • max 5 MB)</small></label><div class="upload-box"><input type="file" id="memory" name="memory" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-image-preview><label for="memory" class="upload-label"><i class="fa-solid fa-cloud-arrow-up"></i><span>Choose a photograph</span><small>or tap here to browse</small></label><img id="imagePreview" class="image-preview" alt="Selected image preview"></div></div>
    <button class="btn btn-primary btn-large" type="submit">Save My Moment ✦</button>
</form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>