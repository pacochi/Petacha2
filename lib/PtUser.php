<?php

# ぺたちゃ　ユーザクラス
# 送信情報の読み込みと整理

class PtUser {

	private $select; # ユーザのチョイス付き選択肢 (SimpleXML)
	private $q = array(); # 送信されたクエリ
	private $join = false; # 参加中なら true
	private $lastId = 0; # 最後に取得したログの ID (加工)
	private $cid = ''; # 簡易 ID
	private $name = ''; # 名前 (未加工)
	private $chat = ''; # チャット発言 (未加工)
	private $color = ''; # 色 (加工)
	private $reload = 0; # リロード秒数 (加工)
	private $line = 0; # 行数 (加工)

	public $mode = ''; # 'join' 'chat'

	# コンストラクタ
	public function __construct() {

		$this->loadQuery()->setStat()->setMySelect();

		return(true);

	}

	# 変数を readonly に
	public function __get($name) {

		return($this->{$name});

	}

	# アクセス制限がかかってるかチェック
	public function isDenied() {

		$denied = false;

		if (PtUtil::arrayPos(PtConf::C('deny/cid'), $this->cid, true) !== false
		 || PtUtil::arrayPos(PtConf::C('deny/host'), PtConf::S('vars/rhost'), true) !== false
		 || PtUtil::arrayPos(PtConf::C('deny/ua'), getenv('HTTP_USER_AGENT'), true) !== false
		 || PtUtil::arrayPos(PtConf::C('deny/ref'), getenv('HTTP_REFERER'), true) !== false)
		 $denied = true;

		return($denied);

	}

	# RSS モードかどうかチェック
	public function isRSSMode() {

		return(isset($this->q['rss']));

	}

	# Ajax モードかどうかチェック
	public function isAjaxMode() {

		return(isset($this->q['a']) && $this->q['a'] > 0);

	}

	# クライアント側で XSLT できるかどうかチェック
	public function isClientXSLTMode() {

		return(isset($this->q['a']) && $this->q['a'] == 2);

	}

	# サーバ側で自動リロードする必要があるかどうかチェック
	public function isServerReloadMode() {

		return(!$this->isAjaxMode() && isset($this->q['r']) && $this->reload > 0);

	}

	# 入室中かどうかチェック
	public function isJoined() {

		# こまごました処理が加わるかもしれない

		return($this->join);

	}

	# 入室
	public function enter() {

		$this->join = true;

		return($this);

	}

	# 退室
	public function leave() {

		$this->join = false;

		return($this);

	}

	# 名前のリセット
	public function resetName() {

		$this->name = $_SESSION['name'] = '';

		return($this);

	}

	# セッション開始
	public function startSession() {

		$expSec = 60 * 60 * 24 * intval(PtConf::S('vars/sesdays'));
		$url = parse_url(PtConf::S('text/scripturl'));
		$domain = $url['host'];
		$path = preg_replace("/[^\/]*$/", '', $url['path']);

		session_name(PtConf::S('vars/sesname'));
		session_save_path(PtConf::S('path/dir/session'));
		session_set_cookie_params ($expSec, $path, $domain);
		ini_set('session.gc_maxlifetime', $expSec);
		session_start();

		return($this);

	}

	# セッション終了
	public function closeSession() {

		session_write_close();

		return($this);

	}

	# 送信情報読み込み
	private function loadQuery() {

		$query = array_merge($_GET, $_POST);

		if (get_magic_quotes_gpc()) $query = array_map('stripslashes', $query);

		# 改行全部要らない (システム発言ではメタ文字として \t を使う)
		# ヌルバイトを \0 で表すと php のバージョンによって動作が変わるらしい
		$removeStr = array(chr(0), "\r", "\n", "\t");
		$query = str_replace($removeStr, ' ', $query);

		$this->q = $query;

		return($this);

	}

	# ユーザ個別の設定
	private function setStat() {

		# RSS はほぼデフォルト値
		if ($this->isRSSMode()) {

			$this->chat = '';
			$this->lastId = 0;
			$this->cid = PtUtil::makeCid();
			$this->name = '';
			$this->color = '';
			$this->line = intval(PtConf::getSelDefault('line'));
			$this->reload = 0;

			return($this);

		}

		$q = $this->q;

		$this->startSession();

		# 発言
		$chat = (isset($q['m']) && strlen($q['m'])) ? trim($q['m']) : '';
		$this->chat = $chat;

		# 最後に取得したログ ID
		$lastId = (isset($q['i']) && intval($q['i']) > 0) ? intval($q['i']) : 0;
		$this->lastId = $lastId;

		# cid (色として使える簡易 ID)
		$cid = (isset($_SESSION['cid']) && PtUtil::checkCid($_SESSION['cid'])) ? $_SESSION['cid'] : PtUtil::makeCid();
		$this->cid = $_SESSION['cid'] = $cid;

		# 名前
		$name = (isset($q['n']) && strlen($q['n'])) ? trim($q['n']) : '';
		if ($name == '' && isset($_SESSION['name'])) $name = $_SESSION['name'];
		$this->name = $_SESSION['name'] = $name;

		# 色
		$color = (isset($q['c']) && strlen($q['c'])) ? trim($q['c']) : '';
		if ($color == '' && isset($_SESSION['color'])) $color = $_SESSION['color'];
		$color = PtConf::normalizeColor($color);
		$this->color = $_SESSION['color'] = $color;

		# 行数
		$lineMin = intval(PtConf::getSelFirst('line'));
		$lineMax = intval(PtConf::getSelLast('line'));

		$line = (isset($q['l']) && is_numeric($q['l'])) ? intval($q['l']) : 0;
		if ($line < $lineMin && isset($_SESSION['line'])) $line = $_SESSION['line'];
		if ($line < $lineMin || $line > $lineMax) $line = intval(PtConf::getSelDefault('line'));
		$this->line = $_SESSION['line'] = $line;

		# リロード
		$reloadMin = intval(PtConf::getSelFirst('reloadsec'));
		$reloadMax = intval(PtConf::getSelLast('reloadsec'));

		$reload = (isset($q['r']) && is_numeric($q['r'])) ? intval($q['r']) : -1;
		if ($reload < $reloadMin && isset($_SESSION['reload'])) $reload = $_SESSION['reload'];
		if ($reload < $reloadMin || $reload > $reloadMax) $reload = intval(PtConf::getSelDefault('reloadsec'));
		$this->reload = $_SESSION['reload'] = $reload;

		return($this);

	}

	# 選択肢のデフォルト位置をユーザ個別の設定に合わせる
	private function setMySelect() {

		$select = current(PtConf::C('select'));
		$select = new SimpleXMLElement($select->asXML());

#$c = PtConf::C('select/color[.="red"]');
		# 色、行数、リロード
		$sets = array('color' => $this->color, 'line' => $this->line, 'reloadsec' => $this->reload);

		foreach ($sets as $key => $val) {

			foreach ($select->{$key} as $item)
			 $item['default'] = (strval($val) == strval($item)) ? 'yes' : 'no';

			if (count($select->xpath("/select/{$key}[@default='yes']")) == 0) {

				$item = $select->addChild($key, strval($val));
				$item['default'] = 'yes';

			}

		}

		$this->select = $select;

		return($this);

	}

}

?>