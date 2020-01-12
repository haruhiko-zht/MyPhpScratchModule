<?php

require_once(__DIR__ . '/DB.php');

// Example 1
$dbh = DB::connect();
$sql = 'SELECT * FROM schema.table';
$stmt = DB::flashSql($dbh, $sql);
$res = $stmt->fetchAll();
// You can replace it.
$res = DB::flashSql($dbh, $sql)->fetchAll();

// Example 2
$dbh = DB::connect();
$sql = 'SELECT * FROM t1 WHERE ex_column = :ex_value';
$data = [':ex_value' => 'TEST'];
$res = DB::flashSql($dbh, $sql, $data)->fetch();

// Example 3
$dbh = DB::connect();
$sql = 'SELECT id FROM t1 WHERE ex_column = :ex_value';
$res = DB::flashSql($dbh, $sql, [':ex_value' => 'TEST'])->fetchColumn();

// Example 3.5
$ex_value = 'TEST';
$dbh = DB::connect();
$sql = 'SELECT id FROM t1 WHERE ex_column = :ex_value';
$res = DB::flashSql($dbh, $sql, [':ex_value' => $ex_value])->fetchColumn();
$res = DB::flashSql($dbh, $sql, ['ex_value' => $ex_value])->fetchColumn();
$res = DB::flashSql($dbh, $sql, compact('ex_value'))->fetchColumn();

// Example 4
$dbh = DB::connect();
$sql = 'SELECT * FROM t1 WHERE ex_column = :ex_value';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':ex_value', 'TEST', $dbh::PARAM_STR);
$stmt->execute();
$res = $stmt->fetch();

// Example 5
$dbh = DB::connect();
$sql = 'SELECT * FROM t1 WHERE ex_column = :ex_value WHERE ex_like LIKE :ex_like ESCAPE \'#\'';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':ex_value', 'TEST', $dbh::PARAM_STR);
$stmt->bindValue(':ex_like', '%' . DB::escapeWildcard('TEST') . '%', $dbh::PARAM_STR);
$stmt->execute();
$res = $stmt->fetch();