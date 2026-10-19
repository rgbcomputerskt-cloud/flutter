<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

$pid = (int)($_GET['product'] ?? $_POST['product_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $do = $_POST['do'] ?? '';
    if ($do === 'delete') {
        q('DELETE FROM product_variations WHERE id = ?', [(int)$_POST['id']]);
        flash('Variation removed.');
    } elseif ($do === 'add') {
        $attr = trim($_POST['attribute'] ?? '');
        $val  = trim($_POST['value'] ?? '');
        if ($attr !== '' && $val !== '') {
            q('INSERT INTO product_variations (product_id,attribute,value,extra_cost,sort_order) VALUES (?,?,?,?,?)',
              [$pid, $attr, $val, (float)($_POST['extra_cost'] ?? 0), (int)($_POST['sort_order'] ?? 0)]);
            flash('Variation added.');
        } else {
            flash('Attribute and value are required.', 'error');
        }
    }
    redirect('admin/variations.php?product=' . $pid);
}

// pick a product if none chosen
if (!$pid) {
    $products = fetch_all('SELECT id, name FROM products ORDER BY name');
    admin_head('Variations'); ?>
    <div class="panel">
      <div class="panel__head"><h2>Product Variations</h2></div>
      <p class="muted" style="padding:0 0 12px">Choose a product to manage its variations (sizes, colors, styles…).</p>
      <ul class="picklist">
        <?php foreach ($products as $p): ?>
          <li><a href="<?= url('admin/variations.php?product=' . (int)$p['id']) ?>"><?= e($p['name']) ?> →</a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php admin_foot(); exit;
}

$product = fetch_one('SELECT * FROM products WHERE id = ?', [$pid]);
if (!$product) { flash('Product not found.', 'error'); redirect('admin/products.php'); }
$vars = fetch_all('SELECT * FROM product_variations WHERE product_id = ? ORDER BY attribute, sort_order, id', [$pid]);

admin_head('Variations — ' . $product['name']);
?>
<div class="panel">
  <div class="panel__head"><h2>Variations — <?= e($product['name']) ?></h2><a class="btn btn--sm" href="<?= url('admin/products.php?edit=' . $pid) ?>">← Edit product</a></div>
  <table class="dtable">
    <thead><tr><th>Attribute</th><th>Value</th><th>Extra cost</th><th>Order</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($vars as $v): ?>
        <tr>
          <td><strong><?= e($v['attribute']) ?></strong></td>
          <td><?= e($v['value']) ?></td>
          <td><?= $v['extra_cost']>0 ? '+'.money($v['extra_cost']) : '—' ?></td>
          <td><?= (int)$v['sort_order'] ?></td>
          <td><form method="post" class="inline" action="<?= url('admin/variations.php?product='.$pid) ?>"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$v['id'] ?>"><button class="btn btn--sm btn--danger" data-confirm="Remove this variation?">Delete</button></form></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$vars): ?><tr><td colspan="5" class="muted">No variations yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="panel">
  <div class="panel__head"><h2>Add variation</h2></div>
  <form class="aform" method="post" action="<?= url('admin/variations.php?product='.$pid) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="do" value="add">
    <input type="hidden" name="product_id" value="<?= $pid ?>">
    <div class="aform__grid">
      <label>Attribute<input type="text" name="attribute" list="attrs" placeholder="Size / Color / Style" required></label>
      <datalist id="attrs"><option>Size</option><option>Color</option><option>Style</option><option>Sleeve</option><option>Fit</option></datalist>
      <label>Value<input type="text" name="value" placeholder="XL / Royal Blue" required></label>
      <label>Extra cost<input type="number" step="0.01" name="extra_cost" value="0"></label>
      <label>Sort order<input type="number" name="sort_order" value="0"></label>
    </div>
    <button class="btn btn--brand btn--lg" type="submit">Add Variation</button>
  </form>
</div>
<?php admin_foot(); ?>
