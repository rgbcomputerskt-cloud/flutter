<?php
require_once __DIR__ . '/includes/functions.php';

// AJAX fragment for the product-page "Size chart" modal
if (isset($_GET['id']) && isset($_GET['ajax'])) {
    $sc = fetch_one('SELECT * FROM size_charts WHERE id = ? AND is_active = 1', [(int)$_GET['id']]);
    if (!$sc) { echo '<p>Size chart not found.</p>'; exit; }
    echo '<h2 style="margin-top:0">' . e($sc['name']) . ' <small>(' . e($sc['unit']) . ')</small></h2>';
    echo '<div class="sizetable">' . $sc['content'] . '</div>';
    exit;
}

$charts = fetch_all('SELECT sc.*, c.name AS cat_name FROM size_charts sc LEFT JOIN categories c ON c.id = sc.category_id WHERE sc.is_active = 1 ORDER BY sc.id');
$pageTitle = 'Size Charts';
include __DIR__ . '/includes/header.php';
?>
<section class="cathero" style="--c:<?= e(get_setting('primary_color')) ?>">
  <div class="container">
    <nav class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <span>Size Charts</span></nav>
    <h1>Size Charts</h1>
    <p>Measurements are a guide — for custom team orders we can tailor every size. All measurements in the unit shown.</p>
  </div>
</section>

<div class="container sizepage">
  <?php foreach ($charts as $sc): ?>
    <div class="sizepage__block">
      <h2><?= e($sc['name']) ?> <small>(<?= e($sc['unit']) ?><?= $sc['cat_name'] ? ' · ' . e($sc['cat_name']) : '' ?>)</small></h2>
      <div class="sizetable"><?= $sc['content'] ?></div>
    </div>
  <?php endforeach; ?>
  <?php if (!$charts): ?><div class="empty"><h3>No size charts yet</h3></div><?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
