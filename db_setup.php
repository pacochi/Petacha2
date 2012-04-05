<?php

# ぺたちゃ2　データベースのセットアップ用スクリプト
# セットアップが済んだらこのファイルはサーバから削除して下さい

# 内部エンコードとか
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
date_default_timezone_set('Asia/Tokyo');

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>ぺたちゃ2 db setup</title></head><body><pre>
';

# 前準備
define('PT2_DEBUG_MODE', true);
require_once('./lib/PtConf.php');
PtConf::load('./conf.xml');
require_once('./lib/PtUtil.php');
require_once('./lib/PtSQL.php');

$pt2log = PtConf::S('vars/logtable');
$pt2member = PtConf::S('vars/membertable');
$pt2misc = PtConf::S('vars/misctable');
$pt2host = PtConf::S('vars/hosttable');
$dbFile = PtConf::S('path/dir/logs') . PtConf::S('path/db');
$dbHostFile = PtConf::S('path/dir/logs') . PtConf::S('path/dbhost');

# バックアップ
backupFile($dbFile);
backupFile($dbHostFile);

# ログ全般
$pt2SQL = new PtSQL(new SQLiteDatabase($dbFile));

# pt2log
SQLQuery("CREATE TABLE %s (
 id INTEGER NOT NULL PRIMARY KEY,
 utime INTEGER NOT NULL,
 date TEXT NOT NULL,
 time TEXT NOT NULL,
 cid TEXT NOT NULL,
 name TEXT NOT NULL,
 color TEXT,
 body TEXT NOT NULL,
 ext TEXT
);",
 $pt2log, PtSQL::R_BOOLEAN, array());

# pt2member
SQLQuery("CREATE TABLE %s (
 utime INTEGER NOT NULL,
 cid TEXT PRIMARY KEY,
 name TEXT NOT NULL,
 color TEXT
);",
 $pt2member, PtSQL::R_BOOLEAN, array());

# pt2misc
SQLQuery("CREATE TABLE %s (
 ltime INTEGER
);",
 $pt2misc, PtSQL::R_BOOLEAN, array());

# pt2log のテスト
SQLQuery("INSERT INTO %s VALUES (NULL, '%d', '%s', '%s', '%s', '%s', '%s', '%s', %s);",
 $pt2log, PtSQL::R_BOOLEAN, 1293688800, '2010.12.30', '15:00:00', '012abc', 'name', 'black', 'test message', 'NULL');

SQLQuery("SELECT * FROM %s;",
 $pt2log, PtSQL::R_ARRAY, array());

SQLQuery("DELETE FROM %s WHERE id = %d;",
 $pt2log, PtSQL::R_BOOLEAN, 1);

# pt2member のテスト
SQLQuery("INSERT INTO %s VALUES (%d, '%s', '%s', '%s');",
 $pt2member, PtSQL::R_BOOLEAN, 1234567890, '012abc', 'name', 'black');

SQLQuery("SELECT * FROM %s;",
 $pt2member, PtSQL::R_ARRAY, array());

SQLQuery("DELETE FROM %s WHERE cid = '%s';",
 $pt2member, PtSQL::R_BOOLEAN, '012abc');

# pt2misc のテスト
SQLQuery("INSERT INTO %s VALUES (%d);",
 $pt2misc, PtSQL::R_BOOLEAN, 1234567890);

SQLQuery("SELECT * FROM %s;",
 $pt2misc, PtSQL::R_ARRAY, array());

SQLQuery("UPDATE %s SET ltime = %d;",
 $pt2misc, PtSQL::R_BOOLEAN, time());

# リモートホストのキャッシュ
$pt2SQL = new PtSQL(new SQLiteDatabase($dbHostFile));

# pt2host
SQLQuery("CREATE TABLE %s (
 utime INTEGER NOT NULL,
 addr TEXT PRIMARY KEY,
 host TEXT NOT NULL
);",
 $pt2host, PtSQL::R_BOOLEAN, array());

# pt2host のテスト
SQLQuery("INSERT INTO %s VALUES (%d, '%s', '%s');",
 $pt2host, PtSQL::R_BOOLEAN, 1234567890, '127.0.0.1', 'localhost');

SQLQuery("SELECT * FROM %s;",
 $pt2host, PtSQL::R_ARRAY, array());

SQLQuery("DELETE FROM %s WHERE addr = '%s';",
 $pt2host, PtSQL::R_BOOLEAN, '127.0.0.1');

echo '
特にエラーが出なかった場合は、セットアップ成功です。
セットアップが終わったら、このファイル (db_setup.php) はサーバから削除して下さい。
</pre></body></html>';

exit;

function backupFile($file) {

	if (file_exists($file)) rename($file, $file . '_' . date('YmdHis') . '.bak');

}

function SQLQuery() {

	global $pt2SQL;

	$funcArgs = func_get_args();
	$result = $pt2SQL->query($funcArgs);

	echo $pt2SQL->getQuery() . ' &rarr; '
	 . (($result !== false) ? "success\n" . htmlspecialchars(var_export($result, true)) : "failed\n" . htmlspecialchars($pt2SQL->getError()))
	 . "\n\n";

}

?>