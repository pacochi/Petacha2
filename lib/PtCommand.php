<?php

# ぺたちゃ　コマンド実行クラス

class PtCommand {

	private $cmd; # コマンド
	private $arg; # 引数
	private $SQL; # SQL 操作用
	private $cid; # cid
	private $errorStr; # エラー格納場所

	# コンストラクタ
	public function __construct($cmd, $arg, $SQL, $cid) {

		$this->cmd = $cmd;
		$this->arg = $arg;
		$this->SQL = $SQL;
		$this->cid = $cid;

		return(true);

	}

	# コマンド
	public function execCmd() {

		$result = '';

		switch ($this->cmd) {

		# 自身の情報を表示
		case 'env' :
			$result = 'env ( ' . $this->execCmdEnv() . ' )';

			break;

		# 削除
		case 'del' :

			$delDate = $this->execCmdDel();
			$delDate = ($delDate) ? "SUCCESS : {$delDate}, " : 'FAILED, ';

			$result = 'del ( ' . $delDate;

			# cid は簡易 ID なので偽者が出る可能性がある
			# かつ作成済みの XML にも手を入れるので消しまくられると負荷増大
			# なので抑止力として env も追加
			$result .= $this->execCmdEnv() . ' )';

			break;

		# 宇宙語変換
		case 'uchu' :

			$convStr = $this->execCmdUchu();
			if ($convStr === false) $convStr = 'FAILED';

			$result = 'uchu ( ' . $convStr . ' )';

			break;

		default :

			return($this->addError('unknown_command'));

		}

		return($result);

	}

	# エラーメッセージを取得
	public function getError() {

		return($this->error);

	}

	# 自身の情報を表示
	# 引数なし
	private function execCmdEnv() {

		$result = '';
		$envMax = intval(PtConf::S('vars/envmax'));

		$envs = array(
		 'HTTP_USER_AGENT', 
		 'HTTP_CACHE_INFO',
		 'HTTP_CLIENT',
		 'HTTP_CLIENT_IP',
		 'HTTP_CLIENTIP',
		 'HTTP_FORWARDED',
		 'HTTP_FROM',
		 'HTTP_IF_MODIFIED_SINCE',
		 'HTTP_PROXY_CONNECTION',
		 'HTTP_REMOTE_HOST',
		 'HTTP_SP_HOST',
		 'HTTP_VIA',
		 'HTTP_X_FORWARDED_FOR',
		 'HTTP_X_LOCKING',
		 'HTTP_XONNECTION',
		 'HTTP_XROXY_CONNECTION'
		);

		foreach ($envs as $env) {

			$envStr = getenv($env);
			if ($envStr !== false && strlen($envStr) < $envMax)
			 $result .= "{$env} : {$envStr}, ";

		}

		$host = PtConf::S('vars/rhost');
		$result .= "HOST : {$host}";

		return($result);

	}

	# 削除
	# 引数日付と時刻で該当発言、引数なしで直近の発言
	private function execCmdDel() {

		$cid = $this->cid;
		$delId = 0;
		$delUtime = 0;
		$delDate = '';
		$delTime = '';
		$today = date(PtConf::S('vars/dateformat'));
		$cTime = date(PtConf::S('vars/timeformat'));
		$tLog = PtConf::S('vars/logtable');
		# とりあえず日付生成して正規表現作る
		$regDate = preg_replace("/\d/", '\\d', preg_quote($today, '/'));
		$regTime = preg_replace("/\d/", '\\d', preg_quote($cTime, '/'));

		# 日付指定あったらそのまま使う
		if (preg_match("/($regDate)($regTime)/", $this->arg, $m)) {

			$delDate = $m[1];
			$delTime = $m[2];

			$data = array($cid, $delDate, $delTime);
			$data = array_map($this->SQL->escape, $data);

			$result = $this->SQL->query(array(
			 "SELECT * FROM %s WHERE cid = '%s' AND date = '%s' AND time = '%s';",
			 $tLog, PtSQL::R_ARRAY, $data));
			if ($result === false) return(false);

			if (count($result) > 0) {

				$line = current($result);
				$delId = intval($line['id']);
				$delUtime = intval($line['utime']);

			} else {

				$delDate = '';
				$delTime = '';

			}

		# 日付指定なかったら直前のログを指定
		} else {

			$result = $this->SQL->query(array(
			 "SELECT * FROM %s WHERE cid = '%s' ORDER BY id DESC LIMIT 1;",
			 $tLog, PtSQL::R_ARRAY, $cid));
			if ($result === false) return(false);

			if (count($result) > 0) {

				$line = current($result);
				$delId = intval($line['id']);
				$delUtime = intval($line['utime']);
				$delDate = $line['date'];
				$delTime = $line['time'];

			}

		}

		if ($delId) {

			$result = $this->SQL->query(array(
			 "DELETE FROM %s WHERE id = %d;",
			 $tLog, PtSQL::R_BOOLEAN, $delId));
			if (!$result) return(false);

		}

		# 今日の日付じゃないなら過去ログも
		if ($delId && $delDate != $today) {

			$logFile = PtConf::S('path/dir/logs') . date('Ymd', $delUtime) . '.xml';

			if (file_exists($logFile)) {

				$logData = new SimpleXMLElement($logFile, LIBXML_NOCDATA, true);

				# Xpath で指定したら消えなかった
				$delChat = false;
				$chatLen = $logData->count();

				for ($i = 0; $i < $chatLen; $i++)
				 if ($logData->chat[$i]->id == $delId) {

					unset($logData->chat[$i]);
					$delChat = true;
					break;

				}

				if (!$delChat) return(false);

				$xmlDoc = new DOMDocument('1.0', 'UTF-8');
				$xmlDoc->loadXML($logData->asXML());
				$xmlDoc->formatOutput = true;
				$xmlDoc->resolveExternals = true;
				$xmlDoc->save($logFile, LIBXML_NOEMPTYTAG);

			}

		}

		return("{$delDate}{$delTime}");

	}

	# 宇宙語変換
	# 引数を宇宙語またはみかか語とみなして変換
	private function execCmdUchu() {

		$arg = strval($this->arg);
		if ($arg === '') return(false);

		require_once(PtConf::S('path/dir/library') . 'JP106Key.php');

		$result = JP106Key::isKana($arg) ? JP106Key::KtoR($arg) : JP106Key::AtoK($arg);

		return("{$arg} >> {$result}");

	}

	# エラーをしまう
	private function addError($message) {

		$this->errorStr = $message;

		return(false);

	}

}

?>