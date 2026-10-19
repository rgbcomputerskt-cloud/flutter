<?php
/** Reusable product card. Expects $p (product row). */
$cat = $p['category_id'] ? fetch_one('SELECT name, color FROM categories WHERE id = ?', [$p['category_id']]) : null;
?>
<article class="pcard">
  <div class="pcard__media">
    <a href="<?= url('product.php?slug=' . e($p['slug'])) ?>">
      <img src="<?= e(img($p['main_image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
    </a>
    <?php if (!empty($p['is_featured'])): ?><span class="pcard__badge">Featured</span><?php endif; ?>
    <div class="pcard__hover">
      <button type="button" class="pcard__qv" data-quickview="<?= (int)$p['id'] ?>">Quick View</button>
      <form method="post" action="<?= url('inquiry.php?action=add') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
        <input type="hidden" name="quantity" value="<?= (int)($p['min_order'] ?? 10) ?>">
        <button type="submit" class="pcard__inq">+ Add to Inquiry</button>
      </form>
    </div>
  </div>
  <div class="pcard__body">
    <?php if ($cat): ?><span class="pcard__cat" style="color:<?= e($cat['color']) ?>"><?= e($cat['name']) ?></span><?php endif; ?>
    <h3 class="pcard__title"><a href="<?= url('product.php?slug=' . e($p['slug'])) ?>"><?= e($p['name']) ?></a></h3>
    <p class="pcard__desc"><?= e($p['short_desc']) ?></p>
    <div class="pcard__foot">
      <span class="pcard__price">from <?= money($p['price_from']) ?></span>
      <span class="pcard__moq">MOQ <?= (int)($p['min_order'] ?? 10) ?></span>
    </div>
  </div>
</article>
