<?php
require_once __DIR__ . '/../config/config.php';

/** Which driver is active ('sqlite' | 'mysql'). */
function db_driver(): string { return DB_DRIVER === 'mysql' ? 'mysql' : 'sqlite'; }

/**
 * Shared PDO connection. SQLite is auto-created and seeded on first run.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        if (db_driver() === 'sqlite') {
            $path  = DB_SQLITE_PATH;
            $fresh = !file_exists($path) || filesize($path) === 0;
            if (!is_dir(dirname($path))) { @mkdir(dirname($path), 0775, true); }
            $pdo = new PDO('sqlite:' . $path, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $pdo->exec('PRAGMA foreign_keys = ON');
            if ($fresh) {
                $sql = file_get_contents(__DIR__ . '/../database/schema.sqlite.sql');
                if ($sql === false) { throw new PDOException('Missing database/schema.sqlite.sql'); }
                $pdo->exec($sql);
            }
        } else {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        $hint = htmlspecialchars($e->getMessage(), ENT_QUOTES);
        $isSqlite = db_driver() === 'sqlite';
        $msg = $isSqlite
            ? "Could not open the SQLite database. Make sure the <code>database/</code> folder is writable by the web server and that PHP has the <code>pdo_sqlite</code> extension enabled."
            : "Could not connect to MySQL. Check <code>config/config.php</code> and make sure you've imported <code>database/schema.sql</code>.";
        echo "<div style='font:16px/1.6 system-ui;max-width:640px;margin:80px auto;padding:28px;border:1px solid #fecaca;background:#fef2f2;border-radius:12px;color:#7f1d1d'>"
           . "<h2 style='margin:0 0 8px'>Database error</h2>"
           . "<p>{$msg}</p>"
           . "<p style='color:#991b1b'><strong>Details:</strong> {$hint}</p>"
           . "</div>";
        exit;
    }
    return $pdo;
}

/** Convenience query helpers */
function q(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}
function fetch_all(string $sql, array $params = []): array { return q($sql, $params)->fetchAll(); }
function fetch_one(string $sql, array $params = []) { return q($sql, $params)->fetch() ?: null; }
function fetch_col(string $sql, array $params = []) { return q($sql, $params)->fetchColumn(); }
