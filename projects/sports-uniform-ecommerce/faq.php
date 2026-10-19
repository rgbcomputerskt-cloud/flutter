<?php
require_once __DIR__ . '/includes/functions.php';
$faqs = fetch_all('SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order, id');
$pageTitle = 'FAQs';
include __DIR__ . '/includes/header.php';
?>
<section class="cathero" style="--c:<?= e(get_setting('primary_color')) ?>">
  <div class="container">
    <nav class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <span>FAQs</span></nav>
    <h1>Frequently Asked Questions</h1>
    <p>Answers to the questions we hear most. Can't find yours? <a href="<?= url('inquiry.php') ?>" style="color:#fff;text-decoration:underline">Ask our team →</a></p>
  </div>
</section>

<div class="container faqpage">
  <div class="faqpage__list">
    <?php foreach ($faqs as $f): ?>
      <details class="faq">
        <summary><?= e($f['question']) ?><span class="faq__ico">+</span></summary>
        <div class="faq__a"><?= nl2br(e($f['answer'])) ?></div>
      </details>
    <?php endforeach; ?>
  </div>
  <aside class="faqpage__cta">
    <h3>Still have questions?</h3>
    <p>Our team replies within one business day with mockups and pricing.</p>
    <a class="btn btn--brand btn--block" href="<?= url('inquiry.php') ?>">Get a Quote</a>
    <a class="btn btn--ghost btn--block" href="mailto:<?= e(get_setting('contact_email')) ?>">Email us</a>
  </aside>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
