<?php
require_once __DIR__ . '/includes/admin.php';
require_admin();

/** key => [label, type] */
$fields = [
    'site_name'        => ['Site name', 'text'],
    'site_tagline'     => ['Tagline', 'text'],
    'logo_text'        => ['Logo text', 'text'],
    'primary_color'    => ['Primary / brand color', 'color'],
    'accent_color'     => ['Accent (dark) color', 'color'],
    'topbar_text'      => ['Top bar message', 'text'],
    'topbar_phone'     => ['Top bar phone', 'text'],
    'topbar_email'     => ['Top bar email', 'text'],
    'marquee_text'     => ['Marquee text', 'textarea'],
    'enable_parallax'  => ['Show parallax section (1 / 0)', 'text'],
    'currency_symbol'  => ['Currency symbol', 'text'],
    'contact_address'  => ['Contact address', 'textarea'],
    'contact_phone'    => ['Contact phone', 'text'],
    'contact_email'    => ['Contact email', 'text'],
    'contact_whatsapp' => ['WhatsApp', 'text'],
    'footer_about'     => ['Footer about text', 'textarea'],
    'social_facebook'  => ['Facebook URL', 'text'],
    'social_instagram' => ['Instagram URL', 'text'],
    'social_linkedin'  => ['LinkedIn URL', 'text'],
    'social_youtube'   => ['YouTube URL', 'text'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (!empty($_POST['new_password'])) {
        q('UPDATE admin_users SET password_hash = ? WHERE id = ?',
          [password_hash($_POST['new_password'], PASSWORD_DEFAULT), admin_user()['id']]);
    }
    foreach ($fields as $key => $_) {
        if (array_key_exists($key, $_POST)) set_setting($key, trim((string)$_POST[$key]));
    }
    flash('Settings saved.');
    redirect('admin/settings.php');
}

admin_head('Settings');
?>
<form class="aform" method="post" action="<?= url('admin/settings.php') ?>">
  <?= csrf_field() ?>
  <div class="panel">
    <div class="panel__head"><h2>Store Settings</h2></div>
    <div class="aform__grid">
      <?php foreach ($fields as $key => [$label, $type]): $val = get_setting($key); ?>
        <label class="<?= $type==='textarea' ? 'aform__full' : '' ?>">
          <?= e($label) ?>
          <?php if ($type === 'textarea'): ?>
            <textarea name="<?= e($key) ?>" rows="3"><?= e($val) ?></textarea>
          <?php elseif ($type === 'color'): ?>
            <input type="color" name="<?= e($key) ?>" value="<?= e($val) ?>">
          <?php else: ?>
            <input type="text" name="<?= e($key) ?>" value="<?= e($val) ?>">
          <?php endif; ?>
        </label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="panel">
    <div class="panel__head"><h2>Admin Password</h2></div>
    <div class="aform__grid">
      <label>New password <small>(blank = keep current)</small><input type="password" name="new_password" autocomplete="new-password"></label>
    </div>
  </div>

  <button class="btn btn--brand btn--lg" type="submit">Save all settings</button>
</form>
<?php admin_foot(); ?>
