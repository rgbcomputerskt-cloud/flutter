<?php
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$p  = fetch_one('SELECT * FROM products WHERE id = ? AND is_active = 1', [$id]);
if (!$p) { http_response_code(404); echo '<div class="qv-loading">Product not found.</div>'; exit; }

$cat     = $p['category_id'] ? fetch_one('SELECT name, color, slug FROM categories WHERE id = ?', [$p['category_id']]) : null;
$varsRaw = fetch_all('SELECT * FROM product_variations WHERE product_id = ? ORDER BY attribute, sort_order, id', [$p['id']]);
$variations = [];
foreach ($varsRaw as $v) { $variations[$v['attribute']][] = $v; }
?>
<div class="qv">
  <div class="qv__media"><img src="<?= e(img($p['main_image'])) ?>" alt="<?= e($p['name']) ?>"></div>
  <div class="qv__body">
    <?php if ($cat): ?><a class="qv__cat" style="color:<?= e($cat['color']) ?>" href="<?= url('category.php?cat='.e($cat['slug'])) ?>"><?= e($cat['name']) ?></a><?php endif; ?>
    <h2 class="qv__title"><?= e($p['name']) ?></h2>
    <div class="qv__price">from <?= money($p['price_from']) ?> <span>/ unit · MOQ <?= (int)$p['min_order'] ?></span></div>
    <p class="qv__desc"><?= e($p['short_desc']) ?></p>

    <form method="post" action="<?= url('inquiry.php?action=add') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
      <?php foreach ($variations as $attr => $opts): ?>
        <div class="qv__opt">
          <label><?= e($attr) ?></label>
          <select name="var[<?= e($attr) ?>]">
            <?php foreach ($opts as $o): ?>
              <option value="<?= e($o['value']) ?>"><?= e($o['value']) ?><?= $o['extra_cost']>0?' (+'.money($o['extra_cost']).')':'' ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endforeach; ?>
      <div class="qv__row">
        <input type="number" name="quantity" value="<?= (int)$p['min_order'] ?>" min="1" class="qv__qty">
        <button type="submit" class="btn btn--brand">Add to Inquiry</button>
      </div>
    </form>
    <a class="qv__full" href="<?= url('product.php?slug='.e($p['slug'])) ?>">View full details →</a>
  </div>
</div>
