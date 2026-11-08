<?php

// Entry point Laravel untuk Vercel (runtime vercel-php).
// Filesystem Vercel read-only kecuali /tmp, jadi siapkan folder storage di sana.
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

require __DIR__ . '/../public/index.php';
