<?php
require_once __DIR__ . '/db.php';

/* ---------------------------------------------------------
 *  Output / formatting helpers
 * ------------------------------------------------------- */
function e($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

function url(string $path = ''): string { return BASE_URL . '/' . ltrim($path, '/'); }

/** Resolve a stored image path to a usable URL (handles absolute, assets/, uploads). */
function img(?string $path, string $fallback = 'assets/images/placeholder.svg'): string
{
    $path = trim((string)$path);
    if ($path === '') return url($fallback);
    if (preg_match('#^https?://#i', $path)) return $path;
    return url($path);
}

function money($amount): string
{
    $sym = get_setting('currency_symbol', '$');
    return $sym . number_format((float)$amount, 2);
}

/* ---------------------------------------------------------
 *  Settings (cached key/value store)
 * ------------------------------------------------------- */
function get_setting(string $key, $default = '')
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (fetch_all('SELECT skey, svalue FROM settings') as $r) {
            $cache[$r['skey']] = $r['svalue'];
        }
    }
    return array_key_exists($key, $cache) ? $cache[$key] : $default;
}

function set_setting(string $key, $value): void
{
    q('INSERT INTO settings (skey, svalue) VALUES (?, ?)
       ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)', [$key, $value]);
}

/* ---------------------------------------------------------
 *  Slugs
 * ------------------------------------------------------- */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item-' . substr(md5($text . mt_rand()), 0, 6);
}

/* ---------------------------------------------------------
 *  Flash messages
 * ------------------------------------------------------- */
function flash(string $msg, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['msg' => $msg, 'type' => $type];
}
function get_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------------------------------------------------------
 *  CSRF
 * ------------------------------------------------------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}
function csrf_check(): bool
{
    $t = $_POST['_csrf'] ?? '';
    return is_string($t) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t);
}
function require_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_check()) {
        http_response_code(400);
        exit('Invalid or expired form token. Please go back and try again.');
    }
}

function redirect(string $to): void
{
    header('Location: ' . (preg_match('#^https?://#', $to) ? $to : url($to)));
    exit;
}

/* ---------------------------------------------------------
 *  Catalog helpers
 * ------------------------------------------------------- */
function get_categories(bool $activeOnly = true): array
{
    $sql = 'SELECT * FROM categories' . ($activeOnly ? ' WHERE is_active = 1' : '') . ' ORDER BY sort_order, name';
    return fetch_all($sql);
}
function get_category(string $slug): ?array
{
    return fetch_one('SELECT * FROM categories WHERE slug = ?', [$slug]);
}
function product_count_in(int $catId): int
{
    return (int) fetch_col('SELECT COUNT(*) FROM products WHERE category_id = ? AND is_active = 1', [$catId]);
}

/* ---------------------------------------------------------
 *  Inquiry "cart" (session based) — quantities per product+variation
 * ------------------------------------------------------- */
function inquiry_items(): array { return $_SESSION['inquiry'] ?? []; }
function inquiry_count(): int
{
    return array_sum(array_map(fn($i) => (int)$i['quantity'], inquiry_items()));
}
function inquiry_add(int $productId, int $qty, string $variations = '', string $notes = ''): void
{
    $key = $productId . '|' . md5($variations);
    $items = inquiry_items();
    if (isset($items[$key])) {
        $items[$key]['quantity'] += $qty;
    } else {
        $p = fetch_one('SELECT name, main_image, price_from, min_order FROM products WHERE id = ?', [$productId]);
        if (!$p) return;
        $items[$key] = [
            'product_id' => $productId,
            'name'       => $p['name'],
            'image'      => $p['main_image'],
            'price_from' => $p['price_from'],
            'variations' => $variations,
            'notes'      => $notes,
            'quantity'   => max($qty, 1),
        ];
    }
    $_SESSION['inquiry'] = $items;
}
function inquiry_remove(string $key): void
{
    unset($_SESSION['inquiry'][$key]);
}
function inquiry_clear(): void { unset($_SESSION['inquiry']); }

/* ---------------------------------------------------------
 *  Admin auth
 * ------------------------------------------------------- */
function admin_user(): ?array { return $_SESSION['admin'] ?? null; }
function require_admin(): void
{
    if (!admin_user()) {
        redirect('admin/login.php');
    }
}
