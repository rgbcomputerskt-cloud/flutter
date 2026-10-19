<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (($_POST['do'] ?? '') === 'delete') {
        q('DELETE FROM size_charts WHERE id = ?', [(int)$_POST['id']]);
        flash('Size chart deleted.'); redirect('admin/size-charts.php');
    }
    $id = (int)($_POST['id'] ?? 0);
    $cat = $_POST['category_id'] === '' ? null : (int)$_POST['category_id'];
    $data = [trim($_POST['name'] ?? ''), $cat, trim($_POST['unit'] ?? 'inches'), trim($_POST['content'] ?? ''), isset($_POST['is_active'])?1:0];
    if ($id) { q('UPDATE size_charts SET name=?,category_id=?,unit=?,content=?,is_active=? WHERE id=?', [...$data, $id]); flash('Size chart updated.'); }
    else     { q('INSERT INTO size_charts (name,category_id,unit,content,is_active) VALUES (?,?,?,?,?)', $data); flash('Size chart created.'); }
    redirect('admin/size-charts.php');
}

$cats = get_categories(false);
$editing = null;
if (isset($_GET['edit'])) $editing = fetch_one('SELECT * FROM size_charts WHERE id = ?', [(int)$_GET['edit']]);
elseif (isset($_GET['new'])) $editing = ['id'=>0,'name'=>'','category_id'=>'','unit'=>'inches','content'=>"<table>\n<thead><tr><th>Size</th><th>Chest</th><th>Length</th></tr></thead>\n<tbody>\n<tr><td>S</td><td>36-38</td><td>28</td></tr>\n</tbody>\n</table>",'is_active'=>1];

admin_head('Size Charts');

if ($editing): ?>
  <div class="panel">
    <div class="panel__head"><h2><?= $editing['id']?'Edit':'New' ?> Size Chart</h2><a class="btn btn--sm" href="<?= url('admin/size-charts.php') ?>">← Back</a></div>
    <form class="aform" method="post" action="<?= url('admin/size-charts.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
      <div class="aform__grid">
        <label>Name<input type="text" name="name" value="<?= e($editing['name']) ?>" required></label>
        <label>Category
          <select name="category_id">
            <option value="">— none —</option>
            <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)$editing['category_id']===(string)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </label>
        <label>Unit<input type="text" name="unit" value="<?= e($editing['unit']) ?>" placeholder="inches / cm"></label>
        <label class="aform__check"><input type="checkbox" name="is_active" <?= $editing['is_active']?'checked':'' ?>> Active</label>
        <label class="aform__full">Table HTML <small>(a standard &lt;table&gt; — rendered as-is)</small><textarea name="content" rows="10" class="mono"><?= e($editing['content']) ?></textarea></label>
      </div>
      <button class="btn btn--brand btn--lg" type="submit">Save Size Chart</button>
    </form>
  </div>
<?php else:
  $rows = fetch_all('SELECT sc.*, c.name AS cat FROM size_charts sc LEFT JOIN categories c ON c.id=sc.category_id ORDER BY sc.id'); ?>
  <div class="panel">
    <div class="panel__head"><h2>Size Charts</h2><a class="btn btn--brand" href="<?= url('admin/size-charts.php?new=1') ?>">+ New Size Chart</a></div>
    <table class="dtable">
      <thead><tr><th>Name</th><th>Category</th><th>Unit</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><strong><?= e($r['name']) ?></strong></td>
            <td><?= e($r['cat']) ?: '—' ?></td>
            <td><?= e($r['unit']) ?></td>
            <td><?= $r['is_active']?'<span class="pill pill--won">Yes</span>':'<span class="pill">No</span>' ?></td>
            <td class="nowrap">
              <a class="btn btn--sm" href="<?= url('admin/size-charts.php?edit=' . (int)$r['id']) ?>">Edit</a>
              <form method="post" class="inline" action="<?= url('admin/size-charts.php') ?>"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn--sm btn--danger" data-confirm="Delete this size chart?">Delete</button></form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; admin_foot(); ?>
