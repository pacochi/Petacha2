<?php

# ぺたちゃ　設定クラス
# 設定情報を扱う静的クラス

class PtConf {

	const ON = 'on';
	const OFF = 'off';
	private static $_conf; # 設定 (SimpleXML)

	# 設定読み込み
	# PtUtil::silentError() より前に呼び出される
	public static function load($confFile) {

		$buf = '';
		libxml_use_internal_errors(true);

		try {

			$conf = new SimpleXMLElement($confFile, LIBXML_NOCDATA, true);

		} catch (Exception $e) {

			$buf .= 'conf error : ' . $e->getMessage() . "\n\n";

		}

		$errors = libxml_get_errors();

		# 設定が読み込めてないと困る
		if (count($errors)) self::exitWithError($confFile, $errors, $buf);

		libxml_clear_errors();
		libxml_use_internal_errors(false);

		self::$_conf = $conf;

		return(true);

	}

	# しょっぱなのエラーは表示して終わる
	private static function exitWithError($confFile, $errors, $buf) {

		$confXML = (file_exists($confFile)) ? @file($confFile) : false;

		$errLevel = array(
		 LIBXML_ERR_WARNING => 'Warning',
		 LIBXML_ERR_ERROR => 'Error',
		 LIBXML_ERR_FATAL => 'Fatal Error'
		);

		foreach ($errors as $error) {

			$buf .= "{$errLevel[$error->level]} {$error->code}: {$error->message}";

			if (is_array($confXML)) {

				$buf .= rtrim($confXML[$error->line - 1]) . "\n";
				$buf .= str_repeat('-', $error->column - 1) . "^\n";

			}

		}

		$buf = '<pre>' . htmlspecialchars($buf) . '</pre>';

		trigger_error($buf, E_USER_ERROR);

	}

	# 環境変数的なもの追加
	public static function addEnv() {

		# スクリプトの URL
		$scripturl = 'http://' . getenv('HTTP_HOST') . getenv('SCRIPT_NAME');
		if (self::isOptOn('trimurl')) $scripturl = preg_replace("/index\.php.?$/", '', $scripturl);
		self::$_conf->text->addChild('scripturl', $scripturl);

		# RSS の URL
		self::$_conf->text->addChild('rssurl', "{$scripturl}?rss");

		# リモートホスト
		$addr = getenv('REMOTE_ADDR');

		# リバースプロキシ用
		# 127.0.0.1 じゃないような運用してる人ならここもきっと自力で直してくれる
		if ($addr == '127.0.0.1') {

			$forwardedFor = getenv('HTTP_X_FORWARDED_FOR');
			$realIP = getenv('HTTP_X_Real_IP');
			$addr = ($realIP !== false) ? $realIP : (($forwardedFor !== false) ? $forwardedFor : $addr);

		}

		$host = '';
		$tHost = self::S('vars/hosttable');
		# 既に PtSQL.php が呼ばれている
		$SQL = new PtSQL(self::S('path/dir/logs') . self::S('path/dbhost'));

		# 24時間経ったデータを消す
		$ctime = time() - 86400;
		$SQL->beginTransaction();

		$result = $SQL->query(array("SELECT addr FROM %s WHERE utime < '%d';",
		 $tHost, PtSQL::R_ARRAY, $ctime));

		if ($result !== false && count($result) > 0) foreach ($result as $line)
		 $SQL->query(array("DELETE FROM %s WHERE addr = '%s';",
		 $tHost, PtSQL::R_BOOLEAN, $line['addr']));

		# データベースにアドレスがあるか調べる
		$result = $SQL->query(array("SELECT host FROM %s WHERE addr = '%s';",
		 $tHost, PtSQL::R_STRING, $addr));
		$host = ($result) ? $result : '';

		# なかったら問い合わせて追加
		if (!$host) {

			$host = gethostbyaddr($addr);
			$SQL->query(array("INSERT INTO %s VALUES ('%d', '%s', '%s');",
			 $tHost, PtSQL::R_BOOLEAN, time(), $SQL->escape($addr), $SQL->escape($host)));

		}

		$SQL->commitTransaction();

		# SQLiteDatabase::close() が無いんだけどこれでいいのかな
		unset($SQL);

		self::$_conf->vars->addChild('rhost', $host);

	}

	# 設定を丸ごと取得
	public static function getConf() {

		return(self::$_conf);

	}

	# テキスト設定を取得
	public static function getConfText() {

		return(self::$_conf->text);

	}

	# 設定項目にアクセス
	public static function getConfValue($xpath, $strict = false) {

		$value = self::$_conf->xpath($xpath);

		if (!is_array($value) || !count($value)) {

			if ($strict) PtUtil::debug("invalid xpath - {$xpath}");
			$value = array();

		}

		return($value);

	}

	# 文字数短めの設定項目アクセス
	public static function C($xpath) {

		return(self::getConfValue('/conf/' . $xpath, false));

	}

	# 文字数短めの設定項目アクセス (string にして返す)
	public static function S($xpath) {

		$value = current(self::getConfValue('/conf/' . $xpath, true));

		return(strval($value));

	}

	# conf->option 内の要素の on, off を true, false に変換
	# というか on を true、それ以外を false に変換
	public static function isOptOn($elm) {

		$value = strtolower(self::S("option/{$elm}"));

		return($value == self::ON);

	}

	# conf->select 内の要素のデフォルト値を返す
	public static function getSelDefault($elm) {

		return(self::S("select/{$elm}[@default='yes'][1]"));

	}

	# conf->select 内の要素の最初の値を返す
	public static function getSelFirst($elm) {

		return(self::S("select/{$elm}[1]"));

	}

	# conf->select 内の要素の最後の値を返す
	public static function getSelLast($elm) {

		return(self::S("select/{$elm}[last()]"));

	}

	# 色の正規化
	public static function normalizeColor($color) {

		$color = strtolower(preg_replace("/\s/", '', $color));

		switch (true) {

		# hsl(0,0%,0%), hsla(0,0%,0%,1)
		case preg_match("/^(hsla?\(\d{1,5}\,[\d\.]{1,5}%\,[\d\.]{1,5}%(\,[\d\.]{1,5}\)|\)))/i", $color, $m): 

			$color = $m[1];
			break;

		# rgb(0,0,0), rgba(0,0,0,1)
		case preg_match("/^(rgba?\([\d\.]{1,5}%?\,[\d\.]{1,5}%?\,[\d\.]{1,5}%?(\,[\d\.]{1,5}\)|\)))/i", $color, $m): 

			$color = $m[1];
			break;

		# black, #000000, #000
		# 色名として認識され得る名前で最長は「LightGoldenrodYellow」の 20文字
		# 最短は「Red」および「Tan」の三文字
		case preg_match("/^([a-z]{3,20}|#[\da-f]{6}|#[\da-f]{3})/i", $color, $m): 

			$color = $m[1];
			break;

		default: 

			$color = self::getSelDefault('color');

		}

		return($color);

	}

}

?>