<?php


class DB
{
  /**
   * Set your database information.
   *
   * MYSQL_ATTR_USE_BUFFERED_QUERY is only mysql option.
   * ATTR_EMULATE_PREPARES => true is good performance, but all get param (excluding NULL) will be string type by SELECT QUERY.
   */
  const CONNECTION = 'mysql';
  const HOST = '127.0.0.1';
  const PORT = '3306';
  const DATABASE = 'dbname';
  const ENCODING = 'UTF-8';
  const USERNAME = 'root';
  const PASSWORD = '';
  const OPTIONS = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  /**
   * Assemble $dsn for PDO.
   * @return string
   */
  protected static function assembleDsn(): string
  {
    return sprintf('%s:dbname=%s;host=%s;port=%s;charset=%s', self::CONNECTION, self::DATABASE, self::HOST,
      self::PORT, self::ENCODING);
  }

  /**
   * Connect DB.
   * @param string|null $dsn
   * @param string|null $username
   * @param string|null $password
   * @param array|null $options
   * @return PDO
   */
  public static function connect(
    ?string $dsn = null,
    ?string $username = null,
    ?string $password = null,
    ?array $options = null
  ): PDO {
    $db_dsn = $dsn ?? self::assembleDsn();
    $db_username = $username ?? self::USERNAME;
    $db_password = $password ?? self::PASSWORD;
    $db_options = $options ?? self::OPTIONS;

    $dbh = new PDO($db_dsn, $db_username, $db_password, $db_options);
    return $dbh;
  }

  /**
   * Execute flash sql.
   *
   * When using this method, all param type (excluding NULL) will be string.
   * MySQL will auto cast (no error occurs / low performance), but Postgres will be error.
   *
   * @param PDO $dbh
   * @param string $sql
   * @param array $bind_data
   * @return PDOStatement
   */
  public static function flashSql(PDO $dbh, string $sql, array $bind_data = []): PDOStatement
  {
    $stmt = $dbh->prepare($sql);
    $stmt->execute($bind_data);
    return $stmt;
  }

  /**
   * Escape for LIKE query.
   * ex) WHERE name LIKE '%#%%' ESCAPE '#'
   *
   * In MySQL, ESCAPE clause is implicitly performed with \,
   * but \ is excluded from substitution by explicitly escaping with #.
   *
   * @param mixed $str
   * @param string $encoding
   * @return string
   */
  public static function escapeWildcard($str, string $encoding = 'UTF-8'): string
  {
    mb_internal_encoding($encoding);
    mb_regex_encoding($encoding);
    return mb_ereg_replace('([_%#])', '#\1', $str);
  }
}