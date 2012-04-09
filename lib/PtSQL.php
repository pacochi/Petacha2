<?php

# ぺたちゃ　SQL操作クラス

class PtSQL {

	const R_BOOLEAN = 1; # 返り値が真偽
	const R_STRING = 2; # 返り値が文字列
	const R_ARRAY = 3; # 返り値が配列
	const EX_SQLITE = 'sqlite'; # SQLite の拡張モジュール名
	const EX_PDO = 'pdo_sqlite'; # PDO SQLite の拡張モジュール名
	private $queryStr; # クエリ格納場所
	private $errorStr; # エラー格納場所
	private $db; # データベース
	private $dbType; # データベースの種類 (sqlite, pdo_sqlite)

	# コンストラクタ
	public function __construct($dbFile) {

		# PHP5.4 で sqlite が消えたから PDO で代替
		if (extension_loaded(self::EX_SQLITE)) {

			$db = new SQLiteDatabase($dbFile);
			$dbType = self::EX_SQLITE;

		} elseif (extension_loaded(self::EX_PDO)) {

			$db = new PDO("sqlite:{$dbFile}",'','');
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbType = self::EX_PDO;

		} else {

			$this->addError('SQLite extension is not loaded');
			return(true);

		}

		$this->db = $db;
		$this->dbType = $dbType;

		return(true);

	}

	# クエリを実行
	public function query($funcArgs) {

		$query = array_shift($funcArgs);
		$table = array_shift($funcArgs);
		$type = array_shift($funcArgs);

		# 引数おかしかったら帰る
		if (!is_string($query)) return($this->addError('invalid SQL query'));
		if (!is_string($table)) return($this->addError('invalid SQL table'));
		# 後で switch で分けるからここらで厳密に判定しとく
		if ($type !== self::R_BOOLEAN
		 && $type !== self::R_STRING
		 && $type !== self::R_ARRAY) return($this->addError('invalid SQL return type'));

		$args = (isset($funcArgs[0]) && is_array($funcArgs[0])) ? $funcArgs[0] : $funcArgs;
		array_unshift($args, $table);
		$this->queryStr = vsprintf($query, $args);

		if ($this->dbType == self::EX_SQLITE) return($this->querySQlite($this->queryStr, $type));
		 else return($this->queryPDO($this->queryStr, $type));

	}

	# トランザクションはじめ
	public function beginTransaction() {

		$query = 'BEGIN TRANSACTION;';
		if ($this->dbType == self::EX_SQLITE) $this->db->queryExec($query);
		 else $this->db->exec($query);

	}

	# トランザクションおわり
	public function commitTransaction() {

		$query = 'COMMIT TRANSACTION;';
		if ($this->dbType == self::EX_SQLITE) $this->db->queryExec($query);
		 else $this->db->exec($query);

	}

	# 文字列のエスケープ
	public function escape($str) {

		if ($this->dbType == self::EX_SQLITE) return(sqlite_escape_string($str));
		 else return(preg_replace("/'/", "''", $str));

	}

	# エラーの内容を返す
	public function getError() {

		return($this->errorStr);

	}

	# 最後に送ったクエリを返す
	public function getQuery() {

		return($this->queryStr);

	}

	# エラーをしまう
	private function addError($message) {

		PtUtil::debug($message);
		$this->errorStr = $message;

		# 行数がかさむのでここで false を返しておく
		return(false);

	}

	# PDO SQLite (SQLite3) を使う
	private function queryPDO($query, $type) {

		try{

			if ($type == self::R_BOOLEAN) {

				$this->db->exec($query);
				# そのままの返り値だと作用した行数を返すみたい
				$result = true;

			} else {

				$res = $this->db->query($query);

				if ($type == self::R_ARRAY)
				 $result = $res->fetchAll(PDO::FETCH_ASSOC);
				 else $result = strval($res->fetchColumn());

				$res->closeCursor();
			}

		} catch(PDOException $error) {

			return($this->addError("{$error->getMessage()} - {$query}"));

		}

		return($result);

	}

	# SQlite を使う
	private function querySQlite($query, $type) {

		switch ($type) {

		case self::R_BOOLEAN :

			$result = $this->queryExec($this->queryStr);
			break;

		case self::R_STRING :

			$result = $this->singleQuery($this->queryStr);
			break;

		case self::R_ARRAY :

			$result = $this->arrayQuery($this->queryStr);
			break;

		default :

			$result = $this->addError('invalid type');

		}

		return($result);

	}

	# queryExec のラッパー
	private function queryExec($query) {

		$result = $this->db->queryExec($query, $error);

		if (!$result) return($this->addError("{$error} - {$query}"));

		# true
		return($result);

	}

	# arrayQuery のラッパー
	private function arrayQuery($query) {

		$result = $this->db->arrayQuery($query, SQLITE_ASSOC);

		if ($result === false) return($this->addError(sqlite_error_string($this->db->lastError) . " - {$query}"));

		# array (! じゃなく === false の方が安全)
		return($result);

	}

	# singleQuery 的なもののラッパー
	private function singleQuery($query) {

		$result = $this->db->unbufferedQuery($query, SQLITE_ASSOC, $error);

		if ($result === false) return($this->addError("{$error} - {$query}"));
		 else $result = ($result->valid()) ? $result->fetchSingle() : '';

		# string (! じゃなく === false の方が安全)
		return($result);

	}

}

?>