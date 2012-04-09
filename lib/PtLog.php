<?php

# ぺたちゃ　チャットログ操作クラス

class PtLog {

	private $user; # ユーザデータ
	private $data; # XML のデータ
	private $tLog; # ログのテーブル名
	private $tMember; # 参加者一覧のテーブル名
	private $tMisc; # その他雑多な設定値が詰まったテーブル名
	private $fDate; # 日付のフォーマット
	private $fTime; # 時刻のフォーマット
	private $logsXMLStr; # SimpleXMLElement 作成時に使う文字列
	private $SQL; # SQL 操作用 (データベースもこの中)

	# コンストラクタ
	public function __construct($user) {

		$this->user = $user;
		$this->tLog = PtConf::S('vars/logtable');
		$this->tMember = PtConf::S('vars/membertable');
		$this->tMisc = PtConf::S('vars/misctable');
		$this->fDate = PtConf::S('vars/dateformat');
		$this->fTime = PtConf::S('vars/timeformat');
		$this->logsXMLStr = '<' . '?xml version="1.0" encoding="UTF-8" ?' . '><logs />';
		$this->SQL = new PtSQL(PtConf::S('path/dir/logs') . PtConf::S('path/db'));
		$this->data = new SimpleXMLElement($this->logsXMLStr);

		if ($this->validateInput()) {

			$this->updateMember();
			$this->addChat();

		}

		$this->loadMember();

		if (PtConf::isOptOn('dailylog')) $this->saveDailyLog();
		$this->slice();
		$this->load();

		return(true);

	}

	# ユーザデータを取得
	public function getUser() {

		return($this->user);

	}

	# ログを丸ごと取得
	public function getData() {

		return($this->data);

	}

	# 最新の id を取得
	public function getLastId() {

		return(strval(current($this->data->xpath('/logs/chat[last()]/id'))));

	}

	# XML でログのとこだけ書き出し
	public function outputXML() {

		header('Content-type: application/xml; charset=UTF-8');
		PtUtil::exit200($this->data->asXML(), $this->user->cid);

	}

	# ログ読み出し
	private function load() {

		switch ($this->user->filter['type']) {

		case 'log' :

			# 過去ログ指定の時は XML ファイルから読む
			if ($this->user->filter['target'] != 'today') {

				$this->loadPastLog($this->user->filter['target']);
				return(true);

			} else {

				$result = $this->loadTodayLog();

			}

			break;

		case 'cid' :

				$result = $this->loadCidLog($this->user->filter['target']);

			break;

		default:

				$result = $this->loadNormalLog();

			break;

		}

		if ($result === false) return(false);

		# フィルタ指定表示アナウンス
		if (!$this->user->isAjaxMode() && $this->user->filter['type'] !== '') $this->addMessage('log_announce');

		# 新着ログなかったらそのまま帰る
		if (count($result) == 0) return(true);
		if (!$this->user->isRSSMode()) $result = array_reverse($result);

		$this->makeXML($result, $this->data, true);

		# 初回表示のアナウンス
		if (!$this->user->isAjaxMode() && !$this->user->isJoined() && $this->user->filter['type'] === '')
		 $this->addMessage('entrance_announce');

		return(true);
	}

	# 指定なしで読み出し
	private function loadNormalLog() {

		$result = $this->SQLQuery("SELECT * FROM %s WHERE id > %d ORDER BY id DESC LIMIT %d;",
		 $this->tLog, PtSQL::R_ARRAY, $this->user->lastId, $this->user->line);

		return($result);

	}

	# cid 指定で読み出し
	private function loadCidLog($cid) {

		$result = $this->SQLQuery("SELECT * FROM %s WHERE cid = '%s' ORDER BY id DESC LIMIT %d;",
		 $this->tLog, PtSQL::R_ARRAY, $cid, $this->user->line);

		return($result);

	}

	# 今日のログ全部読み出し
	private function loadTodayLog() {

		$result = $this->SQLQuery("SELECT * FROM %s WHERE date = '%s' ORDER BY id DESC;",
		 $this->tLog, PtSQL::R_ARRAY, date($this->fDate, time()));

		return($result);

	}

	# 過去の日付のログ読み出し
	private function loadPastLog($date) {

		$logFile = PtConf::S('path/dir/logs') . $date . '.xml';

		# ログ閲覧時に入室とかしないから addMessage 気にしない
		if (file_exists($logFile))
		 $this->data = new SimpleXMLElement($logFile, LIBXML_NOCDATA, true);
		 else $this->addError('failed_xml_load');

		if (!$this->user->isAjaxMode()) $this->addMessage('log_announce');

	}

	# XML にログを追加
	private function makeXML($logs, $xml, $rssdate) {

		foreach ($logs as $line) {

			$id = $line['id'];
			$date = $line['date'] . $line['time'];
			$name = PtUtil::escape($line['name']);
			$cid = PtUtil::escape($line['cid']);
			$body = PtUtil::escape($line['body']);
			$color = PtConf::normalizeColor($line['color']);
			$ext = $line['ext'];

			$item = $xml->addChild('chat');
			$item->addChild('id', $id);
			$item->addChild('date', $date);
			$item->addChild('cid', $cid);
			$item->addChild('name', $name);
			$item->addChild('color', $color);
			#$item->addChild('body', $body);
			#$item->addChild('ext', $ext);

			$text = new PtText($body, $ext);
			$text->addXML($item);

			# RSS
			if ($rssdate && $this->user->isRSSMode())
			 $item->addChild('rssdate', date(DATE_W3C, $line['utime']));

		}

	}

	# 日付毎のログを保存
	private function saveDailyLog() {

		# 最終更新日時
		$result = $this->SQLQuery("SELECT ltime FROM %s;",
		 $this->tMisc, PtSQL::R_STRING);
		# ここは false でも 0 でも空文字列でもエラー
		if (!$result) return(false);

		$d = getdate($result);
		# 記録された ltime の次の日 0時
		$ltime = mktime(0, 0, 0, $d['mon'], $d['mday'] + 1, $d['year']);
		$ctime = time();

		while ($ctime >= $ltime) {

			$ctime -= 86400; # 一日

			$result = $this->SQLQuery("SELECT * FROM %s WHERE date = '%s' ORDER BY id ASC;",
			 $this->tLog, PtSQL::R_ARRAY, date($this->fDate, $ctime));
			if ($result === false) return(false);

			if (count($result) == 0) continue;

			$logFile = PtConf::S('path/dir/logs') . date('Ymd', $ctime) . '.xml';
			$logData = new SimpleXMLElement($this->logsXMLStr);

			$this->makeXML($result, $logData, false);

			# 整形要らないならこの一文だけでいい
			#$logData->asXML($logFile);

			$xmlDoc = new DOMDocument('1.0', 'UTF-8');
			$xmlDoc->loadXML($logData->asXML());
			$xmlDoc->formatOutput = true;
			$xmlDoc->resolveExternals = true;
			$xmlDoc->save($logFile, LIBXML_NOEMPTYTAG);
			PtUtil::tryChmod($logFile, 0666);

		}

		# 最終更新日時を更新
		$result = $this->SQLQuery("UPDATE %s SET ltime = %d;",
		 $this->tMisc, PtSQL::R_BOOLEAN, time());
		if (!$result) return(false);

		return(true);

	}

	# ログ切り落とし
	private function slice() {

		$overNum = $this->getLineNum() - PtConf::getSelLast('line');

		# ログ余ってなかったら帰る
		if ($overNum <= 0) return(true);

		$today = date($this->fDate);

		$this->SQL->beginTransaction();

		# 今日の日付は余ってても消さない
		$result = $this->SQLQuery("SELECT id FROM %s WHERE date != '%s' ORDER BY id ASC LIMIT %d;",
		 $this->tLog, PtSQL::R_ARRAY, $today, $overNum);
		if ($result === false) return(false);

		if (count($result) > 0) foreach ($result as $line) {

			$result = $this->SQLQuery("DELETE FROM %s WHERE id = %d;",
			 $this->tLog, PtSQL::R_BOOLEAN, $line['id']);
			if (!$result) return(false);

		}

		$this->SQL->commitTransaction();

		return(true);

	}

	# 入力されたデータの検証
	private function validateInput() {

		# 発言がある場合のみ
		if (strlen($this->user->chat)) {

			if (getenv('REQUEST_METHOD') != 'POST') return($this->addError('method_not_post'));
			if (strlen($this->user->name) == 0) return($this->addError('empty_name'));
			if (strlen($this->user->chat) > intval(PtConf::S('vars/chatmax'))) return($this->addError('long_message'));

		}

		if (strlen($this->user->name) > intval(PtConf::S('vars/namemax'))) {

			$this->user->resetName();
			return($this->addError('long_name'));

		}

		return(true);

	}

	# 発言書き込み
	private function addChat() {

		# 入室かつ発言してなかったら帰る
		if (!$this->user->isJoined() || !strlen($this->user->chat)) return(true);

		$utime = time();

		$body = $this->user->chat;
		$ext = "NULL";

		# コマンド実行
		if (preg_match("/^_(\w+)_(.*)$/", $body, $m)) {

			require_once(PtConf::S('path/dir/library') . 'PtCommand.php');

			$cmd = new PtCommand($m[1], $m[2], $this->SQL, $this->user->cid);
			$result = $cmd->execCmd();

			if ($result === false) {

				# エラー出すけど帰らない
				$this->addError($cmd->getError());

			} else {

				$body = $result;
				$ext = "'command'";

			}

		}

		$data = array(
		 $utime,
		 date($this->fDate, $utime),
		 date($this->fTime, $utime),
		 $this->user->cid,
		 $this->user->name,
		 $this->user->color,
		 $body
		);

		$data = array_map(array($this->SQL, 'escape'), $data);
		$data[] = $ext;

		$result = $this->SQLQuery("INSERT INTO %s VALUES (NULL, %d, '%s', '%s', '%s', '%s', '%s', '%s', %s);",
		 $this->tLog, PtSQL::R_BOOLEAN, $data);
		if (!$result) return(false);

		return(true);

	}

	# システム発言書き込み
	private function addSystemChat($type, $repName, $repColor) {

		$utime = time();
		$body = str_replace('{name}', "\t{$repName}\t{$repColor}\t", PtConf::S("text/{$type}"));

		$data = array(
		 $utime,
		 date($this->fDate, $utime),
		 date($this->fTime, $utime),
		 PtConf::S('text/systemcid'),
		 PtConf::S('text/systemname'),
		 PtConf::S('text/systemcolor'),
		 $body
		);

		$data = array_map(array($this->SQL, 'escape'), $data);
		$data[] = "'system'"; # ext

		$result = $this->SQLQuery("INSERT INTO %s VALUES (NULL, %d, '%s', '%s', '%s', '%s', '%s', '%s', %s);",
		 $this->tLog, PtSQL::R_BOOLEAN, $data);
		if (!$result) return(false);

		return(true);

	}

	# 入室・退室処理
	private function updateMember() {

		$existCid = false;

		$cid = $this->user->cid;
		$name = $this->SQL->escape($this->user->name);
		$color = $this->user->color;

		# 既にいたら一旦削除
		$result = $this->SQLQuery("SELECT cid FROM %s WHERE cid = '%s';",
		 $this->tMember, PtSQL::R_STRING, $cid);
		if ($result === false) return(false);

		if ($result == $cid) {

			# 名前消してたらエラー出して処理しない
			if (!strlen($this->user->name)) return($this->addError('empty_name'));

			$result = $this->SQLQuery("DELETE FROM %s WHERE cid = '%s';",
			 $this->tMember, PtSQL::R_BOOLEAN, $cid);
			if (!$result) return(false);

			$existCid = true;
			$this->user->enter();

		}

		# 離脱発言してたら離脱、それ以外の発言は入室
		if ($this->user->chat == PtConf::S('text/out')) $this->user->leave();
		 elseif (strlen($this->user->chat)) $this->user->enter();

		# 入室フラグ立ってたら追加
		if ($this->user->isJoined()) {

			$data = array($cid, $name, $color);
			$data = array_map(array($this->SQL, 'escape'), $data);
			array_unshift($data, time());

			$result = $this->SQLQuery("INSERT INTO %s VALUES (%d, '%s', '%s', '%s');",
			 $this->tMember, PtSQL::R_BOOLEAN, $data);
			if (!$result) return(false);

			# さっきまではいなかったらシステム発言とアナウンスも追加
			if (!$existCid) {

				$this->addSystemChat('innormal', $this->user->name, $this->user->color);
				$this->addMessage('chat_announce');

			}

		# さっきまでいたのにいなかったらシステム発言追加
		} elseif ($existCid) {

			$this->addSystemChat('outnormal', $this->user->name, $this->user->color);

		}

		return(true);

	}

	# 参加者一覧読み出し
	private function loadMember() {

		# タイムアウトしたメンバーを削除
		$timeout = time() - PtConf::getSelLast('reloadsec') * 2;
		$this->SQL->beginTransaction();

		$result = $this->SQLQuery("SELECT * FROM %s WHERE utime < %d;",
		 $this->tMember, PtSQL::R_ARRAY, $timeout);
		if ($result === false) return(false);

		if (count($result) > 0) foreach ($result as $member) {

			$result = $this->SQLQuery("DELETE FROM %s WHERE cid = '%s';",
			 $this->tMember, PtSQL::R_BOOLEAN, $member['cid']);
			if (!$result) return(false);

			$this->addSystemChat('outtimeout', $member['name'], $member['color']);

		}

		$this->SQL->commitTransaction();

		# メンバー一覧の取得
		$result = $this->SQLQuery("SELECT * FROM %s;",
		 $this->tMember, PtSQL::R_ARRAY);
		if ($result === false) return(false);

		if (count($result) > 0) foreach ($result as $line) {

			$utime = intval($line['utime']);
			$cid = PtUtil::escape($line['cid']);
			$name = PtUtil::escape($line['name']);
			$color = PtConf::normalizeColor($line['color']);
			$self = ($line['cid'] == $this->user->cid) ? 'yes' : 'no';

			$item = $this->data->addChild('member');
			$item->addChild('utime', $utime);
			$item->addChild('cid', $cid);
			$item->addChild('name', $name);
			$item->addChild('color', $color);
			$item->addChild('self', $self);

		}

		return(true);

	}

	# ログ数カウント
	private function getLineNum() {

		$result = $this->SQLQuery("SELECT COUNT(*) FROM %s;",
		 $this->tLog, PtSQL::R_STRING);
		if ($result === false) return(false);

		$lineNum = intval($result);

		return($lineNum);

	}

	# SQL にクエリを送る
	private function SQLQuery(/* $query, $table, $type, $arg */) {

		$funcArgs = func_get_args();
		$result = $this->SQL->query($funcArgs);

		if ($result === false) return($this->addError('sql_error'));

		return($result);

	}

	# メッセージをためとく
	private function addMessage($message) {

		$this->data->addChild('message', $message);

	}

	# エラーをためとく
	private function addError($message = 'ex') {

		$this->data->addChild('error', $message);

		# 行数がかさむのでここで false を返しておく
		return(false);

	}

}

?>