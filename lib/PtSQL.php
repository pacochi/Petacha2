<?php

# ぺたちゃ　SQL操作クラス

class PtSQL {

	const R_BOOLEAN = 1; # 返り値が真偽
	const R_STRING = 2; # 返り値が文字列
	const R_ARRAY = 3; # 返り値が配列
	private $queryStr; # クエリ格納場所
	private $errorStr; # エラー格納場所
	private $db; # SQLite データベース

	# コンストラクタ
	public function __construct(&$db) {

		$this->db = $db;

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

	# トランザクションはじめ
	public function beginTransaction() {

		$this->db->queryExec('BEGIN TRANSACTION;');

	}

	# トランザクションおわり
	public function commitTransaction() {

		$this->db->queryExec('COMMIT TRANSACTION;');

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