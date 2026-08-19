<?php
/**
 * One-off helper: (re)set the default account passwords with freshly
 * generated bcrypt hashes for this PHP installation.
 *
 * Usage:  php setup-passwords.php
 *     or: open https://yourdomain.com/setup-passwords.php in a browser.
 *
 * DELETE THIS FILE after running it on a production server.
 */
require_once __DIR__ . '/includes/db.php';

$accounts = [
    'admin@musicofeveryone.com' => 'Admin@123456',
    'user@example.com'          => 'User@123456',
];

$isCli = PHP_SAPI === 'cli';
if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
}

$updated = 0;
foreach ($accounts as $email => $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $rows = dbQuery('UPDATE users SET password = ? WHERE email = ?', [$hash, $email])->rowCount();
    echo ($rows > 0 ? '[ok]   ' : '[skip] ') . $email . ' -> ' . $password . PHP_EOL;
    $updated += $rows;
}

echo PHP_EOL . $updated . ' account(s) updated.' . PHP_EOL;
echo 'Remember to delete setup-passwords.php once you are done.' . PHP_EOL;
