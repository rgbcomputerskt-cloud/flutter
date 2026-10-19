<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (($_POST['do'] ?? '') === 'delete') {
        q('DELETE FROM categories WHERE id = ?', [(int)$_POST['id']]);
        flash('Category deleted.');
        redirect('admin/categories.php');
    }
    $id   = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
    $image = handle_upload('image', $_POST['current_image'] ?? '');
    $data = [$name, $slug, trim($_POST['tagline'] ?? ''), trim($_POST['description'] ?? ''), $image, trim($_POST['color'] ?? '#e11d48'), (int)($_POST['sort_order'] ?? 0), isset($_POST['is_active'])?1:0];
    try {
        if ($id) { q('UPDATE categories SET name=?,slug=?,tagline=?,description=?,image=?,color=?,sort_order=?,is_active=? WHERE id=?', [...$data, $id]); flash('Category updated.'); }
        else     { q('INSERT INTO categories (name,slug,tagline,description,image,color,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?)', $data); flash('Category created.'); }
    } catch (PDOException $e) { flash('Save failed — slug may already exist.', 'error'); }
    redirect('admin/categories.php');
}

$editing = null;
if (isset($_GET['edit'])) $editing = fetch_one('SELECT * FROM categories WHERE id = ?', [(int)$_GET['edit']]);
elseif (isset($_GET['new'])) $editing = ['id'=>0,'name'=>'','slug'=>'','tagline'=>'','description'=>'','image'=>'','color'=>'#e11d48','sort_order'=>0,'is_active'=>1];

admin_head('Categories');

if ($editing): ?>
  <div class="panel">
    <div class="panel__head"><h2><?= $editing['id']?'Edit':'New' ?> Category</h2><a class="btn btn--sm" href="<?= url('admin/categories.php') ?>">← Back</a></div>
    <form class="aform" method="post" enctype="multipart/form-data" action="<?= url('admin/categories.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
      <input type="hidden" name="current_image" value="<?= e($editing['image']) ?>">
      <div class="aform__grid">
        <label>Name<input type="text" name="name" value="<?= e($editing['name']) ?>" required></label>
        <label>Slug <small>(blank = auto)</small><input type="text" name="slug" value="<?= e($editing['slug']) ?>"></label>
        <label class="aform__full">Tagline<input type="text" name="tagline" value="<?= e($editing['tagline']) ?>"></label>
        <label class="aform__full">Description<textarea name="description" rows="3"><?= e($editing['description']) ?></textarea></label>
        <label>Accent color<input type="color" name="color" value="<?= e($editing['color']) ?>"></label>
        <label>Sort order<input type="number" name="sort_order" value="<?= (int)$editing['sort_order'] ?>"></label>
        <label class="aform__check"><input type="checkbox" name="is_active" <?= $editing['is_active']?'checked':'' ?>> Active</label>
        <div class="aform__full">
          <label>Image upload<input type="file" name="image" accept="image/*"></label>
          <label>…or image path<input type="text" name="image_path" value="<?= e($editing['image']) ?>" placeholder="assets/images/cats/cat-sports.svg"></label>
          <?php if ($editing['image']): ?><div class="aform__preview"><img src="<?= e(img($editing['image'])) ?>" alt=""></div><?php endif; ?>
        </div>
      </div>
      <button class="btn btn--brand btn--lg" type="submit">Save Category</button>
    </form>
  </div>
<?php else:
  $rows = fetch_all('SELECT * FROM categories ORDER BY sort_order, name'); ?>
  <div class="panel">
    <div class="panel__head"><h2>Categories</h2><a class="btn btn--brand" href="<?= url('admin/categories.php?new=1') ?>">+ New Category</a></div>
    <table class="dtable">
      <thead><tr><th></th><th>Name</th><th>Slug</th><th>Products</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><span class="swatch" style="background:<?= e($r['color']) ?>"></span></td>
            <td><strong><?= e($r['name']) ?></strong><br><small class="muted"><?= e($r['tagline']) ?></small></td>
            <td><code><?= e($r['slug']) ?></code></td>
            <td><?= product_count_in((int)$r['id']) ?></td>
            <td><?= $r['is_active']?'<span class="pill pill--won">Yes</span>':'<span class="pill">No</span>' ?></td>
            <td class="nowrap">
              <a class="btn btn--sm" href="<?= url('admin/categories.php?edit=' . (int)$r['id']) ?>">Edit</a>
              <form method="post" class="inline" action="<?= url('admin/categories.php') ?>"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn--sm btn--danger" data-confirm="Delete this category? Products keep existing but become uncategorised.">Delete</button></form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; admin_foot(); ?>
