<?php
/**
 * Dynamic XML sitemap.
 */
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [];

$staticPages = [
    'index.php'      => '1.0',
    'khoa-hoc.php'   => '0.9',
    'giang-vien.php' => '0.8',
    'thu-vien.php'   => '0.8',
    'cong-dong.php'  => '0.6',
    've-chung-toi.php' => '0.6',
    'lien-he.php'    => '0.6',
    'register.php'   => '0.4',
    'login.php'      => '0.3',
];

foreach ($staticPages as $page => $priority) {
    $urls[] = [
        'loc'      => absoluteUrl($page),
        'lastmod'  => date('Y-m-d'),
        'priority' => $priority,
        'freq'     => 'weekly',
    ];
}

try {
    foreach (dbAll('SELECT slug, created_at FROM courses WHERE is_active = 1') as $row) {
        $urls[] = [
            'loc'      => absoluteUrl('khoa-hoc-chi-tiet.php?slug=' . $row['slug']),
            'lastmod'  => date('Y-m-d', strtotime((string) $row['created_at'])),
            'priority' => '0.8',
            'freq'     => 'weekly',
        ];
    }
    foreach (dbAll('SELECT slug FROM instructors WHERE is_active = 1') as $row) {
        $urls[] = [
            'loc'      => absoluteUrl('giang-vien-chi-tiet.php?slug=' . $row['slug']),
            'lastmod'  => date('Y-m-d'),
            'priority' => '0.6',
            'freq'     => 'monthly',
        ];
    }
    foreach (dbAll('SELECT slug, updated_at FROM posts WHERE is_published = 1') as $row) {
        $urls[] = [
            'loc'      => absoluteUrl('bai-viet-chi-tiet.php?slug=' . $row['slug']),
            'lastmod'  => date('Y-m-d', strtotime((string) $row['updated_at'])),
            'priority' => '0.7',
            'freq'     => 'monthly',
        ];
    }
} catch (Throwable $e) {
    // Database unavailable: still output the static portion of the sitemap.
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url): ?>
  <url>
    <loc><?= e($url['loc']) ?></loc>
    <lastmod><?= e($url['lastmod']) ?></lastmod>
    <changefreq><?= e($url['freq']) ?></changefreq>
    <priority><?= e($url['priority']) ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
