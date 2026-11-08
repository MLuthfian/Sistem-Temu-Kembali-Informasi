<?php

// Fix Symfony Base URL on Vercel so /api routes and root routes match cleanly
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Entry point Laravel untuk Vercel (runtime vercel-php).
// Filesystem Vercel bersifat read-only kecuali direktori /tmp.
$storage = '/tmp/storage';
foreach ([
    'app/public',
    'framework/cache/data',
    'framework/sessions',
    'framework/views',
    'logs',
] as $dir) {
    if (!is_dir("$storage/$dir")) {
        mkdir("$storage/$dir", 0777, true);
    }
}

// Salin database SQLite ke /tmp agar memiliki hak akses baca & tulis di Vercel
$dbSource = null;
if (file_exists(__DIR__ . '/../database/database.sqlite')) {
    $dbSource = __DIR__ . '/../database/database.sqlite';
} elseif (file_exists(__DIR__ . '/../data/medikarag.db')) {
    $dbSource = __DIR__ . '/../data/medikarag.db';
}

$dbTarget = '/tmp/database.sqlite';
if ($dbSource && (!file_exists($dbTarget) || filesize($dbTarget) === 0)) {
    copy($dbSource, $dbTarget);
} elseif (!file_exists($dbTarget)) {
    touch($dbTarget);
}

// Set environment variables dinamis untuk SQLite dan Vercel
putenv("VERCEL=1");
$_ENV['VERCEL'] = '1';
putenv("DB_DATABASE=$dbTarget");
$_ENV['DB_DATABASE'] = $dbTarget;
putenv("VIEW_COMPILED_PATH=/tmp/storage/framework/views");
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv("SESSION_DRIVER=cookie");
$_ENV['SESSION_DRIVER'] = 'cookie';
putenv("CACHE_STORE=array");
$_ENV['CACHE_STORE'] = 'array';

// Teruskan request ke public/index.php
require __DIR__ . '/../public/index.php';
