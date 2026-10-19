<?php
/** Admin bootstrap: auth, helpers, layout. */
require_once __DIR__ . '/../../includes/functions.php';

/* ---- Upload helper ---- */
function handle_upload(string $field, string $fallback = ''): string
{
    // text path provided (e.g. assets/images/...) takes over if no file
    $textPath = trim($_POST[$field . '_path'] ?? '');

    if (!empty($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','webp','gif','svg'];
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            flash('Unsupported image type: .' . e($ext), 'error');
            return $fallback;
        }
        if (!is_dir(UPLOAD_DIR)) { @mkdir(UPLOAD_DIR, 0775, true); }
        $name = date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        $dest = UPLOAD_DIR . '/' . $name;
        if (@move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
            return 'assets/uploads/' . $name;
        }
        flash('Could not save upload (check folder permissions). Used path instead.', 'error');
    }
    return $textPath !== '' ? $textPath : $fallback;
}

/* ---- Layout ---- */
function admin_head(string $title): void
{
    require_admin();
    $u = admin_user();
    $cur = basename($_SERVER['SCRIPT_NAME']);
    $nav = [
        'index.php'       => ['Dashboard', '▤'],
        'sliders.php'     => ['Sliders', '▭'],
        'categories.php'  => ['Categories', '▦'],
        'products.php'    => ['Products', '▣'],
        'variations.php'  => ['Variations', '◨'],
        'size-charts.php' => ['Size Charts', '📏'],
        'quotes.php'      => ['Quotes / Inquiries', '✉'],
        'shipping.php'    => ['Shipping', '🚚'],
        'faqs.php'        => ['FAQs', '?'],
        'settings.php'    => ['Settings', '⚙'],
    ];
    ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> — <?= e(get_setting('site_name')) ?> CMS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head><body class="adm">
<aside class="adm__side">
  <a class="adm__brand" href="<?= url('admin/index.php') ?>">◆ <?= e(get_setting('logo_text')) ?> <span>CMS</span></a>
  <nav class="adm__nav">
    <?php foreach ($nav as $file => [$label, $ico]): ?>
      <a class="<?= $cur === $file ? 'is-active' : '' ?>" href="<?= url('admin/' . $file) ?>"><span class="adm__ico"><?= $ico ?></span><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <a class="adm__view" href="<?= url('index.php') ?>" target="_blank">↗ View store</a>
</aside>
<div class="adm__main">
  <header class="adm__top">
    <h1><?= e($title) ?></h1>
    <div class="adm__user"><span><?= e($u['name']) ?></span><a href="<?= url('admin/logout.php') ?>">Logout</a></div>
  </header>
  <div class="adm__content">
    <?php foreach (get_flashes() as $f): ?>
      <div class="adm__flash adm__flash--<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
    <?php endforeach; ?>
<?php }

function admin_foot(): void { ?>
  </div>
</div>
<script>
// confirm deletes
document.querySelectorAll('[data-confirm]').forEach(function(el){
  el.addEventListener('click', function(e){ if(!confirm(el.getAttribute('data-confirm'))) e.preventDefault(); });
});
</script>
</body></html>
<?php }
