<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$p = fetch_one('SELECT * FROM products WHERE slug = ? AND is_active = 1', [$slug]);
if (!$p) { http_response_code(404); $pageTitle = 'Not found'; include __DIR__.'/includes/header.php'; echo '<div class="container empty" style="padding:80px 0"><h2>Product not found</h2><p><a href="'.url('category.php').'">Back to shop →</a></p></div>'; include __DIR__.'/includes/footer.php'; exit; }

$cat     = $p['category_id'] ? fetch_one('SELECT * FROM categories WHERE id = ?', [$p['category_id']]) : null;
$gallery = fetch_all('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, id', [$p['id']]);
$varsRaw = fetch_all('SELECT * FROM product_variations WHERE product_id = ? ORDER BY attribute, sort_order, id', [$p['id']]);
$related = fetch_all('SELECT * FROM products WHERE is_active = 1 AND category_id = ? AND id <> ? ORDER BY ' . sql_rand() . ' LIMIT 4', [$p['category_id'], $p['id']]);
$sizeChart = $p['category_id'] ? fetch_one('SELECT * FROM size_charts WHERE category_id = ? AND is_active = 1 LIMIT 1', [$p['category_id']]) : null;
if (!$sizeChart) { $sizeChart = fetch_one('SELECT * FROM size_charts WHERE is_active = 1 ORDER BY id LIMIT 1'); }

// group variations by attribute
$variations = [];
foreach ($varsRaw as $v) { $variations[$v['attribute']][] = $v; }

// gallery list (main image first)
$images = array_merge([$p['main_image']], array_map(fn($g) => $g['image'], $gallery));
$images = array_values(array_unique(array_filter($images)));

$pageTitle = $p['name'];
include __DIR__ . '/includes/header.php';
?>

<div class="container crumbs crumbs--page">
  <a href="<?= url('index.php') ?>">Home</a> / <a href="<?= url('category.php') ?>">Shop</a>
  <?php if ($cat): ?> / <a href="<?= url('category.php?cat=' . e($cat['slug'])) ?>"><?= e($cat['name']) ?></a><?php endif; ?>
  / <span><?= e($p['name']) ?></span>
</div>

<section class="product">
  <div class="container product__grid">

    <!-- ===== Gallery + cloud zoom ===== -->
    <div class="gallery">
      <div class="gallery__stage" id="zoom" data-zoom="<?= e(img($images[0] ?? '')) ?>">
        <img id="zoomImg" src="<?= e(img($images[0] ?? '')) ?>" alt="<?= e($p['name']) ?>">
        <span class="gallery__lens" id="zoomLens"></span>
      </div>
      <div class="gallery__result" id="zoomResult"></div>
      <?php if (count($images) > 1): ?>
        <div class="gallery__thumbs">
          <?php foreach ($images as $i => $im): ?>
            <button class="gallery__thumb <?= $i===0?'is-active':'' ?>" data-full="<?= e(img($im)) ?>">
              <img src="<?= e(img($im)) ?>" alt="View <?= $i+1 ?>">
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- ===== Details ===== -->
    <div class="pdetail">
      <?php if ($cat): ?><a class="pdetail__cat" style="color:<?= e($cat['color']) ?>" href="<?= url('category.php?cat='.e($cat['slug'])) ?>"><?= e($cat['name']) ?></a><?php endif; ?>
      <h1 class="pdetail__title"><?= e($p['name']) ?></h1>
      <div class="pdetail__price">from <?= money($p['price_from']) ?> <span>/ unit</span></div>
      <p class="pdetail__short"><?= e($p['short_desc']) ?></p>

      <ul class="pdetail__facts">
        <?php if ($p['sku']): ?><li><span>SKU</span><?= e($p['sku']) ?></li><?php endif; ?>
        <?php if ($p['fabric']): ?><li><span>Fabric</span><?= e($p['fabric']) ?></li><?php endif; ?>
        <li><span>Min. order</span><?= (int)$p['min_order'] ?> pieces</li>
        <li><span>Lead time</span>10–15 working days</li>
      </ul>

      <form class="pbuy" method="post" action="<?= url('inquiry.php?action=add') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">

        <?php foreach ($variations as $attr => $opts): ?>
          <div class="pbuy__opt">
            <label class="pbuy__label"><?= e($attr) ?>
              <?php if (strtolower($attr) === 'size' && $sizeChart): ?>
                <a class="pbuy__sizelink" href="#" data-sizechart="<?= (int)$sizeChart['id'] ?>">Size chart</a>
              <?php endif; ?>
            </label>
            <div class="chips">
              <?php foreach ($opts as $k => $o): ?>
                <label class="chip">
                  <input type="radio" name="var[<?= e($attr) ?>]" value="<?= e($o['value']) ?>" <?= $k===0?'checked':'' ?>>
                  <span><?= e($o['value']) ?><?= $o['extra_cost']>0 ? ' (+'.money($o['extra_cost']).')' : '' ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="pbuy__row">
          <div class="qtybox">
            <button type="button" class="qtybox__btn" data-step="-1">−</button>
            <input type="number" name="quantity" value="<?= (int)$p['min_order'] ?>" min="1" step="1">
            <button type="button" class="qtybox__btn" data-step="1">+</button>
          </div>
          <button type="submit" class="btn btn--brand btn--lg">Add to Inquiry</button>
        </div>
        <p class="pbuy__hint">No payment now — build an inquiry list and request a custom quote.</p>
      </form>

      <div class="pdetail__share">
        <a class="btn btn--dark" href="<?= url('inquiry.php') ?>">View Inquiry List →</a>
        <?php if ($sizeChart): ?><a class="btn btn--ghost" href="<?= url('size-chart.php') ?>">All Size Charts</a><?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- ===== Description / details tabs ===== -->
<section class="pinfo">
  <div class="container pinfo__inner">
    <div class="pinfo__tabs" role="tablist">
      <button class="is-active" data-tab="desc">Description</button>
      <?php if ($sizeChart): ?><button data-tab="size">Size Chart</button><?php endif; ?>
      <button data-tab="ship">Shipping &amp; MOQ</button>
    </div>
    <div class="pinfo__panel is-active" data-panel="desc">
      <p><?= nl2br(e($p['description'])) ?></p>
    </div>
    <?php if ($sizeChart): ?>
      <div class="pinfo__panel" data-panel="size">
        <h3><?= e($sizeChart['name']) ?> <small>(<?= e($sizeChart['unit']) ?>)</small></h3>
        <div class="sizetable"><?= $sizeChart['content'] /* trusted HTML from CMS */ ?></div>
      </div>
    <?php endif; ?>
    <div class="pinfo__panel" data-panel="ship">
      <p>Minimum order for this item is <strong><?= (int)$p['min_order'] ?> pieces</strong>. We ship worldwide by air and sea — see live rates on the <a href="<?= url('inquiry.php') ?>">quote page</a>.</p>
      <ul class="shiplist">
        <?php foreach (fetch_all('SELECT * FROM shipping_methods WHERE is_active = 1 ORDER BY rate') as $sm): ?>
          <li><strong><?= e($sm['name']) ?></strong> — <?= e($sm['region']) ?> · <?= money($sm['rate']) ?> · <?= e($sm['delivery_time']) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="prods">
  <div class="container">
    <header class="section-head"><span class="section-kicker">You may also like</span><h2 class="section-title">Related Products</h2></header>
    <div class="pgrid">
      <?php foreach ($related as $p) include __DIR__ . '/includes/product-card.php'; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
