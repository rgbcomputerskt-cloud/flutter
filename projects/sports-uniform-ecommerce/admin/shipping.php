<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (($_POST['do'] ?? '') === 'delete') {
        q('DELETE FROM shipping_methods WHERE id = ?', [(int)$_POST['id']]);
        flash('Shipping method deleted.'); redirect('admin/shipping.php');
    }
    $id = (int)($_POST['id'] ?? 0);
    $free = $_POST['free_over'] === '' ? null : (float)$_POST['free_over'];
    $data = [trim($_POST['name'] ?? ''), trim($_POST['region'] ?? ''), (float)($_POST['rate'] ?? 0), $free, trim($_POST['delivery_time'] ?? ''), isset($_POST['is_active'])?1:0];
    if ($id) { q('UPDATE shipping_methods SET name=?,region=?,rate=?,free_over=?,delivery_time=?,is_active=? WHERE id=?', [...$data, $id]); flash('Shipping method updated.'); }
    else     { q('INSERT INTO shipping_methods (name,region,rate,free_over,delivery_time,is_active) VALUES (?,?,?,?,?,?)', $data); flash('Shipping method created.'); }
    redirect('admin/shipping.php');
}

$editing = null;
if (isset($_GET['edit'])) $editing = fetch_one('SELECT * FROM shipping_methods WHERE id = ?', [(int)$_GET['edit']]);
elseif (isset($_GET['new'])) $editing = ['id'=>0,'name'=>'','region'=>'','rate'=>0,'free_over'=>'','delivery_time'=>'','is_active'=>1];

admin_head('Shipping');

if ($editing): ?>
  <div class="panel">
    <div class="panel__head"><h2><?= $editing['id']?'Edit':'New' ?> Shipping Method</h2><a class="btn btn--sm" href="<?= url('admin/shipping.php') ?>">← Back</a></div>
    <form class="aform" method="post" action="<?= url('admin/shipping.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
      <div class="aform__grid">
        <label>Name<input type="text" name="name" value="<?= e($editing['name']) ?>" required></label>
        <label>Region<input type="text" name="region" value="<?= e($editing['region']) ?>" required></label>
        <label>Rate<input type="number" step="0.01" name="rate" value="<?= e($editing['rate']) ?>"></label>
        <label>Free over <small>(blank = never)</small><input type="number" step="0.01" name="free_over" value="<?= e($editing['free_over']) ?>"></label>
        <label>Delivery time<input type="text" name="delivery_time" value="<?= e($editing['delivery_time']) ?>" placeholder="7-10 business days"></label>
        <label class="aform__check"><input type="checkbox" name="is_active" <?= $editing['is_active']?'checked':'' ?>> Active</label>
      </div>
      <button class="btn btn--brand btn--lg" type="submit">Save Method</button>
    </form>
  </div>
<?php else:
  $rows = fetch_all('SELECT * FROM shipping_methods ORDER BY region, rate'); ?>
  <div class="panel">
    <div class="panel__head"><h2>Shipping Methods</h2><a class="btn btn--brand" href="<?= url('admin/shipping.php?new=1') ?>">+ New Method</a></div>
    <table class="dtable">
      <thead><tr><th>Name</th><th>Region</th><th>Rate</th><th>Free over</th><th>Time</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><strong><?= e($r['name']) ?></strong></td>
            <td><?= e($r['region']) ?></td>
            <td><?= money($r['rate']) ?></td>
            <td><?= $r['free_over']!==null ? money($r['free_over']) : '—' ?></td>
            <td><?= e($r['delivery_time']) ?></td>
            <td><?= $r['is_active']?'<span class="pill pill--won">Yes</span>':'<span class="pill">No</span>' ?></td>
            <td class="nowrap">
              <a class="btn btn--sm" href="<?= url('admin/shipping.php?edit=' . (int)$r['id']) ?>">Edit</a>
              <form method="post" class="inline" action="<?= url('admin/shipping.php') ?>"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn--sm btn--danger" data-confirm="Delete this shipping method?">Delete</button></form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; admin_foot(); ?>
