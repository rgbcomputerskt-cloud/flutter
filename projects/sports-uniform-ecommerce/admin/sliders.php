<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $do = $_POST['do'] ?? '';
    if ($do === 'delete') {
        q('DELETE FROM sliders WHERE id = ?', [(int)$_POST['id']]);
        flash('Slide deleted.');
        redirect('admin/sliders.php');
    }
    $id    = (int)($_POST['id'] ?? 0);
    $image = handle_upload('image', $_POST['current_image'] ?? '');
    $data  = [
        trim($_POST['title'] ?? ''),
        trim($_POST['subtitle'] ?? ''),
        $image,
        trim($_POST['button_text'] ?? ''),
        trim($_POST['button_link'] ?? ''),
        (int)($_POST['sort_order'] ?? 0),
        isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($id) {
        q('UPDATE sliders SET title=?,subtitle=?,image=?,button_text=?,button_link=?,sort_order=?,is_active=? WHERE id=?', [...$data, $id]);
        flash('Slide updated.');
    } else {
        q('INSERT INTO sliders (title,subtitle,image,button_text,button_link,sort_order,is_active) VALUES (?,?,?,?,?,?,?)', $data);
        flash('Slide created.');
    }
    redirect('admin/sliders.php');
}

$editing = null;
if (isset($_GET['edit'])) $editing = fetch_one('SELECT * FROM sliders WHERE id = ?', [(int)$_GET['edit']]);
elseif (isset($_GET['new'])) $editing = ['id'=>0,'title'=>'','subtitle'=>'','image'=>'','button_text'=>'','button_link'=>'','sort_order'=>0,'is_active'=>1];

admin_head('Sliders');

if ($editing): ?>
  <div class="panel">
    <div class="panel__head"><h2><?= $editing['id'] ? 'Edit' : 'New' ?> Slide</h2><a class="btn btn--sm" href="<?= url('admin/sliders.php') ?>">← Back</a></div>
    <form class="aform" method="post" enctype="multipart/form-data" action="<?= url('admin/sliders.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
      <input type="hidden" name="current_image" value="<?= e($editing['image']) ?>">
      <div class="aform__grid">
        <label class="aform__full">Title<input type="text" name="title" value="<?= e($editing['title']) ?>" required></label>
        <label class="aform__full">Subtitle<input type="text" name="subtitle" value="<?= e($editing['subtitle']) ?>"></label>
        <label>Button text<input type="text" name="button_text" value="<?= e($editing['button_text']) ?>"></label>
        <label>Button link<input type="text" name="button_link" value="<?= e($editing['button_link']) ?>" placeholder="category.php?cat=street-wear"></label>
        <label>Sort order<input type="number" name="sort_order" value="<?= (int)$editing['sort_order'] ?>"></label>
        <label class="aform__check"><input type="checkbox" name="is_active" <?= $editing['is_active']?'checked':'' ?>> Active</label>
        <div class="aform__full">
          <label>Image upload<input type="file" name="image" accept="image/*"></label>
          <label>…or image path<input type="text" name="image_path" value="<?= e($editing['image']) ?>" placeholder="assets/images/slides/slide-1.svg"></label>
          <?php if ($editing['image']): ?><div class="aform__preview"><img src="<?= e(img($editing['image'])) ?>" alt=""></div><?php endif; ?>
        </div>
      </div>
      <button class="btn btn--brand btn--lg" type="submit">Save Slide</button>
    </form>
  </div>
<?php else:
  $rows = fetch_all('SELECT * FROM sliders ORDER BY sort_order, id'); ?>
  <div class="panel">
    <div class="panel__head"><h2>Homepage Sliders</h2><a class="btn btn--brand" href="<?= url('admin/sliders.php?new=1') ?>">+ New Slide</a></div>
    <table class="dtable">
      <thead><tr><th>Image</th><th>Title</th><th>Order</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><img class="thumb" src="<?= e(img($r['image'])) ?>" alt=""></td>
            <td><strong><?= e($r['title']) ?></strong><br><small class="muted"><?= e($r['subtitle']) ?></small></td>
            <td><?= (int)$r['sort_order'] ?></td>
            <td><?= $r['is_active'] ? '<span class="pill pill--won">Yes</span>' : '<span class="pill">No</span>' ?></td>
            <td class="nowrap">
              <a class="btn btn--sm" href="<?= url('admin/sliders.php?edit=' . (int)$r['id']) ?>">Edit</a>
              <form method="post" class="inline" action="<?= url('admin/sliders.php') ?>"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn--sm btn--danger" data-confirm="Delete this slide?">Delete</button></form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="5" class="muted">No slides yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; admin_foot(); ?>
