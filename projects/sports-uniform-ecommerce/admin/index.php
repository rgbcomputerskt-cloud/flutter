<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

$stats = [
    'Products'    => ['products.php',   (int)fetch_col('SELECT COUNT(*) FROM products'),   '▣'],
    'Categories'  => ['categories.php', (int)fetch_col('SELECT COUNT(*) FROM categories'), '▦'],
    'Sliders'     => ['sliders.php',    (int)fetch_col('SELECT COUNT(*) FROM sliders'),    '▭'],
    'New Inquiries' => ['quotes.php',   (int)fetch_col("SELECT COUNT(*) FROM quotes WHERE status='new'"), '✉'],
];
$recent = fetch_all('SELECT * FROM quotes ORDER BY created_at DESC LIMIT 6');

admin_head('Dashboard');
?>
<div class="statrow">
  <?php foreach ($stats as $label => [$link, $val, $ico]): ?>
    <a class="statcard" href="<?= url('admin/' . $link) ?>">
      <span class="statcard__ico"><?= $ico ?></span>
      <span class="statcard__val"><?= $val ?></span>
      <span class="statcard__label"><?= e($label) ?></span>
    </a>
  <?php endforeach; ?>
</div>

<div class="panel">
  <div class="panel__head"><h2>Recent Inquiries</h2><a class="btn btn--sm" href="<?= url('admin/quotes.php') ?>">View all</a></div>
  <table class="dtable">
    <thead><tr><th>Ref</th><th>Name</th><th>Company</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($recent as $r): ?>
        <tr>
          <td><strong><?= e($r['ref']) ?></strong></td>
          <td><?= e($r['name']) ?></td>
          <td><?= e($r['company']) ?: '—' ?></td>
          <td><span class="pill pill--<?= e($r['status']) ?>"><?= e(str_replace('_',' ',$r['status'])) ?></span></td>
          <td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td>
          <td><a class="btn btn--sm" href="<?= url('admin/quotes.php?view=' . (int)$r['id']) ?>">Open</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$recent): ?><tr><td colspan="6" class="muted">No inquiries yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="panel">
  <div class="panel__head"><h2>Quick actions</h2></div>
  <div class="quickactions">
    <a class="btn btn--brand" href="<?= url('admin/products.php?new=1') ?>">+ New Product</a>
    <a class="btn" href="<?= url('admin/categories.php?new=1') ?>">+ New Category</a>
    <a class="btn" href="<?= url('admin/sliders.php?new=1') ?>">+ New Slide</a>
    <a class="btn" href="<?= url('admin/size-charts.php?new=1') ?>">+ New Size Chart</a>
    <a class="btn" href="<?= url('admin/settings.php') ?>">Edit Settings</a>
  </div>
</div>
<?php admin_foot(); ?>
