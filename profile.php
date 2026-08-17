<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Profile';
$errors = [];

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM entries WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalEntries = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM entries WHERE user_id = ? AND file_name IS NOT NULL AND file_name <> ''");
$stmt->execute([$_SESSION['user_id']]);
$totalMemories = (int)$stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || strlen($name) < 2) $errors[] = 'Please enter a valid name.';
        $newProfile = $user['profile_image'];

        if (!$errors && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['profile_image'];
            if ($file['error'] !== UPLOAD_ERR_OK) $errors[] = 'Profile image upload failed.';
            elseif ($file['size'] > 3 * 1024 * 1024) $errors[] = 'Profile image must be 3 MB or less.';
            else {
                $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                if (!isset($allowed[$mime])) $errors[] = 'Only JPG, PNG and WEBP profile images are allowed.';
                else {
                    $newProfile = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $newProfile)) {
                        $errors[] = 'Could not save the profile image.';
                    } elseif ($user['profile_image']) {
                        $old = __DIR__ . '/uploads/' . basename($user['profile_image']);
                        if (is_file($old)) @unlink($old);
                    }
                }
            }
        }

        if (!$errors) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, profile_image = ? WHERE user_id = ?");
            $stmt->execute([$name, $newProfile, $_SESSION['user_id']]);
            $_SESSION['user_name'] = $name;
            $_SESSION['flash'] = ['type'=>'success','message'=>'Profile updated successfully.'];
            header('Location: profile.php'); exit;
        }
    }

    if ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password'])) $errors[] = 'Your current password is incorrect.';
        if (strlen($new) < 8) $errors[] = 'New password must contain at least 8 characters.';
        if ($new !== $confirm) $errors[] = 'New passwords do not match.';

        if (!$errors) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->execute([$hash, $_SESSION['user_id']]);
            $_SESSION['flash'] = ['type'=>'success','message'=>'Password changed successfully.'];
            header('Location: profile.php'); exit;
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

require __DIR__ . '/includes/header.php';
?>
<section class="dashboard-head reveal"><div><span class="eyebrow">YOUR EUNOIA PROFILE</span><h1>Your personal <em>space.</em></h1><p>Manage your details and keep your account secure.</p></div></section>
<?php if ($errors): ?><div class="form-alert error profile-alert"><i class="fa-solid fa-circle-exclamation"></i><div><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div></div><?php endif; ?>
<div class="profile-grid">
<section class="profile-card reveal">
    <div class="profile-top">
        <div class="avatar"><?php if ($user['profile_image']): ?><img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile picture"><?php else: ?><?= strtoupper(substr($user['name'],0,1)) ?><?php endif; ?></div>
        <div><h2><?= htmlspecialchars($user['name']) ?></h2><p><?= htmlspecialchars($user['email']) ?></p><small>Member since <?= date('M Y', strtotime($user['created_at'])) ?></small></div>
    </div>
    <div class="profile-stats"><div><strong><?= $totalEntries ?></strong><span>Journal Entries</span></div><div><strong><?= $totalMemories ?></strong><span>Memories</span></div></div>
    <form method="POST" enctype="multipart/form-data" data-validate>
        <input type="hidden" name="action" value="profile">
        <div class="field"><label for="name">Name</label><input class="plain-input" type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required minlength="2"></div>
        <div class="field"><label for="profile_image">Profile Image <small>(max 3 MB)</small></label><input class="plain-input file-input" type="file" id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></div>
        <button class="btn btn-primary" type="submit">Save Profile</button>
    </form>
</section>
<section class="profile-card reveal">
    <div class="section-heading"><div><span class="eyebrow">ACCOUNT SECURITY</span><h2>Change password</h2></div></div>
    <form method="POST" data-validate>
        <input type="hidden" name="action" value="password">
        <div class="field"><label for="current_password">Current Password</label><div class="input-wrap"><i class="fa-solid fa-lock"></i><input type="password" id="current_password" name="current_password" required><button type="button" class="password-toggle" data-target="current_password"><i class="fa-regular fa-eye"></i></button></div></div>
        <div class="field"><label for="new_password">New Password</label><div class="input-wrap"><i class="fa-solid fa-key"></i><input type="password" id="new_password" name="new_password" minlength="8" required><button type="button" class="password-toggle" data-target="new_password"><i class="fa-regular fa-eye"></i></button></div></div>
        <div class="field"><label for="confirm_password">Confirm New Password</label><div class="input-wrap"><i class="fa-solid fa-shield"></i><input type="password" id="confirm_password" name="confirm_password" minlength="8" required><button type="button" class="password-toggle" data-target="confirm_password"><i class="fa-regular fa-eye"></i></button></div></div>
        <button class="btn btn-outline" type="submit">Update Password</button>
    </form>
    <div class="security-note"><i class="fa-solid fa-shield-halved"></i><div><strong>Your password is protected</strong><p>EUNOIA stores passwords using PHP's secure password hashing functions.</p></div></div>
</section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>