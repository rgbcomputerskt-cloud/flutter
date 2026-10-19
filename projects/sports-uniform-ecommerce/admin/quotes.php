<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $do = $_POST['do'] ?? '';
    if ($do === 'delete') {
        q('DELETE FROM quotes WHERE id = ?', [(int)$_POST['id']]);
        flash('Inquiry deleted.'); redirect('admin/quotes.php');
    }
    if ($do === 'status') {
        q('UPDATE quotes SET status = ? WHERE id = ?', [$_POST['status'] ?? 'new', (int)$_POST['id']]);
        flash('Status updated.'); redirect('admin/quotes.php?view=' . (int)$_POST['id']);
    }
}

$statuses = ['new','in_review','quoted','won','closed'];

// ---- Detail view ----
if (isset($_GET['view'])) {
    $quote = fetch_one('SELECT * FROM quotes WHERE id = ?', [(int)$_GET['view']]);
    if (!$quote) { flash('Inquiry not found.', 'error'); redirect('admin/quotes.php'); }
    $items = fetch_all('SELECT * FROM quote_items WHERE quote_id = ?', [$quote['id']]);
    admin_head('Inquiry ' . $quote['ref']); ?>
    <div class="panel">
      <div class="panel__head"><h2>Inquiry <?= e($quote['ref']) ?></h2><a class="btn btn--sm" href="<?= url('admin/quotes.php') ?>">← Back</a></div>
      <div class="quoteview">
        <div class="quoteview__meta">
          <dl>
            <dt>Name</dt><dd><?= e($quote['name']) ?></dd>
            <dt>Company</dt><dd><?= e($quote['company']) ?: '—' ?></dd>
            <dt>Email</dt><dd><a href="mailto:<?= e($quote['email']) ?>"><?= e($quote['email']) ?></a></dd>
            <dt>Phone</dt><dd><?= e($quote['phone']) ?: '—' ?></dd>
            <dt>Country</dt><dd><?= e($quote['country']) ?: '—' ?></dd>
            <dt>Received</dt><dd><?= e(date('M j, Y H:i', strtotime($quote['created_at']))) ?></dd>
          </dl>
          <?php if ($quote['message']): ?><div class="quoteview__msg"><strong>Message</strong><p><?= nl2br(e($quote['message'])) ?></p></div><?php endif; ?>
          <form method="post" class="quoteview__status" action="<?= url('admin/quotes.php') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="do" value="status"><input type="hidden" name="id" value="<?= (int)$quote['id'] ?>">
            <label>Status
              <select name="status">
                <?php foreach ($statuses as $s): ?><option value="<?= $s ?>" <?= $quote['status']===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option><?php endforeach; ?>
              </select>
            </label>
            <button class="btn btn--brand btn--sm" type="submit">Update</button>
          </form>
        </div>
        <div class="quoteview__items">
          <h3>Requested items</h3>
          <table class="dtable">
            <thead><tr><th>Product</th><th>Options</th><th>Qty</th><th>Notes</th></tr></thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <tr><td><strong><?= e($it['product_name']) ?></strong></td><td><?= e($it['variations']) ?: '—' ?></td><td><?= (int)$it['quantity'] ?></td><td><?= e($it['notes']) ?: '—' ?></td></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php admin_foot(); exit;
}

// ---- List ----
$filter = $_GET['status'] ?? '';
$where = $filter && in_array($filter, $statuses, true) ? 'WHERE status = ?' : '';
$rows = fetch_all("SELECT q.*, (SELECT COUNT(*) FROM quote_items qi WHERE qi.quote_id=q.id) AS n FROM quotes q $where ORDER BY created_at DESC", $where ? [$filter] : []);
admin_head('Quotes / Inquiries');
?>
<div class="panel">
  <div class="panel__head">
    <h2>Inquiries</h2>
    <div class="filterpills">
      <a class="pill <?= !$filter?'is-active':'' ?>" href="<?= url('admin/quotes.php') ?>">All</a>
      <?php foreach ($statuses as $s): ?><a class="pill pill--<?= $s ?> <?= $filter===$s?'is-active':'' ?>" href="<?= url('admin/quotes.php?status='.$s) ?>"><?= ucwords(str_replace('_',' ',$s)) ?></a><?php endforeach; ?>
    </div>
  </div>
  <table class="dtable">
    <thead><tr><th>Ref</th><th>Name</th><th>Company</th><th>Items</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><strong><?= e($r['ref']) ?></strong></td>
          <td><?= e($r['name']) ?><br><small class="muted"><?= e($r['email']) ?></small></td>
          <td><?= e($r['company']) ?: '—' ?></td>
          <td><?= (int)$r['n'] ?></td>
          <td><span class="pill pill--<?= e($r['status']) ?>"><?= e(str_replace('_',' ',$r['status'])) ?></span></td>
          <td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td>
          <td class="nowrap">
            <a class="btn btn--sm" href="<?= url('admin/quotes.php?view=' . (int)$r['id']) ?>">Open</a>
            <form method="post" class="inline" action="<?= url('admin/quotes.php') ?>"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn--sm btn--danger" data-confirm="Delete this inquiry?">Delete</button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="7" class="muted">No inquiries found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php admin_foot(); ?>
