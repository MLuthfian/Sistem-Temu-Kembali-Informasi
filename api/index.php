<?php

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
$dbSource = __DIR__ . '/../database/database.sqlite';
$dbTarget = '/tmp/database.sqlite';
if (file_exists($dbSource) && (!file_exists($dbTarget) || filesize($dbTarget) === 0)) {
    copy($dbSource, $dbTarget);
}

// Set environment variables dinamis untuk SQLite dan Vercel
putenv("VERCEL=1");
$_ENV['VERCEL'] = '1';
putenv("DB_DATABASE=$dbTarget");
$_ENV['DB_DATABASE'] = $dbTarget;
putenv("VIEW_COMPILED_PATH=/tmp/storage/framework/views");
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Teruskan request ke public/index.php
require __DIR__ . '/../public/index.php';
