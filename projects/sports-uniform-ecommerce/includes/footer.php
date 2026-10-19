</main>

<!-- ===== Footer ===== -->
<footer class="footer">
  <div class="footer__cta">
    <div class="container footer__cta-inner">
      <div>
        <h3>Ready to kit out your team or launch your brand?</h3>
        <p>Free mockups • Low minimums • Factory-direct pricing</p>
      </div>
      <a class="btn btn--light" href="<?= url('inquiry.php') ?>">Request a Quote →</a>
    </div>
  </div>

  <div class="container footer__grid">
    <div class="footer__col footer__col--brand">
      <a class="logo logo--light" href="<?= url('index.php') ?>"><span class="logo__mark">◆</span><?= e(get_setting('logo_text','VANTAGE')) ?></a>
      <p><?= e(get_setting('footer_about')) ?></p>
      <div class="footer__social">
        <a href="<?= e(get_setting('social_facebook','#')) ?>">Facebook</a>
        <a href="<?= e(get_setting('social_instagram','#')) ?>">Instagram</a>
        <a href="<?= e(get_setting('social_linkedin','#')) ?>">LinkedIn</a>
        <a href="<?= e(get_setting('social_youtube','#')) ?>">YouTube</a>
      </div>
    </div>

    <div class="footer__col">
      <h4>Shop</h4>
      <ul>
        <?php foreach (array_slice(get_categories(), 0, 6) as $c): ?>
          <li><a href="<?= url('category.php?cat=' . e($c['slug'])) ?>"><?= e($c['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="footer__col">
      <h4>Company</h4>
      <ul>
        <li><a href="<?= url('index.php#about') ?>">About Us</a></li>
        <li><a href="<?= url('faq.php') ?>">FAQs</a></li>
        <li><a href="<?= url('size-chart.php') ?>">Size Charts</a></li>
        <li><a href="<?= url('inquiry.php') ?>">Get a Quote</a></li>
      </ul>
    </div>

    <div class="footer__col">
      <h4>Contact</h4>
      <ul class="footer__contact">
        <li><?= e(get_setting('contact_address')) ?></li>
        <li><a href="tel:<?= e(get_setting('contact_phone')) ?>"><?= e(get_setting('contact_phone')) ?></a></li>
        <li><a href="mailto:<?= e(get_setting('contact_email')) ?>"><?= e(get_setting('contact_email')) ?></a></li>
      </ul>
    </div>
  </div>

  <div class="footer__bar">
    <div class="container footer__bar-inner">
      <p>&copy; <?= date('Y') ?> <?= e(get_setting('site_name')) ?>. All rights reserved.</p>
      <p>Custom apparel manufacturing — <?= e(get_setting('site_tagline')) ?></p>
    </div>
  </div>
</footer>

<!-- Quick view modal shell -->
<div class="modal" id="quickview" aria-hidden="true">
  <div class="modal__overlay" data-close></div>
  <div class="modal__box" role="dialog" aria-modal="true">
    <button class="modal__close" data-close aria-label="Close">✕</button>
    <div class="modal__content" id="quickviewContent"><div class="qv-loading">Loading…</div></div>
  </div>
</div>

<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
