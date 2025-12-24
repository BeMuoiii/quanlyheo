<?php
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base = ($scriptDir === '' || $scriptDir === '.') ? '' : $scriptDir;

header("Location: {$base}/public/index.php");
exit;