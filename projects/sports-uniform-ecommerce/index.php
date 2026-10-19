<?php
require_once __DIR__ . '/includes/functions.php';

$slides   = fetch_all('SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order, id');
$cats     = get_categories();
$featured = fetch_all('SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY created_at DESC, id DESC LIMIT 8');
$recent   = fetch_all('SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC, id DESC LIMIT 8');
$faqs     = fetch_all('SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order, id LIMIT 6');
$marquee  = get_setting('marquee_text');
$parallaxOn = get_setting('enable_parallax', '1') === '1';

$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>

<!-- ============ HERO SLIDER ============ -->
<section class="slider" id="slider">
  <div class="slider__track">
    <?php foreach ($slides as $i => $s): ?>
      <div class="slide <?= $i === 0 ? 'is-active' : '' ?>" style="background-image:url('<?= e(img($s['image'])) ?>')">
        <div class="container slide__inner">
          <div class="slide__content">
            <span class="slide__eyebrow"><?= e(get_setting('site_tagline')) ?></span>
            <h1 class="slide__title"><?= e($s['title']) ?></h1>
            <p class="slide__sub"><?= e($s['subtitle']) ?></p>
            <?php if ($s['button_text']): ?>
              <a class="btn btn--brand" href="<?= e(img($s['button_link'] ?: 'category.php')) ?>"><?= e($s['button_text']) ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($slides) > 1): ?>
    <button class="slider__nav slider__nav--prev" data-slide="prev" aria-label="Previous">‹</button>
    <button class="slider__nav slider__nav--next" data-slide="next" aria-label="Next">›</button>
    <div class="slider__dots">
      <?php foreach ($slides as $i => $s): ?>
        <button class="<?= $i === 0 ? 'is-active' : '' ?>" data-dot="<?= $i ?>" aria-label="Slide <?= $i+1 ?>"></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- ============ MARQUEE ============ -->
<div class="marquee" aria-hidden="true">
  <div class="marquee__track">
    <?php for ($i = 0; $i < 2; $i++): ?>
      <span><?= e($marquee) ?></span><span class="marquee__sep">◆</span>
    <?php endfor; ?>
    <?php for ($i = 0; $i < 2; $i++): ?>
      <span><?= e($marquee) ?></span><span class="marquee__sep">◆</span>
    <?php endfor; ?>
  </div>
</div>

<!-- ============ SEARCH BAND ============ -->
<section class="searchband">
  <div class="container searchband__inner">
    <div class="searchband__text">
      <h2>Find your kit</h2>
      <p>Search thousands of customizable styles across every category.</p>
    </div>
    <form class="searchband__form" action="<?= url('search.php') ?>" method="get">
      <input type="search" name="q" placeholder="e.g. football jersey, hoodie, leggings…" aria-label="Search products">
      <button type="submit" class="btn btn--brand">Search</button>
    </form>
  </div>
</section>

<!-- ============ MAGAZINE EDITORIAL INTRO ============ -->
<section class="mag" id="about">
  <div class="container mag__grid">
    <div class="mag__lead">
      <span class="section-kicker">Vol. 01 — The Line</span>
      <h2 class="mag__headline">Factory-direct apparel,<br><em>engineered</em> for the people who wear it.</h2>
    </div>
    <div class="mag__body">
      <p class="mag__drop">From the touchline to the street, <?= e(get_setting('site_name')) ?> manufactures custom sports uniforms, active wear, casual and street wear — made to your brand, your colors, your minimums.</p>
      <div class="mag__stats">
        <div><strong>500+</strong><span>Teams &amp; brands</span></div>
        <div><strong>10</strong><span>Piece minimum</span></div>
        <div><strong>10-15</strong><span>Day turnaround</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ TALL CATEGORY CARDS (vertical hollow text) ============ -->
<section class="cats">
  <div class="container">
    <header class="section-head">
      <span class="section-kicker">Browse</span>
      <h2 class="section-title">Shop by Category</h2>
    </header>
    <div class="cats__grid">
      <?php foreach ($cats as $i => $c): $count = product_count_in((int)$c['id']); ?>
        <a class="catcard <?= $i === 0 ? 'catcard--wide' : '' ?>" href="<?= url('category.php?cat=' . e($c['slug'])) ?>" style="--c:<?= e($c['color']) ?>">
          <img src="<?= e(img($c['image'])) ?>" alt="<?= e($c['name']) ?>" loading="lazy">
          <span class="catcard__vtext" aria-hidden="true"><?= e($c['name']) ?></span>
          <span class="catcard__overlay"></span>
          <span class="catcard__meta">
            <span class="catcard__name"><?= e($c['name']) ?></span>
            <span class="catcard__count"><?= $count ?> styles →</span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ FEATURED PRODUCTS ============ -->
<section class="prods prods--featured">
  <div class="container">
    <header class="section-head section-head--row">
      <div><span class="section-kicker">Hand-picked</span><h2 class="section-title">Featured Products</h2></div>
      <a class="link-arrow" href="<?= url('category.php') ?>">View all →</a>
    </header>
    <div class="pgrid">
      <?php foreach ($featured as $p) include __DIR__ . '/includes/product-card.php'; ?>
    </div>
  </div>
</section>

<?php if ($parallaxOn): ?>
<!-- ============ PARALLAX ============ -->
<section class="parallax">
  <div class="parallax__bg"></div>
  <div class="container parallax__inner">
    <span class="section-kicker section-kicker--light">Our Factory</span>
    <h2 class="parallax__title">Cut. Sewn. Printed.<br>Shipped worldwide.</h2>
    <p>Vertically-integrated production means tighter quality control, faster lead times and better pricing — with no middlemen between you and the people making your gear.</p>
    <a class="btn btn--light" href="<?= url('inquiry.php') ?>">Start your order →</a>
  </div>
</section>
<?php endif; ?>

<!-- ============ RECENT PRODUCTS ============ -->
<section class="prods">
  <div class="container">
    <header class="section-head section-head--row">
      <div><span class="section-kicker">Fresh off the line</span><h2 class="section-title">Recent Products</h2></div>
      <a class="link-arrow" href="<?= url('category.php') ?>">View all →</a>
    </header>
    <div class="pgrid">
      <?php foreach ($recent as $p) include __DIR__ . '/includes/product-card.php'; ?>
    </div>
  </div>
</section>

<!-- ============ FAQ TEASER ============ -->
<section class="faqs faqs--home">
  <div class="container faqs__grid">
    <div class="faqs__intro">
      <span class="section-kicker">Good to know</span>
      <h2 class="section-title">Frequently Asked</h2>
      <p>Everything you need to know before you order. Still stuck? <a href="<?= url('inquiry.php') ?>">Talk to us →</a></p>
    </div>
    <div class="faqs__list">
      <?php foreach ($faqs as $f): ?>
        <details class="faq">
          <summary><?= e($f['question']) ?><span class="faq__ico">+</span></summary>
          <div class="faq__a"><?= nl2br(e($f['answer'])) ?></div>
        </details>
      <?php endforeach; ?>
      <a class="link-arrow" href="<?= url('faq.php') ?>">All questions →</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
