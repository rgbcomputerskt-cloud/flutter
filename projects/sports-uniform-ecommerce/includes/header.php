<?php
require_once __DIR__ . '/functions.php';
$cats = get_categories();
$pageTitle = $pageTitle ?? get_setting('site_name', 'VANTAGE');
$metaDesc  = $metaDesc ?? get_setting('site_tagline', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?= e($metaDesc) ?>">
<title><?= e($pageTitle) ?> — <?= e(get_setting('site_name')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
<style>:root{--brand:<?= e(get_setting('primary_color','#e11d48')) ?>;--ink:<?= e(get_setting('accent_color','#0f172a')) ?>;}</style>
</head>
<body>

<!-- ===== Top bar ===== -->
<div class="topbar">
  <div class="container topbar__inner">
    <p class="topbar__msg"><?= e(get_setting('topbar_text')) ?></p>
    <div class="topbar__meta">
      <a href="tel:<?= e(get_setting('topbar_phone')) ?>">☎ <?= e(get_setting('topbar_phone')) ?></a>
      <a href="mailto:<?= e(get_setting('topbar_email')) ?>">✉ <?= e(get_setting('topbar_email')) ?></a>
      <span class="topbar__social">
        <a href="<?= e(get_setting('social_facebook','#')) ?>" aria-label="Facebook">Fb</a>
        <a href="<?= e(get_setting('social_instagram','#')) ?>" aria-label="Instagram">Ig</a>
        <a href="<?= e(get_setting('social_linkedin','#')) ?>" aria-label="LinkedIn">In</a>
      </span>
    </div>
  </div>
</div>

<!-- ===== Header ===== -->
<header class="header" id="siteHeader">
  <div class="container header__inner">
    <a class="logo" href="<?= url('index.php') ?>">
      <span class="logo__mark">◆</span><?= e(get_setting('logo_text','VANTAGE')) ?>
    </a>

    <nav class="mainnav" id="mainnav">
      <a href="<?= url('index.php') ?>">Home</a>
      <div class="mainnav__drop">
        <a href="<?= url('category.php') ?>">Shop <span class="caret">▾</span></a>
        <div class="mega">
          <?php foreach ($cats as $c): ?>
            <a class="mega__item" href="<?= url('category.php?cat=' . e($c['slug'])) ?>">
              <span class="mega__dot" style="background:<?= e($c['color']) ?>"></span>
              <span><strong><?= e($c['name']) ?></strong><small><?= e($c['tagline']) ?></small></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <a href="<?= url('category.php?cat=active-wear') ?>">Active</a>
      <a href="<?= url('category.php?cat=street-wear') ?>">Street</a>
      <a href="<?= url('size-chart.php') ?>">Size Chart</a>
      <a href="<?= url('faq.php') ?>">FAQs</a>
      <a href="<?= url('inquiry.php') ?>">Get a Quote</a>
    </nav>

    <div class="header__actions">
      <form class="searchbox" action="<?= url('search.php') ?>" method="get" role="search">
        <input type="search" name="q" placeholder="Search products…" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search">
        <button type="submit" aria-label="Search">⌕</button>
      </form>
      <a class="inquiry-btn" href="<?= url('inquiry.php') ?>">
        <span>Inquiry</span>
        <b class="inquiry-count"><?= inquiry_count() ?></b>
      </a>
      <button class="nav-toggle" id="navToggle" aria-label="Menu">☰</button>
    </div>
  </div>
</header>

<?php foreach (get_flashes() as $f): ?>
  <div class="flash flash--<?= e($f['type']) ?>"><div class="container"><?= e($f['msg']) ?></div></div>
<?php endforeach; ?>

<main>
