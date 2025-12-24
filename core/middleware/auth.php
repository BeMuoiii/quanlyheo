<?php
// core/middleware/auth.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa đăng nhập → đá về login ngay lập tức
if (!isset($_SESSION['user_id'])) {
    $projectRootPath = str_replace('\\', '/', realpath(__DIR__ . '/../../'));
    $documentRoot    = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $relativeRoot    = '';

    if ($projectRootPath && $documentRoot && strpos($projectRootPath, $documentRoot) === 0) {
        $relativeRoot = substr($projectRootPath, strlen($documentRoot));
    }

    $relativeRoot = trim($relativeRoot, '/');
    $baseUrl = $relativeRoot === '' ? '' : '/' . $relativeRoot;

    header("Location: {$baseUrl}/public/index.php?url=login");
    exit;
}
?>