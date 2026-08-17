<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$entryId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$entryId) {
    $_SESSION['flash'] = ['type'=>'error','message'=>'Invalid entry.'];
    header('Location: dashboard.php'); exit;
}

$stmt = $pdo->prepare("SELECT file_name FROM entries WHERE entry_id = ? AND user_id = ?");
$stmt->execute([$entryId, $_SESSION['user_id']]);
$entry = $stmt->fetch();

if (!$entry) {
    $_SESSION['flash'] = ['type'=>'error','message'=>'Entry not found or you do not have permission to delete it.'];
    header('Location: dashboard.php'); exit;
}

$stmt = $pdo->prepare("DELETE FROM entries WHERE entry_id = ? AND user_id = ?");
$stmt->execute([$entryId, $_SESSION['user_id']]);

if ($stmt->rowCount() && $entry['file_name']) {
    $path = __DIR__ . '/uploads/' . basename($entry['file_name']);
    if (is_file($path)) @unlink($path);
}

$_SESSION['flash'] = ['type'=>'success','message'=>'The memory was removed from your journal.'];
header('Location: dashboard.php');
exit;
?>