<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (($_POST['do'] ?? '') === 'delete') {
        q('DELETE FROM products WHERE id = ?', [(int)$_POST['id']]);
        flash('Product deleted.'); redirect('admin/products.php');
    }
    $id    = (int)($_POST['id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $slug  = trim($_POST['slug'] ?? '') ?: slugify($name);
    $cat   = $_POST['category_id'] === '' ? null : (int)$_POST['category_id'];
    $image = handle_upload('main_image', $_POST['current_image'] ?? '');
    $data  = [
        $name, $slug, $cat, trim($_POST['sku'] ?? ''), trim($_POST['short_desc'] ?? ''),
        trim($_POST['description'] ?? ''), $image, (float)($_POST['price_from'] ?? 0),
        (int)($_POST['min_order'] ?? 10), trim($_POST['fabric'] ?? ''),
        isset($_POST['is_featured'])?1:0, isset($_POST['is_active'])?1:0,
    ];
    try {
        if ($id) {
            q('UPDATE products SET name=?,slug=?,category_id=?,sku=?,short_desc=?,description=?,main_image=?,price_from=?,min_order=?,fabric=?,is_featured=?,is_active=? WHERE id=?', [...$data, $id]);
        } else {
            q('INSERT INTO products (name,slug,category_id,sku,short_desc,description,main_image,price_from,min_order,fabric,is_featured,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', $data);
            $id = (int) db()->lastInsertId();
        }
        // gallery: rebuild from textarea (one path per line)
        q('DELETE FROM product_images WHERE product_id = ?', [$id]);
        $lines = preg_split('/\r?\n/', trim($_POST['gallery'] ?? ''));
        $o = 1;
        foreach ($lines as $ln) { $ln = trim($ln); if ($ln !== '') { q('INSERT INTO product_images (product_id,image,sort_order) VALUES (?,?,?)', [$id, $ln, $o++]); } }
        flash('Product saved.');
    } catch (PDOException $ex) {
        flash('Save failed — slug may already exist.', 'error');
    }
    redirect('admin/products.php?edit=' . $id);
}

$cats = get_categories(false);
$editing = null;
if (isset($_GET['edit'])) $editing = fetch_one('SELECT * FROM products WHERE id = ?', [(int)$_GET['edit']]);
elseif (isset($_GET['new'])) $editing = ['id'=>0,'name'=>'','slug'=>'','category_id'=>'','sku'=>'','short_desc'=>'','description'=>'','main_image'=>'','price_from'=>'0.00','min_order'=>10,'fabric'=>'','is_featured'=>0,'is_active'=>1];

admin_head('Products');

if ($editing):
  $gallery = $editing['id'] ? fetch_all('SELECT image FROM product_images WHERE product_id = ? ORDER BY sort_order', [$editing['id']]) : [];
  $galleryText = implode("\n", array_column($gallery, 'image'));
  $varCount = $editing['id'] ? (int)fetch_col('SELECT COUNT(*) FROM product_variations WHERE product_id = ?', [$editing['id']]) : 0;
  ?>
  <div class="panel">
    <div class="panel__head">
      <h2><?= $editing['id']?'Edit':'New' ?> Product</h2>
      <div>
        <?php if ($editing['id']): ?><a class="btn btn--sm" href="<?= url('admin/variations.php?product=' . (int)$editing['id']) ?>">Variations (<?= $varCount ?>)</a><?php endif; ?>
        <a class="btn btn--sm" href="<?= url('admin/products.php') ?>">← Back</a>
      </div>
    </div>
    <form class="aform" method="post" enctype="multipart/form-data" action="<?= url('admin/products.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
      <input type="hidden" name="current_image" value="<?= e($editing['main_image']) ?>">
      <div class="aform__grid">
        <label>Name<input type="text" name="name" value="<?= e($editing['name']) ?>" required></label>
        <label>Slug <small>(blank = auto)</small><input type="text" name="slug" value="<?= e($editing['slug']) ?>"></label>
        <label>Category
          <select name="category_id">
            <option value="">— none —</option>
            <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)$editing['category_id']===(string)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </label>
        <label>SKU<input type="text" name="sku" value="<?= e($editing['sku']) ?>"></label>
        <label>Price from<input type="number" step="0.01" name="price_from" value="<?= e($editing['price_from']) ?>"></label>
        <label>Min. order (MOQ)<input type="number" name="min_order" value="<?= (int)$editing['min_order'] ?>"></label>
        <label class="aform__full">Fabric<input type="text" name="fabric" value="<?= e($editing['fabric']) ?>"></label>
        <label class="aform__full">Short description<input type="text" name="short_desc" value="<?= e($editing['short_desc']) ?>" maxlength="300"></label>
        <label class="aform__full">Full description<textarea name="description" rows="5"><?= e($editing['description']) ?></textarea></label>

        <div class="aform__full">
          <label>Main image upload<input type="file" name="main_image" accept="image/*"></label>
          <label>…or main image path<input type="text" name="main_image_path" value="<?= e($editing['main_image']) ?>" placeholder="assets/images/products/p-football.svg"></label>
          <?php if ($editing['main_image']): ?><div class="aform__preview"><img src="<?= e(img($editing['main_image'])) ?>" alt=""></div><?php endif; ?>
        </div>

        <label class="aform__full">Gallery images <small>(one path per line — used for the cloud-zoom gallery)</small>
          <textarea name="gallery" rows="4" class="mono" placeholder="assets/images/products/p-football.svg"><?= e($galleryText) ?></textarea>
        </label>

        <label class="aform__check"><input type="checkbox" name="is_featured" <?= $editing['is_featured']?'checked':'' ?>> Featured</label>
        <label class="aform__check"><input type="checkbox" name="is_active" <?= $editing['is_active']?'checked':'' ?>> Active</label>
      </div>
      <button class="btn btn--brand btn--lg" type="submit">Save Product</button>
    </form>
  </div>
<?php else:
  $rows = fetch_all('SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC, p.id DESC'); ?>
  <div class="panel">
    <div class="panel__head"><h2>Products (<?= count($rows) ?>)</h2><a class="btn btn--brand" href="<?= url('admin/products.php?new=1') ?>">+ New Product</a></div>
    <table class="dtable">
      <thead><tr><th></th><th>Name</th><th>Category</th><th>From</th><th>MOQ</th><th>Flags</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><img class="thumb" src="<?= e(img($r['main_image'])) ?>" alt=""></td>
            <td><strong><?= e($r['name']) ?></strong><br><small class="muted"><?= e($r['sku']) ?></small></td>
            <td><?= e($r['cat']) ?: '—' ?></td>
            <td><?= money($r['price_from']) ?></td>
            <td><?= (int)$r['min_order'] ?></td>
            <td><?= $r['is_featured']?'<span class="pill pill--quoted">Featured</span> ':'' ?><?= $r['is_active']?'<span class="pill pill--won">Live</span>':'<span class="pill">Hidden</span>' ?></td>
            <td class="nowrap">
              <a class="btn btn--sm" href="<?= url('admin/products.php?edit=' . (int)$r['id']) ?>">Edit</a>
              <a class="btn btn--sm" href="<?= url('admin/variations.php?product=' . (int)$r['id']) ?>">Vars</a>
              <form method="post" class="inline" action="<?= url('admin/products.php') ?>"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn--sm btn--danger" data-confirm="Delete this product?">Delete</button></form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; admin_foot(); ?>
