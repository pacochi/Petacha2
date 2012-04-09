<?php

# ぺたちゃ　チャットテキスト操作クラス
# 表示する時に呼ばれる
# URL と BN の変換もここ

class PtText {

	private $chat = array(); # チャット
	private $ext; # 拡張タイプ
	private $bodyXMLStr; # SimpleXMLElement 作成時に使う文字列
	# BN の正規表現
	private $BNRegExp = '/((^P\\dID：)|セットID：|検証ID：|ﾀｰﾝ\\d-\\d：|戦士No\\.|検証ＩＤ：|ﾀｰﾝ\\d{1,2}\\/BN：)(\\d+)(.*$)/';
	# URL の正規表現 (日本語ドメインとか今のとこ未対応)
	private $URLRegExp = '_(http://ja\\.wikipedia\\.org/wiki/[^<>\\[\\]\\{\\}\s]+|https?://[/;:&=~@#!%\'\\w\\*\\(\\)\\?\\$\\.\\,\\-\\+]+)_';
	# システムメッセージの名前の正規表現
	private $NameRegExp = "/(\t(.+)\t(.+)\t)/";

	# コンストラクタ
	public function __construct($chat, $ext) {

		$this->ext = $ext;
		$this->chat = array(array('text' => $chat));

		if ($this->ext == 'system') {

			$this->markName();

		} elseif ($this->ext == 'command') {

			$this->markCmd();

		} else {

			$this->markURL()->markBN();

		}

		return(true);

	}

	# XML に追加
	public function addXML($log) {

		$log->addChild('body');

		foreach ($this->chat as $chat) {

			$type = key($chat);
			$str = current($chat);

			switch ($type) {

			case 'text' : 
			case 'url' : 
			case 'bn' : 
			case 'cmd' : 

				$log->body->addChild($type, $str);
				break;

			case 'name' : 

				$log->body->addChild('name', $chat['name']);
				$log->body->name['color'] = $chat['color'];
				break;

			default: 

			}

		}

		$log->addChild('ext', $this->ext);

		return(true);

	}


	# URL をマーキング
	private function markURL() {

		reset($this->chat);

		while (list($n, $chat) = each($this->chat)) {

			$type = key($chat);

			if ($type != 'text') continue;
			$str = current($chat);

			if (preg_match($this->URLRegExp, $str, $m)) {

				$url = $m[1];
				$rep = array();
				list($pre, $suf) = explode($url, $str, 2);
				if (strlen($pre)) $rep[] = array('text' => $pre);
				$rep[] = array('url' => $url);
				if (strlen($suf)) $rep[] = array('text' => $suf);
				array_splice($this->chat, $n, 1, $rep);
				for ($i = 0; $i <= $n; $i++) next($this->chat);

			}

		}

		return($this);

	}

	# BN をマーキング
	private function markBN() {

		reset($this->chat);

		while (list($n, $chat) = each($this->chat)) {

			$type = key($chat);

			if ($type != 'text') continue;
			$str = current($chat);

			if (!preg_match("/\\d{2,}/", $str)) continue;

			if (preg_match($this->BNRegExp, $str, $m)) {

				$bn = $m[3];
				if ($m[2]) $bn .= $m[4];
				$rep = array();
				list($pre, $suf) = explode($bn, $str, 2);
				$rep[] = array('text' => $pre);
				$rep[] = array('bn' => $bn);
				if (strlen($suf)) $rep[] = array('text' => $suf);
				array_splice($this->chat, $n, 1, $rep);
				for ($i = 0; $i <= $n; $i++) next($this->chat);

			}

		}

		return($this);

	}

	# システム発言の名前をマーキング
	private function markName() {

		# 一回しか置換しないような気持ち
		foreach ($this->chat as $n => $chat) {

			$type = key($chat);
			if ($type != 'text') continue;
			$str = current($chat);

			 if (preg_match($this->NameRegExp, $str, $m)) {

				$hit = $m[1];
				$name = $m[2];
				$color = $m[3];
				$rep = array();
				list($pre, $suf) = explode($hit, $str, 2);
				if (strlen($pre)) $rep[] = array('text' => $pre);
				$rep[] = array('name' => $name, 'color' => $color);
				if (strlen($suf)) $rep[] = array('text' => $suf);
				array_splice($this->chat, $n, 1, $rep);

			}

		}

		return($this);

	}

	# コマンドをマーキング
	private function markCmd() {

		$this->chat = array(array('cmd' => $this->chat[0]['text']));

		return($this);

	}

}

?>