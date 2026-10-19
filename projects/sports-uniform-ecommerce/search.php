<?php
require_once __DIR__ . '/includes/functions.php';

$q = trim($_GET['q'] ?? '');
$products = [];
if ($q !== '') {
    $like = "%$q%";
    $products = fetch_all(
        'SELECT * FROM products WHERE is_active = 1 AND (name LIKE ? OR short_desc LIKE ? OR description LIKE ? OR sku LIKE ?) ORDER BY is_featured DESC, created_at DESC',
        [$like, $like, $like, $like]
    );
}

$pageTitle = 'Search';
include __DIR__ . '/includes/header.php';
?>
<section class="cathero" style="--c:<?= e(get_setting('primary_color')) ?>">
  <div class="container">
    <nav class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <span>Search</span></nav>
    <h1>Search Results</h1>
    <form class="searchband__form searchband__form--page" action="<?= url('search.php') ?>" method="get">
      <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search products…" autofocus>
      <button type="submit" class="btn btn--light">Search</button>
    </form>
  </div>
</section>

<div class="container" style="padding:36px 0 70px">
  <?php if ($q === ''): ?>
    <div class="empty"><h3>Type something to search</h3><p>Try a category, fabric or product name.</p></div>
  <?php elseif ($products): ?>
    <p class="shopmain__bar"><strong><?= count($products) ?></strong> result<?= count($products)===1?'':'s' ?> for “<?= e($q) ?>”</p>
    <div class="pgrid"><?php foreach ($products as $p) include __DIR__ . '/includes/product-card.php'; ?></div>
  <?php else: ?>
    <div class="empty"><h3>No results for “<?= e($q) ?>”</h3><p>Check the spelling or <a href="<?= url('category.php') ?>">browse the full catalog →</a></p></div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
