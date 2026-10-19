<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (($_POST['do'] ?? '') === 'delete') {
        q('DELETE FROM faqs WHERE id = ?', [(int)$_POST['id']]);
        flash('FAQ deleted.'); redirect('admin/faqs.php');
    }
    $id = (int)($_POST['id'] ?? 0);
    $data = [trim($_POST['question'] ?? ''), trim($_POST['answer'] ?? ''), (int)($_POST['sort_order'] ?? 0), isset($_POST['is_active'])?1:0];
    if ($id) { q('UPDATE faqs SET question=?,answer=?,sort_order=?,is_active=? WHERE id=?', [...$data, $id]); flash('FAQ updated.'); }
    else     { q('INSERT INTO faqs (question,answer,sort_order,is_active) VALUES (?,?,?,?)', $data); flash('FAQ created.'); }
    redirect('admin/faqs.php');
}

$editing = null;
if (isset($_GET['edit'])) $editing = fetch_one('SELECT * FROM faqs WHERE id = ?', [(int)$_GET['edit']]);
elseif (isset($_GET['new'])) $editing = ['id'=>0,'question'=>'','answer'=>'','sort_order'=>0,'is_active'=>1];

admin_head('FAQs');

if ($editing): ?>
  <div class="panel">
    <div class="panel__head"><h2><?= $editing['id']?'Edit':'New' ?> FAQ</h2><a class="btn btn--sm" href="<?= url('admin/faqs.php') ?>">← Back</a></div>
    <form class="aform" method="post" action="<?= url('admin/faqs.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
      <div class="aform__grid">
        <label class="aform__full">Question<input type="text" name="question" value="<?= e($editing['question']) ?>" required></label>
        <label class="aform__full">Answer<textarea name="answer" rows="4" required><?= e($editing['answer']) ?></textarea></label>
        <label>Sort order<input type="number" name="sort_order" value="<?= (int)$editing['sort_order'] ?>"></label>
        <label class="aform__check"><input type="checkbox" name="is_active" <?= $editing['is_active']?'checked':'' ?>> Active</label>
      </div>
      <button class="btn btn--brand btn--lg" type="submit">Save FAQ</button>
    </form>
  </div>
<?php else:
  $rows = fetch_all('SELECT * FROM faqs ORDER BY sort_order, id'); ?>
  <div class="panel">
    <div class="panel__head"><h2>FAQs</h2><a class="btn btn--brand" href="<?= url('admin/faqs.php?new=1') ?>">+ New FAQ</a></div>
    <table class="dtable">
      <thead><tr><th>Question</th><th>Order</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><strong><?= e($r['question']) ?></strong></td>
            <td><?= (int)$r['sort_order'] ?></td>
            <td><?= $r['is_active']?'<span class="pill pill--won">Yes</span>':'<span class="pill">No</span>' ?></td>
            <td class="nowrap">
              <a class="btn btn--sm" href="<?= url('admin/faqs.php?edit=' . (int)$r['id']) ?>">Edit</a>
              <form method="post" class="inline" action="<?= url('admin/faqs.php') ?>"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn--sm btn--danger" data-confirm="Delete this FAQ?">Delete</button></form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; admin_foot(); ?>
