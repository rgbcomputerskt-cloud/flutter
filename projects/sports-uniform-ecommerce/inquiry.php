<?php
require_once __DIR__ . '/includes/functions.php';

$action = $_GET['action'] ?? '';
$back   = $_POST['_back'] ?? ($_SERVER['HTTP_REFERER'] ?? url('category.php'));

/* ---- Add to inquiry ---- */
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $pid = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $parts = [];
    foreach (($_POST['var'] ?? []) as $attr => $val) {
        if ($val !== '') $parts[] = trim($attr) . ': ' . trim($val);
    }
    inquiry_add($pid, $qty, implode(' · ', $parts));
    flash('Added to your inquiry list.');
    redirect($back);
}

/* ---- Remove / clear ---- */
if ($action === 'remove') {
    inquiry_remove($_GET['key'] ?? '');
    flash('Item removed.');
    redirect('inquiry.php');
}
if ($action === 'clear') {
    inquiry_clear();
    flash('Inquiry list cleared.');
    redirect('inquiry.php');
}

/* ---- Update quantities ---- */
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    foreach (($_POST['qty'] ?? []) as $key => $q) {
        if (isset($_SESSION['inquiry'][$key])) {
            $_SESSION['inquiry'][$key]['quantity'] = max(1, (int)$q);
        }
    }
    flash('Quantities updated.');
    redirect('inquiry.php');
}

/* ---- Submit the quote ---- */
$done = null; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === '') {
    require_csrf();
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $items   = inquiry_items();

    if ($name === '')  $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
    if (!$items)       $errors[] = 'Your inquiry list is empty.';

    if (!$errors) {
        $ref = 'VN-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        q('INSERT INTO quotes (ref,name,company,email,phone,country,message) VALUES (?,?,?,?,?,?,?)',
          [$ref, $name, $company, $email, $phone, $country, $message]);
        $qid = (int) db()->lastInsertId();
        foreach ($items as $it) {
            q('INSERT INTO quote_items (quote_id,product_id,product_name,variations,quantity,notes) VALUES (?,?,?,?,?,?)',
              [$qid, $it['product_id'], $it['name'], $it['variations'], $it['quantity'], $it['notes'] ?? '']);
        }
        inquiry_clear();
        $done = $ref;
    }
}

$items = inquiry_items();
$pageTitle = 'Your Inquiry';
include __DIR__ . '/includes/header.php';
?>

<section class="cathero" style="--c:<?= e(get_setting('primary_color')) ?>">
  <div class="container">
    <nav class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <span>Inquiry</span></nav>
    <h1>Request a Quote</h1>
    <p>Build your list of products, tell us what you need, and our team will come back with a custom quote.</p>
  </div>
</section>

<div class="container inqwrap">
  <?php if ($done): ?>
    <div class="inqdone">
      <div class="inqdone__ico">✓</div>
      <h2>Inquiry sent!</h2>
      <p>Thank you — your reference is <strong><?= e($done) ?></strong>. Our team will be in touch within one business day.</p>
      <a class="btn btn--brand" href="<?= url('category.php') ?>">Continue browsing →</a>
    </div>
  <?php else: ?>

  <?php if ($errors): ?>
    <div class="formerrors"><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="inqgrid">
    <!-- items -->
    <div class="inqitems">
      <h2 class="inqitems__h">Your Items <span><?= count($items) ?></span></h2>
      <?php if ($items): ?>
        <form method="post" action="<?= url('inquiry.php?action=update') ?>">
          <?= csrf_field() ?>
          <table class="inqtable">
            <thead><tr><th>Product</th><th>Options</th><th>Qty</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($items as $key => $it): ?>
              <tr>
                <td class="inqtable__prod">
                  <img src="<?= e(img($it['image'])) ?>" alt="">
                  <div><strong><?= e($it['name']) ?></strong><small>from <?= money($it['price_from']) ?></small></div>
                </td>
                <td><?= e($it['variations']) ?: '<span class="muted">—</span>' ?></td>
                <td><input class="inqtable__qty" type="number" name="qty[<?= e($key) ?>]" value="<?= (int)$it['quantity'] ?>" min="1"></td>
                <td><a class="inqtable__rm" href="<?= url('inquiry.php?action=remove&key=' . urlencode($key)) ?>" aria-label="Remove">✕</a></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <div class="inqitems__actions">
            <button type="submit" class="btn btn--ghost">Update quantities</button>
            <a class="inqitems__clear" href="<?= url('inquiry.php?action=clear') ?>">Clear list</a>
          </div>
        </form>
      <?php else: ?>
        <div class="empty empty--sm">
          <p>Your inquiry list is empty.</p>
          <a class="btn btn--brand" href="<?= url('category.php') ?>">Browse products →</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- form -->
    <div class="inqform">
      <h2>Your Details</h2>
      <form method="post" action="<?= url('inquiry.php') ?>">
        <?= csrf_field() ?>
        <label>Full name *<input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required></label>
        <label>Company / Team<input type="text" name="company" value="<?= e($_POST['company'] ?? '') ?>"></label>
        <label>Email *<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></label>
        <label>Phone / WhatsApp<input type="text" name="phone" value="<?= e($_POST['phone'] ?? '') ?>"></label>
        <label>Country<input type="text" name="country" value="<?= e($_POST['country'] ?? '') ?>"></label>
        <label>Project details<textarea name="message" rows="4" placeholder="Quantities, colors, logos, deadlines…"><?= e($_POST['message'] ?? '') ?></textarea></label>
        <button type="submit" class="btn btn--brand btn--block btn--lg" <?= $items ? '' : 'disabled' ?>>Send Inquiry</button>
        <?php if (!$items): ?><p class="pbuy__hint">Add at least one product to send an inquiry.</p><?php endif; ?>
      </form>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
