<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$entryId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$entryId) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid journal entry.'];
    header('Location: dashboard.php'); exit;
}
$stmt = $pdo->prepare("SELECT * FROM entries WHERE entry_id = ? AND user_id = ?");
$stmt->execute([$entryId, $_SESSION['user_id']]);
$entry = $stmt->fetch();
if (!$entry) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'That journal entry was not found.'];
    header('Location: dashboard.php'); exit;
}

$pageTitle = 'Edit Entry';
$errors = [];
$moods = ['Happy','Calm','Excited','Sad','Angry','Grateful'];
$moodIcons = ['Happy'=>'😊','Calm'=>'😌','Excited'=>'🤩','Sad'=>'😔','Angry'=>'😤','Grateful'=>'❤️'];

function editImageUpload(array $file): array {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return ['name' => null, 'error' => null];
    if ($file['error'] !== UPLOAD_ERR_OK) return ['name' => null, 'error' => 'The image upload failed.'];
    if ($file['size'] > 5 * 1024 * 1024) return ['name' => null, 'error' => 'Image size must be 5 MB or less.'];
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) return ['name' => null, 'error' => 'Only JPG, JPEG, PNG and WEBP images are allowed.'];
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $name)) return ['name' => null, 'error' => 'Could not save the uploaded image.'];
    return ['name' => $name, 'error' => null];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $mood = $_POST['mood'] ?? '';
    $dateValue = $_POST['date'] ?? '';
    $removeImage = isset($_POST['remove_image']);

    if ($title === '' || strlen($title) > 150) $errors[] = 'Title is required and must be under 150 characters.';
    if ($content === '') $errors[] = 'Journal content is required.';
    if (!in_array($mood, $moods, true)) $errors[] = 'Please choose a valid mood.';
    $timestamp = strtotime($dateValue);
    if (!$timestamp || $timestamp > time()) $errors[] = 'Please choose a valid date that is not in the future.';

    $newFile = $entry['file_name'];
    if (!$errors && isset($_FILES['memory'])) {
        $upload = editImageUpload($_FILES['memory']);
        if ($upload['error']) $errors[] = $upload['error'];
        elseif ($upload['name']) $newFile = $upload['name'];
    }
    if (!$errors && $removeImage) $newFile = null;

    if (!$errors) {
        if ($newFile !== $entry['file_name'] && $entry['file_name']) {
            $oldPath = __DIR__ . '/uploads/' . basename($entry['file_name']);
            if (is_file($oldPath)) @unlink($oldPath);
        }
        $updatedDate = date('Y-m-d H:i:s', $timestamp);
        $stmt = $pdo->prepare("UPDATE entries SET title = ?, content = ?, mood = ?, file_name = ?, created_at = ?, updated_at = NOW() WHERE entry_id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $mood, $newFile, $updatedDate, $entryId, $_SESSION['user_id']]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Your journal entry was updated.'];
        header('Location: dashboard.php'); exit;
    }
} else {
    $title = $entry['title'];
    $content = $entry['content'];
    $mood = $entry['mood'];
    $dateValue = date('Y-m-d', strtotime($entry['created_at']));
}

require __DIR__ . '/includes/header.php';
?>
<section class="form-page-head reveal"><a class="back-link" href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to journal</a><span class="eyebrow">REVISIT A MEMORY</span><h1>Edit your <em>moment.</em></h1><p>Keep the story true to how you remember it.</p></section>
<section class="entry-form-card reveal">
<?php if ($errors): ?><div class="form-alert error"><i class="fa-solid fa-circle-exclamation"></i><div><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" data-validate>
    <div class="form-row"><div class="field"><label for="title">Title</label><input class="plain-input" type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required maxlength="150"></div><div class="field"><label for="date">Date</label><input class="plain-input" type="date" id="date" name="date" value="<?= htmlspecialchars($dateValue) ?>" max="<?= date('Y-m-d') ?>" required></div></div>
    <div class="field"><label>Mood</label><div class="mood-options"><?php foreach ($moods as $m): ?><label class="mood-option"><input type="radio" name="mood" value="<?= $m ?>" <?= $mood === $m ? 'checked' : '' ?>><span><?= $moodIcons[$m] ?> <?= $m ?></span></label><?php endforeach; ?></div></div>
    <div class="field"><label for="content">Journal Content</label><textarea class="plain-input textarea" id="content" name="content" rows="10" required><?= htmlspecialchars($content) ?></textarea></div>
    <div class="field"><label>Memory Photograph</label><?php if ($entry['file_name']): ?><div class="current-image"><img src="uploads/<?= htmlspecialchars($entry['file_name']) ?>" alt="Current memory"><label><input type="checkbox" name="remove_image"> Remove current image</label></div><?php endif; ?><div class="upload-box compact"><input type="file" id="memory" name="memory" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-image-preview><label for="memory" class="upload-label"><i class="fa-solid fa-camera"></i><span>Replace with another image</span></label><img id="imagePreview" class="image-preview" alt="Selected image preview"></div></div>
    <button class="btn btn-primary btn-large" type="submit">Update My Moment ✦</button>
</form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>