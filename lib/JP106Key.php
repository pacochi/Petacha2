<?php

# みかか語変換
# ぺたちゃ用に作りましたが他の場所でも自由に使っていただいて構いません

class JP106Key {

	const SyllableReg = '/([^aiueo]*)([aiueo]|$)/'; # 母音
	const GemConReg = '/([kqgszjtcdhfbvpmrwylx])(\\1)/'; # 促音
	const SylNasReg = '/(nn?|m)/'; # 撥音
	const LonVowReg = '/(\\-|－|h)/'; # 長音

	private static $baseMap = array( # 基本
	 'あ' => '３', 'い' => 'ｅ', 'う' => '４', 'え' => '５', 'お' => '６', 
	 'か' => 'ｔ', 'き' => 'ｇ', 'く' => 'ｈ', 'け' => '：', 'こ' => 'ｂ', 
	 'さ' => 'ｘ', 'し' => 'ｄ', 'す' => 'ｒ', 'せ' => 'ｐ', 'そ' => 'ｃ', 
	 'た' => 'ｑ', 'ち' => 'ａ', 'つ' => 'ｚ', 'て' => 'ｗ', 'と' => 'ｓ', 
	 'な' => 'ｕ', 'に' => 'ｉ', 'ぬ' => '１', 'ね' => '，', 'の' => 'ｋ', 
	 'は' => 'ｆ', 'ひ' => 'ｖ', 'ふ' => '２', 'へ' => '＾', 'ほ' => '－', 
	 'ま' => 'ｊ', 'み' => 'ｎ', 'む' => '］', 'め' => '／', 'も' => 'ｍ', 
	 'や' => '７', 'ゆ' => '８', 'よ' => '９', 'わ' => '０', 'ん' => 'ｙ', 
	 'ら' => 'ｏ', 'り' => 'ｌ', 'る' => '．', 'れ' => '；', 'ろ' => '＼', # ￥になるんだけどさ
	 'ー' => '￥', '゛' => '＠', '゜' => '［',
	);
	private static $shiftMap = array( # Shift
	 'ぁ' => '＃', 'ぃ' => 'Ｅ', 'ぅ' => '＄', 'ぇ' => '％', 'ぉ' => '＆', 
	 "ヵ" => 'Ｔ', 'き' => 'Ｇ', 'く' => 'Ｈ', 'ヶ' => '＊', 'こ' => 'Ｂ', 
	 'さ' => 'Ｘ', 'し' => 'Ｄ', 'す' => 'Ｒ', 'せ' => 'Ｐ', 'そ' => 'Ｃ', 
	 'た' => 'Ｑ', 'ち' => 'Ａ', 'っ' => 'Ｚ', 'て' => 'Ｗ', 'と' => 'Ｓ', 
	 'な' => 'Ｕ', 'に' => 'Ｉ', 'ぬ' => '！', '、' => '＜', 'の' => 'Ｋ', 
	 'ゎ' => 'Ｆ', 'ゐ' => 'Ｖ', 'ふ' => '”', 'ゑ' => '～', 'ほ' => '＝', 
	 'ま' => 'Ｊ', 'み' => 'Ｎ', '」' => '｝', '・' => '？', 'も' => 'Ｍ', 
	 'ゃ' => '’', 'ゅ' => '（', 'ょ' => '）', 'ん' => 'Ｙ', 
	 'ら' => 'Ｏ', 'り' => 'Ｌ', '。' => '＞', 'れ' => '＋', 'ろ' => '＿', 
	 'ー' => '｜', '゛' => '‘', '「' => '｛', 
	);
	private static $flippedMap; # $baseMap と $shiftMap のキーと値を反転させたもの
	private static $mergedMap; # $baseMap と $shiftMap を合わせたもの
	private static $romajiMap = array ( # ローマ字変換
	 'a' => array (
	  'tch' => 'っちゃ', 'ts' => 'つぁ', 'lk' => 'ヵ', 'xk' => 'ヵ', 'lw' => 'ゎ',
	  'sw' => 'すぁ', 'tw' => 'とぁ', 'dw' => 'どぁ', 'fw' => 'ふぁ', 'sh' => 'しゃ',
	  'zh' => 'じゃ', 'ch' => 'ちゃ', 'th' => 'てゃ', 'dh' => 'でゃ', 'wh' => 'うぁ',
	  'ky' => 'きゃ', 'gy' => 'ぎゃ', 'qy' => 'くゃ', 'sy' => 'しゃ', 'zy' => 'じゃ',
	  'jy' => 'じゃ', 'ty' => 'ちゃ', 'cy' => 'ちゃ', 'dy' => 'ぢゃ', 'ny' => 'にゃ',
	  'hy' => 'ひゃ', 'by' => 'びゃ', 'py' => 'ぴゃ', 'fy' => 'ふゃ', 'my' => 'みゃ',
	  'ry' => 'りゃ', 'vy' => 'ヴゃ', 'ly' => 'ゃ', 'xy' => 'ゃ',
	  'k' => 'か', 'g' => 'が', 'q' => 'くぁ', 's' => 'さ', 'c' => 'か', 'z' => 'ざ',
	  'j' => 'じゃ', 't' => 'た', 'd' => 'だ', 'n' => 'な', 'h' => 'は', 'b' => 'ば',
	  'p' => 'ぱ', 'f' => 'ふぁ', 'm' => 'ま', 'y' => 'や', 'r' => 'ら', 'w' => 'わ',
	  'v' => 'ヴぁ', 'l' => 'ぁ', 'x' => 'ぁ', '' => 'あ'
	 ),
	 'i' => array (
	  'tch' => 'っち', 'ts' => 'つぃ', 'dz' => 'ぢ',
	  'sw' => 'すぃ', 'tw' => 'とぃ', 'dw' => 'どぃ', 'fw' => 'ふぃ', 'sh' => 'し',
	  'zh' => 'じぃ', 'ch' => 'ち', 'th' => 'てぃ', 'dh' => 'でぃ', 'wh' => 'ゐ',
	  'ky' => 'きぃ', 'qy' => 'くぃ', 'gy' => 'ぎぃ', 'sy' => 'しぃ', 'jy' => 'じぃ',
	  'zy' => 'じぃ', 'cy' => 'ちぃ', 'ty' => 'ちぃ', 'dy' => 'ぢぃ', 'ny' => 'にぃ',
	  'hy' => 'ひぃ', 'by' => 'びぃ', 'py' => 'ぴぃ', 'fy' => 'ふぃ', 'my' => 'みぃ',
	  'ry' => 'りぃ', 'vy' => 'ヴぃ', 'wy' => 'ゐ', 'ly' => 'ぃ', 'xy' => 'ぃ',
	  'k' => 'き', 'q' => 'くぃ', 'g' => 'ぎ', 's' => 'し', 'c' => 'し', 'z' => 'じ',
	  'j' => 'じ', 't' => 'ち', 'd' => 'ぢ', 'n' => 'に', 'h' => 'ひ', 'b' => 'び',
	  'p' => 'ぴ', 'f' => 'ふぃ', 'm' => 'み', 'y' => 'い', 'r' => 'り', 'w' => 'うぃ',
	  'v' => 'ヴぃ', 'l' => 'ぃ', 'x' => 'ぃ', '' => 'い'
	 ),
	 'u' => array (
	  'tch' => 'っちゅ', 'lts' => 'っ', 'xts' => 'っ', 'ltu' => 'っ', 'ts' => 'つ', 
	  'sw' => 'すぅ', 'tw' => 'とぅ', 'dw' => 'どぅ', 'fw' => 'ふぅ', 'sh' => 'しゅ',
	  'zh' => 'じゅ', 'ch' => 'ちゅ', 'th' => 'てゅ', 'dh' => 'でゅ', 'wh' => 'うぅ',
	  'ky' => 'きゅ', 'qy' => 'くゅ', 'gy' => 'ぎゅ', 'sy' => 'しゅ', 'zy' => 'じゅ',
	  'jy' => 'じゅ', 'cy' => 'ちゅ', 'ty' => 'ちゅ', 'dy' => 'ぢゅ', 'ny' => 'にゅ',
	  'hy' => 'ひゅ', 'by' => 'びゅ', 'py' => 'ぴゅ', 'fy' => 'ふゅ', 'my' => 'みゅ',
	  'ry' => 'りゅ', 'vy' => 'ヴゅ', 'ly' => 'ゅ', 'xy' => 'ゅ',
	  'k' => 'く', 'q' => 'く', 'g' => 'ぐ', 's' => 'す', 'c' => 'く', 'z' => 'ず',
	  'j' => 'じゅ', 't' => 'つ', 'd' => 'づ', 'n' => 'ぬ', 'h' => 'ふ', 'b' => 'ぶ',
	  'p' => 'ぷ', 'f' => 'ふ', 'm' => 'む', 'y' => 'ゆ', 'r' => 'る', 'w' => 'う',
	  'v' => 'ヴ', 'l' => 'ぅ', 'x' => 'ぅ', '' => 'う'
	 ),
	 'e' => array (
	  'tch' => 'っちぇ', 'ts' => 'つぇ', 'lk' => 'ヶ', 'xk' => 'ヶ',
	  'sw' => 'すぇ', 'tw' => 'とぇ', 'dw' => 'どぇ', 'fw' => 'ふぇ', 'sh' => 'しぇ',
	  'zh' => 'じぇ', 'ch' => 'ちぇ', 'th' => 'てぇ', 'dh' => 'でぇ', 'wh' => 'ゑ',
	  'ky' => 'きぇ', 'qy' => 'くぇ', 'gy' => 'ぎぇ', 'sy' => 'しぇ', 'zy' => 'じぇ',
	  'jy' => 'じぇ', 'cy' => 'ちぇ', 'ty' => 'ちぇ', 'dy' => 'ぢぇ', 'ny' => 'にぇ',
	  'hy' => 'ひぇ', 'by' => 'びぇ', 'py' => 'ぴぇ', 'fy' => 'ふぇ', 'my' => 'みぇ',
	  'ry' => 'りぇ', 'vy' => 'ヴぇ', 'wy' => 'ゑ', 'ly' => 'ぇ', 'xy' => 'ぇ',
	  'k' => 'け', 'q' => 'くぇ', 'g' => 'げ', 's' => 'せ', 'c' => 'せ', 'z' => 'ぜ',
	  'j' => 'じぇ', 't' => 'て', 'd' => 'で', 'n' => 'ね', 'h' => 'へ', 'b' => 'べ',
	  'p' => 'ぺ', 'f' => 'ふぇ', 'm' => 'め', 'y' => 'いぇ', 'r' => 'れ', 'w' => 'うぇ',
	  'v' => 'ヴぇ', 'l' => 'ぇ', 'x' => 'ぇ', '' => 'え'
	 ),
	 'o' => array (
	  'tch' => 'っちょ', 'ts' => 'つぉ',
	  'sw' => 'すぉ', 'tw' => 'とぉ', 'dw' => 'どぉ', 'fw' => 'ふぉ', 'sh' => 'しょ',
	  'zh' => 'じょ', 'ch' => 'ちょ', 'th' => 'てょ', 'dh' => 'でょ', 'wh' => 'うぉ',
	  'ky' => 'きょ', 'qy' => 'くょ', 'gy' => 'ぎょ', 'sy' => 'しょ', 'zy' => 'じょ',
	  'jy' => 'じょ', 'cy' => 'ちょ', 'ty' => 'ちょ', 'dy' => 'ぢょ', 'ny' => 'にょ',
	  'hy' => 'ひょ', 'by' => 'びょ', 'py' => 'ぴょ', 'fy' => 'ふょ', 'my' => 'みょ',
	  'ry' => 'りょ', 'vy' => 'ヴょ', 'ly' => 'ょ', 'xy' => 'ょ',
	  'k' => 'こ', 'q' => 'くぉ', 'g' => 'ご', 's' => 'そ', 'c' => 'こ', 'z' => 'ぞ',
	  'j' => 'じょ', 't' => 'と', 'd' => 'ど', 'n' => 'の', 'h' => 'ほ', 'b' => 'ぼ',
	  'p' => 'ぽ', 'f' => 'ふぉ', 'm' => 'も', 'y' => 'よ', 'r' => 'ろ', 'w' => 'を',
	  'v' => 'ヴぉ', 'l' => 'ぉ', 'x' => 'ぉ', '' => 'お'
	 )
	);

	# かなで構成されているかをざっくり調べる
	public static function isKana($str) {

		$kanaNum = preg_match_all("/[ぁ-ヶ]/u", $str, $m);

		return($kanaNum * 2 > mb_strlen($str));

	}

	# vot@u→ひらがな
	public static function AtoK($str) {

		# 配列なかったら今のうちに生成
		if (!isset(self::$flippedMap)) {

			self::$flippedMap = array_flip(self::$baseMap) + array_flip(self::$shiftMap);
			# 濁点・半濁点をくっつくやつに
			self::$flippedMap['＠'] = "\xE3\x82\x99";
			self::$flippedMap['｀'] = "\xE3\x82\x99";
			self::$flippedMap['‘'] = "\xE3\x82\x99";
			self::$flippedMap['［'] = "\xE3\x82\x9A";
			# ヵとヶをひらがなに
			self::$flippedMap['Ｔ'] = "\xE3\x82\x95";
			self::$flippedMap['＊'] = "\xE3\x82\x96";

		}

		$str = mb_convert_kana($str, 'AS');
		# A オプションで変換されないもの
		$str = str_replace(array('"', '\'', '\\', '~'), array('”', '’', '￥', '～'), $str);
		$len = mb_strlen($str);
		$result = '';
		for ($i = 0; $i < $len; $i++) $result .= self::A2K(mb_substr($str, $i, 1));

		return($result);

	}

	# ちりせくちこいか→alphabet
	public static function KtoA($str) {

		# 配列なかったら今のうちに生成
		if (!isset(self::$mergedMap)) {

			self::$mergedMap = self::$baseMap + self::$shiftMap;
			# Shif + 0 で文字は出ない
			self::$flippedMap['を'] = '';
			# くっつく濁点・半濁点も一応
			self::$flippedMap["\xE3\x82\x99"] = '＠';
			self::$flippedMap["\xE3\x82\x9A"] = '［';
			# ひらがなのヵとヶも一応
			self::$flippedMap["\xE3\x82\x95"] = 'Ｔ';
			self::$flippedMap["\xE3\x82\x96"] = '＊';

		}

		$str = mb_convert_kana($str, 'Hc');
		$len = mb_strlen($str);
		$result = '';
		for ($i = 0; $i < $len; $i++) $result .= self::K2A(mb_substr($str, $i, 1));

		return($result);

	}

	# ro-maji→ろーまじ
	public static function AtoR($str) {

		$str = self::KtoA($str);
		$str = mb_convert_case($str, MB_CASE_LOWER);
		$str = mb_convert_kana($str, 'r');

		# 音節っぽいものがなかったら諦める
		if (!preg_match_all(self::SyllableReg, $str, $match, PREG_SET_ORDER))
		 return($str);

		$result = '';
		foreach ($match as $m) $result .= self::A2R($m[1], $m[2]);

		return($result);

	}

	# すらほもちまに→ろーまじ
	public static function KtoR($str) {

		return(self::AtoR(self::KtoA($str)));

	}

	# a→ち
	private static function A2K($char) {

		return(isset(self::$flippedMap[$char]) ? self::$flippedMap[$char] : $char);

	}

	# ち→a
	private static function K2A($char) {

		return(isset(self::$mergedMap[$char]) ? self::$mergedMap[$char] : $char);

	}

	# ka→か
	private static function A2R($consonant, $vowel) {

		# ラストが母音で終わってなかった時用
		if ($vowel == '') return(self::H2LV(self::N2SN(self::TT2GR($consonant))));
		# 母音だけ
		if (!strlen($consonant)) return(self::$romajiMap[$vowel]['']);

		# 促音
		$consonant = self::TT2GR($consonant);

		$result = '';

		foreach (self::$romajiMap[$vowel] as $c => $rep) {

			if ($c == '') continue;
			$reg = "/{$c}$/";
			if (preg_match($reg, $consonant)) {

				$result = preg_replace($reg, $rep, $consonant, 1);
				break;

			}

		}

		# 引っかからなかったら母音をつけとく
		if ($result == '') $result = $consonant . self::$romajiMap[$vowel][''];
		# 撥音と長音
		$result = self::H2LV(self::N2SN($result));

		return($result);

	}

	# 撥音の処理
	private static function N2SN($str) {

		return(preg_replace(self::SylNasReg, 'ん', $str));

	}

	# 長音の処理
	private static function H2LV($str) {

		return(preg_replace(self::LonVowReg, 'ー', $str));

	}

	# 促音の処理
	private static function TT2GR($str) {

		return(preg_replace(self::GemConReg, "っ$1", $str));

	}

}

?>