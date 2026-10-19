<?php
require_once __DIR__ . '/includes/functions.php';

$cats       = get_categories();
$activeSlug = $_GET['cat'] ?? '';
$activeCat  = $activeSlug ? get_category($activeSlug) : null;
$q          = trim($_GET['q'] ?? '');
$sort       = $_GET['sort'] ?? 'newest';
$featured   = isset($_GET['featured']) && $_GET['featured'] == '1';
$minP       = isset($_GET['min']) && $_GET['min'] !== '' ? (float)$_GET['min'] : null;
$maxP       = isset($_GET['max']) && $_GET['max'] !== '' ? (float)$_GET['max'] : null;

// ---- Build query ----
$where  = ['p.is_active = 1'];
$params = [];
if ($activeCat) { $where[] = 'p.category_id = ?'; $params[] = $activeCat['id']; }
if ($q !== '')  { $where[] = '(p.name LIKE ? OR p.short_desc LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($featured)  { $where[] = 'p.is_featured = 1'; }
if ($minP !== null) { $where[] = 'p.price_from >= ?'; $params[] = $minP; }
if ($maxP !== null) { $where[] = 'p.price_from <= ?'; $params[] = $maxP; }

$order = match ($sort) {
    'price-asc'  => 'p.price_from ASC',
    'price-desc' => 'p.price_from DESC',
    'name'       => 'p.name ASC',
    default      => 'p.created_at DESC, p.id DESC',
};

$sql = 'SELECT p.* FROM products p WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order;
$products = fetch_all($sql, $params);

$pageTitle = $activeCat ? $activeCat['name'] : 'Shop All';
include __DIR__ . '/includes/header.php';

/** Helper to keep current filters while changing one param */
function flt(array $overrides = []): string {
    $base = array_filter([
        'cat'      => $_GET['cat'] ?? null,
        'q'        => $_GET['q'] ?? null,
        'sort'     => $_GET['sort'] ?? null,
        'featured' => isset($_GET['featured']) ? '1' : null,
        'min'      => $_GET['min'] ?? null,
        'max'      => $_GET['max'] ?? null,
    ], fn($v) => $v !== null && $v !== '');
    $base = array_merge($base, $overrides);
    $base = array_filter($base, fn($v) => $v !== null && $v !== '');
    return url('category.php' . ($base ? '?' . http_build_query($base) : ''));
}
?>

<section class="cathero" style="--c:<?= e($activeCat['color'] ?? get_setting('primary_color')) ?>">
  <div class="container">
    <nav class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <a href="<?= url('category.php') ?>">Shop</a><?= $activeCat ? ' / <span>' . e($activeCat['name']) . '</span>' : '' ?></nav>
    <h1><?= e($activeCat['name'] ?? 'Shop All Products') ?></h1>
    <p><?= e($activeCat['description'] ?? 'Browse the full catalog of custom sports uniforms and apparel.') ?></p>
  </div>
</section>

<div class="container shoplayout">
  <!-- ===== Vertical colorful menu + filters ===== -->
  <aside class="filters">
    <div class="filters__block">
      <h3 class="filters__title">Categories</h3>
      <ul class="catmenu">
        <li><a class="catmenu__item <?= !$activeCat ? 'is-active' : '' ?>" href="<?= flt(['cat' => null]) ?>" style="--c:#64748b"><span class="catmenu__bar"></span>All Products</a></li>
        <?php foreach ($cats as $c): ?>
          <li>
            <a class="catmenu__item <?= $activeSlug === $c['slug'] ? 'is-active' : '' ?>" href="<?= flt(['cat' => $c['slug']]) ?>" style="--c:<?= e($c['color']) ?>">
              <span class="catmenu__bar"></span>
              <?= e($c['name']) ?>
              <span class="catmenu__count"><?= product_count_in((int)$c['id']) ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <form class="filters__block" method="get" action="<?= url('category.php') ?>">
      <?php if ($activeSlug): ?><input type="hidden" name="cat" value="<?= e($activeSlug) ?>"><?php endif; ?>
      <h3 class="filters__title">Search in catalog</h3>
      <input class="filters__search" type="search" name="q" value="<?= e($q) ?>" placeholder="Keyword…">

      <h3 class="filters__title">Price (from)</h3>
      <div class="filters__price">
        <input type="number" name="min" min="0" step="1" value="<?= e($_GET['min'] ?? '') ?>" placeholder="Min">
        <span>—</span>
        <input type="number" name="max" min="0" step="1" value="<?= e($_GET['max'] ?? '') ?>" placeholder="Max">
      </div>

      <label class="filters__check">
        <input type="checkbox" name="featured" value="1" <?= $featured ? 'checked' : '' ?>> Featured only
      </label>

      <h3 class="filters__title">Sort by</h3>
      <select name="sort" class="filters__select">
        <option value="newest"     <?= $sort==='newest'?'selected':'' ?>>Newest</option>
        <option value="price-asc"  <?= $sort==='price-asc'?'selected':'' ?>>Price: low to high</option>
        <option value="price-desc" <?= $sort==='price-desc'?'selected':'' ?>>Price: high to low</option>
        <option value="name"       <?= $sort==='name'?'selected':'' ?>>Name A–Z</option>
      </select>

      <button type="submit" class="btn btn--brand btn--block">Apply filters</button>
      <a href="<?= url('category.php') ?>" class="filters__reset">Reset all</a>
    </form>

    <div class="filters__promo">
      <strong>Need something custom?</strong>
      <p>We make to order from just 10 pieces.</p>
      <a class="btn btn--dark btn--block" href="<?= url('inquiry.php') ?>">Get a Quote</a>
    </div>
  </aside>

  <!-- ===== Results ===== -->
  <div class="shopmain">
    <div class="shopmain__bar">
      <span><strong><?= count($products) ?></strong> product<?= count($products)===1?'':'s' ?></span>
      <?php if ($q !== ''): ?><span class="shopmain__q">for “<?= e($q) ?>”</span><?php endif; ?>
    </div>

    <?php if ($products): ?>
      <div class="pgrid">
        <?php foreach ($products as $p) include __DIR__ . '/includes/product-card.php'; ?>
      </div>
    <?php else: ?>
      <div class="empty">
        <h3>No products match your filters</h3>
        <p>Try widening your search or <a href="<?= url('category.php') ?>">reset the filters</a>.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
