<?php
/**
 * Database connection (PDO singleton).
 */

if (!defined('DB_HOST')) {
    $configFile = dirname(__DIR__) . '/config.php';
    if (!file_exists($configFile)) {
        die('Missing config.php. Copy config.sample.php to config.php and configure your database.');
    }
    require_once $configFile;
}

/**
 * Return a shared PDO connection.
 *
 * @return PDO
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            die('Database connection failed: ' . $e->getMessage());
        }
        http_response_code(500);
        die('Database connection failed. Please try again later.');
    }

    return $pdo;
}

/**
 * Run a prepared query and return the statement.
 *
 * @param string $sql
 * @param array  $params
 * @return PDOStatement
 */
function dbQuery(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch all rows for a query.
 */
function dbAll(string $sql, array $params = []): array
{
    return dbQuery($sql, $params)->fetchAll();
}

/**
 * Fetch a single row (or null).
 */
function dbOne(string $sql, array $params = []): ?array
{
    $row = dbQuery($sql, $params)->fetch();
    return $row === false ? null : $row;
}

/**
 * Fetch a single scalar value.
 */
function dbValue(string $sql, array $params = [])
{
    $value = dbQuery($sql, $params)->fetchColumn();
    return $value === false ? null : $value;
}

/**
 * Insert helper: returns the new row id.
 */
function dbInsert(string $table, array $data): int
{
    $columns = array_keys($data);
    $placeholders = array_map(static fn($c) => ':' . $c, $columns);
    $sql = 'INSERT INTO `' . $table . '` (`' . implode('`, `', $columns) . '`) VALUES (' . implode(', ', $placeholders) . ')';
    dbQuery($sql, $data);
    return (int) db()->lastInsertId();
}

/**
 * Update helper by primary key id.
 */
function dbUpdate(string $table, array $data, int $id): void
{
    $sets = [];
    foreach (array_keys($data) as $column) {
        $sets[] = '`' . $column . '` = :' . $column;
    }
    $data['__id'] = $id;
    $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $sets) . ' WHERE id = :__id';
    dbQuery($sql, $data);
}

/**
 * Delete helper by primary key id.
 */
function dbDelete(string $table, int $id): void
{
    dbQuery('DELETE FROM `' . $table . '` WHERE id = ?', [$id]);
}
