<?php

# ぺたちゃ　ユーティリティクラス
# こまごました関数

class PtUtil {

	const ERROR_LOG = './error_log.txt'; # 設定ファイルにエラーログのパスが無かった時用

	# セットアップのファイルがあったら起動させない
	public static function checkDBSetup() {

		$DBSetupFile = PtConf::S('path/dbsetup');

		if (file_exists($DBSetupFile)) {

			$error = "{$DBSetupFile} exists";
			trigger_error($error, E_USER_ERROR);

		}

	}

	# 配列の中身を部分一致で検索
	# 最初にヒットした値のキーを返す、reverse = true は配列の中身「で」検索
	public static function arrayPos($target, $str, $reverse = false) {

		if (!is_array($target) && !(is_object($target) && get_class($target) == 'SimpleXMLElement')) return(false);

		if ($reverse) {

			foreach ($target as $key => $val)
			 if (strpos($str, $val) !== false) return($key);

		} else {

			foreach ($target as $key => $val)
			 if (strpos($val, $str) !== false) return($key);

		}

		return(false);

	}

	# cid のチェック
	public static function checkCid($cid) {

		return(preg_match("/^[0-9a-f]{6}$/", $cid));

	}

	# cid を返す
	public static function makeCid() {

		# getenv('PATH') つけてちょっと推測しづらくした
		$str = getenv('REMOTE_ADDR') . getenv('PATH');
		$cid = substr(self::crcHex($str), 1, 6);

		return($cid);

	}

	# CRC を 16進数で返す
	public static function crcHex($str) {

		return(sprintf("%08x", crc32($str)));

	}

	# 文字列のエスケープ
	public static function escape($str) {

		# システム発言ではメタ文字として \t を使う
		# なので今回は送信情報から \s を削ってる (PtUser->loadQuery())
		# ここは表示前に呼び出される

		$str = htmlspecialchars($str, ENT_QUOTES);
		#$str = preg_replace("/\s/", " ", $str);
		#$str = preg_replace("/&amp;(#?[\w]+;)/", "&$1", $str);

		return ($str);

	}

	# 403 エラー出して終わる
	public static function exit403($cid) {

		$protocol = getenv('SERVER_PROTOCOL');
		if (!preg_match("/^HTTP\/\d\.\d$/", $protocol)) $protocol = 'HTTP/1.1';
		header("{$protocol} 403 Forbidden");

		$message = '403 Forbidden';
		if (PtConf::isOptOn('accesslog')) self::accessLog($cid, 403, strlen($message));
		exit($message);

	}

	# 平和に終わる
	public static function exit200($output, $cid) {

		if (PtConf::isOptOn('accesslog')) self::accessLog($cid, 200, strlen($output));
		exit($output);

	}

	# アクセス権の変更を試みる
	public static function tryChmod() {

		try {


		} catch (Exception $e) {


		}

	}

	# アクセスログ書き出し
	public static function accessLog($cid, $status, $size) {

		$logFile = PtConf::S('path/dir/logs') . 'access_' . date("Ymd") . '.log';

		$data = array(
		 'host' => PtConf::S('vars/rhost'),
		 'ident' => '-',
		 'cid' => $cid,
		 'date' => date('d/M/Y:H:i:s O'),
		 'method' => getenv('REQUEST_METHOD'),
		 'path' => getenv('REQUEST_URI'),
		 'protocol' => getenv('SERVER_PROTOCOL'),
		 'status' => $status,
		 'size' => $size,
		 'referer' => getenv('HTTP_REFERER'),
		 'agent' => getenv('HTTP_USER_AGENT')
		);

		# Combined Log Format
		$line = vsprintf('%s %s %s [%s] "%s %s %s" %d %d "%s" "%s"', $data);

		# ログ追加保存
		$LOG_fp = fopen($logFile, 'a');
		fwrite($LOG_fp, "{$line}\n");
		fclose($LOG_fp);
		@chmod($logFile, 0666);

	}

	# デバッグ用のログ書き出し
	public static function debug($str) {

		if (PT2_DEBUG_MODE) error_log(date("Y.m.d H:i") . " : {$str}\n\n", 3, PtConf::S('path/dir/logs') . PtConf::S('path/errorlog'));

	}

	# エラーを裏で拾う
	public static function silentError() {

		set_error_handler(array('PtUtil', 'silentErrorHandler'));

	}

	# PHP のエラーを拾う
	public static function silentErrorHandler($errno, $errstr, $errfile, $errline) {

		$file = (class_exists('PtConf') && method_exists('PtConf','S')) ? PtConf::S('path/dir/logs') . PtConf::S('path/errorlog') : '';
		if (!$file) $file = PtUtil::ERROR_LOG;
		$env = getenv('REMOTE_ADDR') . ', ' . getenv('HTTP_USER_AGENT');

		if ($errno != E_NOTICE && $errno != E_STRICT) {

			$message = "{$errstr} (type:{$errno}, file:{$errfile}, line:{$errline})";
			error_log(date("Y.m.d H:i") . " : {$message}\n({$env})\n\n", 3, $file);

		}

		# 今もう E_ERROR 定義しても意味ないけど
		if ($errno == E_USER_ERROR || $errno == E_ERROR){

			echo "error : {$errstr}";
			exit(1);

		}

		return(true);

	}

}

?>