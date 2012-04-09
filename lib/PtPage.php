<?php

# ぺたちゃ　ページ表示クラス
# XML こさえて XSLT 通して適当にヘッダ出して出力

class PtPage {

	private $logs; # チャットログ
	private $user; # ユーザデータ
	private $xslFile; # XSL ファイル名
	private $xmlDoc; # こさえた XML
	private $xslDoc; # 読み込んだ XSL

	# コンストラクタ
	public function __construct($logs) {

		$this->logs = $logs;
		$this->user = $this->logs->getUser();

		$this->selectXSLFile()->setXSL();

		return(true);

	}

	# ページの表示
	public function output() {

		$this->setXML()->sendHeader();

		$proc = new XSLTProcessor();
		#$proc->registerPHPFunctions();
		$proc->importStylesheet($this->xslDoc);
		$html = $proc->transformToXML($this->xmlDoc);
		#$html = preg_replace("/(<(textarea|script) [^>]+)\/>/", "$1></$2>", $html);
		$html = str_replace('"/>', '" />', $html);
		PtUtil::exit200($html, $this->user->cid);

	}

	# XML データの作成
	private function setXML() {

		# RSS は Ajax 用ページと兼用
		$xmlDoc = ($this->user->isAjaxMode() || $this->user->isRSSMode())
		 ? $this->makeLogXML() : $this->makeBaseXML();

		# デバッグ用
		#exit($xmlDoc->saveXML());

		$this->xmlDoc = $xmlDoc;

		return($this);

	}

	# 通常ページ用データの作成
	private function makeBaseXML() {

		$xmlDoc = new DOMDocument('1.0', 'UTF-8');
		$xmlElm = $xmlDoc->createElement('page');
		$xmlDocPage = $xmlDoc->appendChild($xmlElm);

		# 設定
		$xmlElm = dom_import_simplexml(PtConf::getConf());
		$xmlElm = $xmlDoc->importNode($xmlElm, true);
		$xmlDocConf = $xmlDocPage->appendChild($xmlElm);

		# ユーザ固有情報
		$xmlElm = $xmlDoc->createElement('user');
		$xmlDocUser = $xmlDocPage->appendChild($xmlElm);

		# 入室フラグ、最後に取得したログの ID、cid、名前、色、行数、リロード
		$params = array('join', 'lastId', 'cid', 'name', 'color', 'line', 'reload');

		foreach ($params as $param)
		 $xmlDocUser->appendChild($xmlDoc->createElement(strtolower($param), $this->user->{$param}));

		# 選択肢
		$xmlElm = dom_import_simplexml($this->user->select);
		$xmlElm = $xmlDoc->importNode($xmlElm, true);
		$xmlDocUser->appendChild($xmlElm);

		# ログ
		$xmlElm = dom_import_simplexml($this->logs->getData());
		$xmlElm = $xmlDoc->importNode($xmlElm, true);
		$xmlDocLogs = $xmlDocPage->appendChild($xmlElm);

		return($xmlDoc);

	}

	# Ajax, RSS 用データの作成
	private function makeLogXML() {

		$xmlDoc = new DOMDocument('1.0', 'UTF-8');

		# ログ
		$xmlElm = dom_import_simplexml($this->logs->getData());
		$xmlElm = $xmlDoc->importNode($xmlElm, true);
		$xmlDocLogs = $xmlDoc->appendChild($xmlElm);

		# 設定
		$xmlElm = dom_import_simplexml(PtConf::getConfText());
		$xmlElm = $xmlDoc->importNode($xmlElm, true);
		$xmlDocConf = $xmlDocLogs->appendChild($xmlElm);

		return($xmlDoc);

	}

	# 読み込む XSL ファイルの判定
	private function selectXSLFile() {

		switch (true) {

		case $this->user->isRSSMode() :

			$xslFile = PtConf::S('path/xsl/rss');
			break;

		case $this->user->isAjaxMode() :

			$xslFile = PtConf::S('path/xsl/ajax');
			break;

		default :

			$xslFile = PtConf::S('path/xsl/base');

		}

		$this->xslFile = PtConf::S('path/dir/templates') . $xslFile;

		return($this);

	}

	# XSL ファイルの読み込み
	private function setXSL() {

		$xslDoc = new DOMDocument('1.0', 'UTF-8');
		$loadResult = $xslDoc->load($this->xslFile);

		if (!$loadResult) return(false);

		# デバッグ用
		#exit($xslDoc->saveXML());

		$this->xslDoc = $xslDoc;

		return(true);

	}

	# HTTP ヘッダの送信
	private function sendHeader() {

		$charset = mb_http_output();
		if ($charset == 'SJIS') $charset = 'Shift_JIS';

		# RSS
		if ($this->user->isRSSMode()) {

			header("Content-Type: application/xml" . (($charset && $charset != 'pass') ? "; charset={$charset}" : ''));
			return;

		}

		# IE の XML 扱い回避
		$mimeType = (preg_match("/application\/xhtml\+xml/", getenv('HTTP_ACCEPT'))) ? 'application/xhtml+xml' : 'text/html';
		header("Content-Type: {$mimeType}" . (($charset && $charset != 'pass') ? "; charset={$charset}" : ''));

		header('Content-Style-Type: text/css');
		header('Content-Script-Type: text/javascript');

		# リロード
		if ($this->user->isServerReloadMode()) {

			$scripturl = PtConf::S('text/scripturl');
			$lastId = $this->logs->getLastId();
			header("Refresh: {$this->user->reload}; URL={$scripturl}?r={$this->user->reload}#r{$lastId}");

		};

		return($this);

	}

}

?>
