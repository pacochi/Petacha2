/*

ぺたちゃ2 のクライアントサイドスクリプト (peta2.js)
書いた人 : kaska
みんなで使ってね。なかよく使ってね。

*/

(function() { // タブ省略
if (typeof(window.PT2) == 'undefined') { // タブ省略

// インスタンス化とかしないで直に使うおもちゃ箱
window.PT2 = {};
// conf.xml 読む前に決めること
PT2.version = 170106; // よく変え忘れるけど気にしないでね
PT2.confFile = './conf.xml';
PT2.URL = location.href.replace(/[#\?].*$/, '');
PT2.BNRegExp = /(ver\s?\d+\.|戦士No\.|検証ＩＤ：|ﾀｰﾝ\d+\/BN：|P\dID：|検証ID：|ﾀｰﾝ\d-\d：|セットID：)\d+/;
// conf.xml 読んで決めること
// 一応初期値割り当てとく
PT2.xslFile = './templates/log.xsl';
PT2.copySwfFile = './resources/Peta2Copy.swf';
PT2.logDir = './logs/';
PT2.reloadCountOption = 'on';
PT2.text = {
 title: 'ぺたちゃ2',
 join: '(参加中)',
 out: '離脱',
 reload: 'リロード',
 spliter: ' : ',
 said: ' &gt; ',
 announce: '※',
 close: '×',
 noreload: '手動',
 autopaste: 'オートペースト',
 backup: 'バックアップ',
 pastlog: '過去ログ',
 view: '閲覧',
 reverselog: '逆順 (閲覧ツール用)'
};
PT2.text.message = {
 chat_announce: '離脱 (退室) する時は「離脱」と発言して下さい。',
 entrance_announce: '名前を入力して発言すると、参加 (入室) できます。',
 entrance_announce_ajax: '負荷軽減のため、リロードにはリロードボタンをお使い下さい。(このページ自体を更新しないで下さい。)',
 log_announce: '日付などを指定したログの閲覧時は、自動リロードが一時的に無効になります。'
};
PT2.text.error = {
 malformed_cid: '不正な cid です。',
 method_not_post: '不正なメソッドです。',
 empty_name: '名前が入力されていません。',
 long_name: '名前が長過ぎます。',
 long_message: '本文が長過ぎます。',
 sql_error: 'データベースの処理中にエラーが発生しました。',
 unknown_command: '認識できないコマンドです。',
 failed_xsl_load: 'XSL ファイルの読み込みに失敗しました。ページを開き直して下さい。',
 failed_xml_load: '指定した日付のログは存在しないようです。',
 failed_php_load: 'ログの読み込みに失敗しました。',
 copy_not_ready: 'まだコピーの準備ができていません。',
 invalid_xml_filename: '日付の指定にミスがあります。',
 ex: '予期せぬエラーです。'
};
// 色々準備が終わってから使う値
PT2.joinText = '';
PT2.toXSLDoc = false;
PT2.clipboardData = null;
PT2.XSLTProcessor = null;
PT2.reloadTimer = false;
PT2.reloadCounter = 0;
PT2.logXSL = null;
PT2.copySwf = null;
PT2.body = null;
PT2.h1 = null;
PT2.uMember = null;
PT2.dChat = null;
PT2.dWrite = null;
PT2.dTmp = null;
PT2.dPast = null;
PT2.fSay = null;
PT2.fView = null;
PT2.exSelf = null;
PT2.responseType = null;
PT2.input = {};
PT2.S = {}; // スタートとかセットアップとか
PT2.F = {}; // フォームとか
PT2.C = {}; // チャットとか過去ログとか
PT2.X = {}; // ajax とか XSLT とか
PT2.A = {}; // アラートとか
PT2.P = {}; // クリップボードとか

// Dom と設定ファイルの準備ができたらすること
PT2.S.init = function() {

	PT2
	 .S.saveVars()
	 .F.setAjax()
	 .F.setLineResetter()
	 .F.setColorPicker()
	 .F.setReloadButton()
	 .F.setAutoPaste()
	 .F.setOutButton()
	 .F.disableButton()
	 .C.setChatAdjuster()
	 .C.addLogViewer()
	 .C.movableLogViewer()
	 .C.processChatLink()
	 .C.processBNElm()
	 .C.processAlert()
	 .C.movePastLog()
	 .C.addEntranceMessage();

};

// window.clipboardData の代替を作る
// かなり無理がある
PT2.S.clipboardData = function() {

	var clip = {};
	clip.swf = true;
	clip.getData = null;
	clip.setData = null;

	return(clip);

};

// 拡張機能利用時に使う
PT2.S.clipboardDataEx = function() {

	var clip = {};
	clip.swf = true;
	clip.getData = true;
	clip.setData = null;

	return(clip);

};

// window.XSLTProcessor の代替を作る
PT2.S.XSLTProcessor = function() {

	// IE7 で XSLT できない、原因はまだ特定してない
	if (typeof(document.documentMode) != 'number' || document.documentMode < 8)
	 return(null);

	var xslt = function() {};
	xslt.prototype.xsl = null;
	xslt.prototype.importStylesheet = function(xsl) { this.xsl = xsl; };
	xslt.prototype.transformToFragment = function(xml, doc) { return(xml.transformNode(this.xsl)); };

	// IE10 でこれつけないと XSLT できない
	// でもへたにつけっぱにすると Chrome とかで読み込めなくなる
	// beforeSend: function (xhr, settings) { try { xhr.responseType = 'msxml-document'; } catch(err) {} },
	// って方のやり方だと何故かつけたことにならなかった
	if (document.documentMode >= 10) PT2.responseType = 'msxml-document';

	return(xslt);

};

// 使いまわすオブジェクトを変数に保存
PT2.S.saveVars = function() {

	PT2.body = $('body');
	PT2.h1 = $('h1');
	PT2.uMember = $('ul#member');
	PT2.dChat = $('div#chat');
	PT2.dWrite = $('div#write');
	PT2.fSay = $('form#say');
	PT2.input = {
	 m: $('input#m'),
	 s: $('button#s'),
	 n: $('input#n'),
	 c: $('select#c'),
	 l: $('select#l'),
	 r: $('select#r'),
	 a: $('input#a'),
	 i: $('input#i')
	};

	// WebKit 系は XSL に ownerDocument を合わせないと XSLT できない
	// XSLTProcessor での判別はテストデータ用意しないと無理っぽいから簡易判別
	// 2012.4.8 に 17.0.963.12 dev-m で XSLT もできるし $.support.checkOn も true になってることを確認
	// 関係ない機能で判別してたから両方オンになったのは単なる偶然、ヒヤヒヤもの
	if (!$.support.checkOn) PT2.toXSLDoc = true;
	// clipboardData のラッパー
	if (typeof(window.clipboardData) != 'undefined') {

		PT2.clipboardData = window.clipboardData;

	} else if (typeof(navigator.plugins) != 'undefined'){

		var flash = 'application/x-shockwave-flash';
		var plugin = (navigator.mimeTypes[flash]) ? navigator.mimeTypes[flash].enabledPlugin : null;
		var version = (plugin && plugin.description) ? plugin.description.split(' ').slice(-2, -1) : 0;

		if (version > 10) {

			// Chrome はページのスクリプトにちょっかい出せないから DOM にちょっかい
			// Firefox は拡張機能の方で PT2.S.clipboardData を PT2.S.clipboardDataEx に挿げ替えてる
			PT2.clipboardData = (PT2.input.m.hasClass('apchrome')) ? PT2.S.clipboardDataEx() : PT2.S.clipboardData();
			PT2.P.addCopySwf();

		}

	}

	// XSLTProcessor のラッパー
	if (typeof(window.XSLTProcessor) != 'undefined') PT2.XSLTProcessor = window.XSLTProcessor;
	 else if (typeof(window.ActiveXObject) != 'undefined' || 'ActiveXObject' in window) PT2.XSLTProcessor = PT2.S.XSLTProcessor();

	return(PT2);

};

// フォーム送信を Ajax 仕様にする
PT2.F.setAjax = function() {

	PT2.fSay.submit(PT2.X.post);
	PT2.input.a.val(PT2.XSLTProcessor ? '2' : '1');

	PT2.body.tag('div').attr('id', 'tmp').hide().gat();
	PT2.dTmp = $('div#tmp');

	return(PT2);

};

// カラーピッカー設置
// 何か探すのめんどくなって自前にした
PT2.F.setColorPicker = function(colors) {

	PT2.body.tag('div').attr('id', 'picker').gat();
	PT2.picker = $('#picker');
	PT2.picker.tag('div').addClass('box').gat().hide();
	var box = $('div.box', PT2.picker);

	// パレット作成
	if (!colors) colors = PT2.F.makeColorPallet();

	var sel = function() {

		var col = this.title;
		if (!PT2.input.c.is(':has(option[value=' + col + '])'))
		 PT2.input.c.tag('option').css('color', col).attr('value', col).text(col).gat();
		PT2.input.c.val(col);
		PT2.F.changeColor();

	};

	$.each(colors, function() {

		var col = this.toString();
		box.tag('div').css('backgroundColor', col)
		 .attr('title', col).click(sel).text(' ').gat();

	});

	PT2.input.c.click(PT2.F.openColorPicker).change(PT2.F.changeColor)
	 .parent().addClass('colorpick');
	PT2.F.changeColor();

	return(PT2);

};

// デフォルトのカラーパレット
PT2.F.makeColorPallet = function() {

	var colors = [];
	var n = [0, 0, 2, 1, 1, 3];
	var i, h, s, v;
	var hsv2rgb = function(h, s, v) {

		var p = parseInt(h);
		var f = h - p;
		var c = [v, v * (1 - s), v * (1 - s * f), v * (1 - s * (1 - f))];
		for (var i = 0; i < 4; i++) c[i] = parseInt(c[i] * 15).toString(16);

		return('#' + c[n[(p + 5) % 6]] + c[n[(p + 1) % 6]] + c[n[(p + 3) % 6]]);

	};

	// 最初の 6色はモノクロ
	for (i = 0; i < 6; i++) {

		v = (i * 3).toString(16);
		colors.unshift('#' + v + v + v);

	}

	// (2 + 4) * 12 くらい回す
	for (h = 0; h < 6; h += 0.5) {

		v = 1;
		for (s = 0.5; s < 1; s += 0.25) colors.push(hsv2rgb(h, s, v));
		s = 1;
		for (v = 1; v >= 0.25; v -= 0.25) colors.push(hsv2rgb(h, s, v));

	}

	return(colors);

};

// カラーピッカーを開く
PT2.F.openColorPicker = function() {

	var offset = PT2.input.c.offset();
	offset = { top: Math.min(offset.top, PT2.body.height() - PT2.picker.height()), left: offset.left + PT2.input.c.width() };
	PT2.picker.css({ top: offset.top + "px", left: offset.left + "px"}).toggle('fast', function() {

		if (PT2.picker.is(':visible')) PT2.body.click(PT2.F.closeColorPicker);

	});

};

// カラーピッカーを閉じる
PT2.F.closeColorPicker = function() {

	PT2.body.unbind('click', PT2.F.closeColorPicker);
	PT2.picker.hide('fast');

};

// サンプル文字の色を変更
PT2.F.changeColor = function() {

	PT2.input.c.parent().css('color', PT2.input.c.val());

};

// 行数変更時にログ読み直しイベント追加
PT2.F.setLineResetter = function() {

	PT2.input.l.change(PT2.X.superReload);

	return(PT2);

};

// リロードボタン設置
PT2.F.setReloadButton = function() {

	// IE は button 要素の type 属性が書き換えられない
	PT2.fSay.tag('p')
	 .tag('button type="button"').attr('id', 'reload').text(PT2.text.reload).gat()
	 .gat();

	PT2.input.reload = $('button#reload');
	PT2.input.reload.click(PT2.X.reload);

	// オートリロード
	PT2.input.r.change(PT2.F.setReloadTimer);
	PT2.F.setReloadTimer();

	return(PT2);

};

// 自動リロードを作動させる
PT2.F.setReloadTimer = function() {

	PT2.reloadCounter = parseInt(PT2.input.r.val(), 10);

	if (PT2.reloadCounter > 0) {

		PT2.F.countDownTimer();

	} else {

		if (PT2.reloadTimer) clearTimeout(PT2.reloadTimer);
		PT2.input.reload.text(PT2.text.reload
		 + ((PT2.reloadCountOption == 'on') ? PT2.text.spliter + PT2.text.noreload : ''));

	}

};

// リロードまで一秒ずつ測る
PT2.F.countDownTimer = function() {

	if (PT2.reloadTimer) clearTimeout(PT2.reloadTimer);

	if (PT2.reloadCounter > 0) {

		PT2.reloadCounter--;
		PT2.F.drawReloadCount();
		PT2.reloadTimer = setTimeout(PT2.F.countDownTimer, 1000);

	} else {

		PT2.reloadTimer = false;
		PT2.F.drawReloadCount();
		PT2.X.autoReload();

	}

};

// リロード残り秒数を表示
PT2.F.drawReloadCount = function() {

	if (PT2.reloadCountOption != 'on') return;

	var count = PT2.text.spliter
	 + parseInt(PT2.reloadCounter / 100, 10)
	 + parseInt(PT2.reloadCounter % 100 / 10, 10)
	 + PT2.reloadCounter % 10;
	PT2.input.reload.text(PT2.text.reload + count);

};

// オートペースト設置
PT2.F.setAutoPaste = function() {

	// 今のとこ IE 以外無効になってる
	if (!PT2.clipboardData || !PT2.clipboardData.getData) return(PT2);

	// オートペーストオンオフ用のチェックボックスとバックアップ
	$('p:last', PT2.fSay).tag('label').text(PT2.text.autopaste + PT2.text.spliter)
	 .tag('input').prop('checked', true).attr({ type: 'checkbox', id: 'autopaste', name: 'autopaste' }).val('1').gat()
	 .gat()
	 .tag('label').text(PT2.text.backup + PT2.text.spliter)
	// ログにむっちゃ残るから「name: 'backup', 」はおあずけ
	 .tag('input').attr({ type: 'text', id: 'backup', readonly: 'readonly' }).val('').gat()
	 .gat();

	PT2.input.autopaste = $('input#autopaste');
	PT2.input.backup = $('input#backup');
	PT2.input.reload.width(PT2.dWrite.width() * 0.4);
	if (!PT2.input.m.hasClass('apchrome')) PT2.input.m.focus(PT2.P.paste);

	return(PT2);

};

// 離脱ボタン設置
PT2.F.setOutButton = function() {

	$('p:last', PT2.fSay).tag('button type="submit"').attr('id', 'out').text(PT2.text.out).gat();

	PT2.input.out = $('button#out');
	PT2.input.button = $('button', PT2.fSay);
	PT2.input.out.click(function() { PT2.input.m.val(PT2.text.out); });
	PT2.joinText = $('li.self', PT2.uMember).length ? PT2.text.join : '';

	return(PT2);

};

// 最後の発言 id をセットする
PT2.F.setLastId = function(reset) {

	if (PT2.dChat.is(':has(p.chat)')) {

		var lastId = reset ? '0' : $('p.chat:last', PT2.dChat).attr('id').substr(1);
		PT2.input.i.val(lastId);

	}

	return(PT2);

};

// 発言欄をクリア
PT2.F.clearSaying = function() {

	PT2.input.m.val('').focus();

	return(PT2);

};

// 発言とリロードの抑制
PT2.F.disableButton = function() {

	PT2.input.button.attr('disabled', 'disabled');

	return(PT2);

};

// 発言とリロードの開放
PT2.F.enableButton = function() {

	PT2.input.button.prop('disabled', false);
	if (PT2.joinText == '') PT2.input.out.attr('disabled', 'disabled');

	return(PT2);

};

// リサイズ時に発言一覧の高さ調整
PT2.C.setChatAdjuster = function() {

	if (typeof(document.ontouchstart) != 'undefined') return(PT2);

	$(window).resize(PT2.C.adjustChatHeight);
	PT2.C.adjustChatHeight();

	return(PT2);

};

// 発言一覧の高さを調整する
PT2.C.adjustChatHeight = function() {

	PT2.dChat.height($('div#body').height() - PT2.dWrite.height());

	return(PT2);

};

// 発言中のリンクの加工
PT2.C.processChatLink = function() {

	// 今のとこ別窓で開くようにするだけ
//	$('a.chatlink', PT2.dChat).live('click', function() {
	PT2.dChat.on('click', 'a.chatlink', function() {

		window.open(this.href, '_blank');
		return(false);

	});

	return(PT2);

};

// チャット発言と一緒の画面に出るアラートの加工
PT2.C.processAlert = function() {

	// クリックで閉じる
//	$('button.close', PT2.dChat).live('click', function() {
	PT2.dChat.on('click', 'button.close', function() {

		$(this).parent().slideUp('slow', function(){ $(this).remove(); });

	});

	// 既にあるアラートに閉じるボタン付加
	$('p.note,p.alert', PT2.dChat).each(function() {

		var _this = $(this);
		if (!_this.is(':has(button)'))
		 _this.tag('button type="button"').addClass('close').text(PT2.text.close).gat();

	});

	return(PT2);

};

// BN が入ってる要素の加工
PT2.C.processBNElm = function() {

	// Flash でコピー可能に
	// この辺すぐ削除する予定なんだけどとりあえずアップグレードはする
	// でもクリック何度かしないと有効にならないしもう使わないと思う
	if (PT2.clipboardData && PT2.clipboardData.swf) {

		// マウス乗っけた時に透明なボタンを重ねる
//		$('label.bn').live('mouseover', function() {
		PT2.dChat.on('mouseover', 'label.bn', function(e) {

			var _this = $(this);
			var offset = _this.offset();
			PT2.P.moveCopySwf(offset.left, offset.top + 1,
			 Math.min(_this.width(), PT2.dChat.width()),
			 _this.height()).P.setCopyText(_this.text(), this.id);
			 e.stopPropagation();

		});

		$('#chat,#past').mouseover(function() {

			if (PT2.copySwf.width() > 1) PT2.P.moveCopySwf(-100);

		});

	} else if (PT2.clipboardData) {

		// IE での BN クリック時の動作
//		$('input.bn').live('focusin', function() {
		PT2.dChat.on('focusin', 'input.bn', function() {

			PT2.P.copy(this.value).P.addCopiedMark(this.parentNode);
			this.blur();

		});

	}

	// 既にある BN をクリッカブルに
	if (PT2.clipboardData) $('em.bn', PT2.dChat).each(function(i) {

		var _this = $(this);
		var bn = _this.text();
		var bnid = 'bn' + _this.parent().attr('id') + '-' + i;
		var label = $('<label />').attr('id', bnid).addClass('bn')
		 .tag('input').addClass('bn').attr({ type: 'radio', name: 'bn' }).val(bn).gat()
		 .append(bn);
		_this.before(label).remove();

	});

	PT2.C.addAccKey();

	return(PT2);

};

// BN のアクセスキーの書き換え
PT2.C.addAccKey = function() {

	// IE 以外だとクリック以外でコピーできないから意味無い
	if (!PT2.clipboardData || !PT2.clipboardData.setData) return(PT2);

	var aKey = 0;
	$($('input.bn', PT2.dChat).get().reverse()).each(function() {

		aKey++;
		if (aKey < 10) $(this).attr('accesskey', aKey);
		 else $(this).removeAttr('accesskey');

	});

	return(PT2);

};

// 過去ログ表示用フォームの追加
PT2.C.addLogViewer = function() {

	if (PT2.input.a.val() < 1) return(PT2);

	var yday = new Date();
	yday.setTime(yday.getTime() - 86400000);

	PT2.body.tag('form').attr('id', 'view').submit(PT2.X.loadPastLog)
	 .tag('p').addClass('logdate')
	  .tag('input').attr('id', 'year').val(yday.getFullYear()).gat()
	  .tag('input').attr('id', 'month').val(yday.getMonth() + 1).gat()
	  .tag('input').attr('id', 'day').val(yday.getDate()).gat()
	  .tag('input').attr({ type: 'checkbox', id: 'reverselog' }).gat()
	  .tag('label').attr('for', 'reverselog').text(PT2.text.reverselog).gat()
	  .tag('button').attr('id', 'getlog').text(PT2.text.view).gat()
	  .tag('textarea').attr('id', 'logtext').focus(function() { $(this).select(); }).gat()
	  .tag('button type="button"').addClass('close').click(function() { PT2.fView.hide('slow'); }).text(PT2.text.close).gat()
	 .gat().tag('div').attr('id', 'past').gat().gat();

	$('ul.navi').tag('li')
	 .tag('a').attr('href', '#').click(function () { PT2.fView.show('slow'); return(false); }).text(PT2.text.pastlog).gat()
	 .gat();

	PT2.fView = $('#view');
	PT2.dPast = $('#past');
	PT2.input.year = $('#year');
	PT2.input.month = $('#month');
	PT2.input.day = $('#day');
	PT2.input.reverselog = $('#reverselog');
	PT2.input.getlog = $('#getlog');
	PT2.input.logtext = $('#logtext');
	PT2.input.button = $('#say button,#getlog');

	return(PT2);

};

// 過去ログ表示用フォームを動かせるようにする
PT2.C.movableLogViewer = function() {

	if (!PT2.fView) return(PT2);

	var pos = PT2.fView.position();

	PT2.fView.mousedown(function(e) {

		var mov = { left: e.pageX, top: e.pageY };

		PT2.fView.mousemove(function(e) {

			pos.left += e.pageX - mov.left;
			pos.top += e.pageY - mov.top;
			PT2.fView.css({ left: pos.left + 'px', top: pos.top + 'px' });
			mov = { left: e.pageX, top: e.pageY };

			return(false);

		}).one('mouseup', function() {

			PT2.fView.unbind('mousemove');

		});

		return(false);

	}).mouseout(function(e) {

		PT2.fView.unbind('mousemove');

	});

	PT2.fView.children().mousedown(function(e){

		e.stopPropagation();

	});

	PT2.fView.hide();

	return(PT2);

};

// ページ開きたての時にメッセージを表示する
PT2.C.addEntranceMessage = function() {

	PT2.A.addNote(PT2.text.message.entrance_announce_ajax).C.scrollToLatest();

	return(PT2);

};

// 最新発言までスクロール
PT2.C.scrollToLatest = function() {

	PT2.dChat.scrollTop(PT2.dChat.get(0).scrollHeight);

	return(PT2);

};

// 過去ログを受け取って表示
PT2.C.showPastLog = function(logs) {

	var reverse = PT2.input.reverselog.is(':checked');
	var logText = '';

	PT2.dPast.empty().tag('div').attr('id', 'pastcontainer').html(logs).gat();
	var pastcontainer = $('#pastcontainer');
	$('li', pastcontainer).remove();

	$('p', pastcontainer).each(function() {

		if (this.id) this.id = 'past-' + this.id;

		if (this.className == 'alert') PT2.dChat.append($(this));
		 else if (reverse) {

			var _this = $(this);
			PT2.dPast.prepend(_this.append('\n'));
			logText = _this.clone().children('span.cid,span.date').text('').parent().text() + logText;

		}

	});

	if (logText) PT2.input.logtext.val(logText).show();
	 else PT2.input.logtext.hide();

	if (PT2.fView.is(':hidden')) PT2.fView.show('slow');

	PT2.F.enableButton();

};

// 過去ログを過去ログ表示部に移動
PT2.C.movePastLog = function() {

	if (!PT2.fView) return(PT2);

	// 過去ログ閲覧モードじゃなかったら帰る
	if ($('p.note', PT2.dChat).is('[title="log_announce"]') == false) return(PT2);

	// 初回表示時しか使わないからリセットなし
	PT2.dPast.tag('div').attr('id', 'pastcontainer').gat();
	var pastcontainer = $('#pastcontainer');

	$('p.chat', PT2.dChat).each(function() {

		if (this.id) this.id = 'past-' + this.id;
		pastcontainer.append($(this));

	});

	// 日付を合わせる
	if (location.href.match(/([\?&]f=|#!\/)log-(\d{4})(\d{2})(\d{2})/)) {

		PT2.input.year.val(parseInt(RegExp.$2, 10));
		PT2.input.month.val(parseInt(RegExp.$3, 10));
		PT2.input.day.val(parseInt(RegExp.$4, 10));

	}

	PT2.fView.show('slow');

	return(PT2);

};

// ログの追加と切り落とし
PT2.C.addLog = function(logs) {

	PT2.dTmp.html(logs);

	// 参加者の更新
	$('li.member', PT2.uMember).remove();
	PT2.dTmp.children('li.member').appendTo(PT2.uMember);
	// 参加状態を更新
	PT2.joinText = $('li.self', PT2.uMember).length ? PT2.text.join : '';
	PT2.h1.text(PT2.text.title + PT2.joinText);
	// 時系列修正
	var tmpFirst = PT2.dTmp.children().eq(0).attr('id');
	tmpFirst = parseInt((tmpFirst) ? tmpFirst.substr(1) : '');
	var chatLast = $('p.chat:last', PT2.dChat).attr('id');
	chatLast = parseInt((chatLast) ? chatLast.substr(1) : '');
	if (!isNaN(tmpFirst) && !isNaN(chatLast) && tmpFirst < chatLast)
	 $('p.chat', PT2.dChat).remove();
	// 追加
	PT2.dTmp.children().hide().appendTo(PT2.dChat).fadeIn('normal');
	// 切り落とし
	var over = $('p.chat', PT2.dChat).length - PT2.input.l.val();
	if (over > 0) $('p.chat:lt(' + over + ')', PT2.dChat).remove();

	PT2.C.addAccKey().C.scrollToLatest().F.enableButton();

};

// #!/ 付いてたら ?f= 相当の動作をする
PT2.X.applyState = function() {

	if (!location.hash.match(/!\/(log|cid)-(\w+)/)) return(PT2);

	var type = RegExp.$1;
	var target = RegExp.$2;

	switch (type) {

	case 'log' :
		PT2.X.loadPastLog(target);
		break;

	case 'cid' :
		PT2.X.loadCidLog(target);
		break;

	}

	return(PT2);

};

// ?f= を #!/ にして Ajax で遷移したよ感を醸し出す
PT2.X.changeState = function() {

	if (location.href.match(/[\?&]f=((log|cid)-\w+)/)) PT2.X.setState(RegExp.$1);
	// PT2.C.movePastLog でやったら XSLT の準備できてなかった
	if ($('p.note', PT2.dChat).is('[title="log_announce"]')) PT2.X.superReload();

	return(PT2);

};

// 今どんな状態か URL で表現する
// pjax もうしばらくおあずけ
PT2.X.setState = function(stat) {

	// 履歴の活用ができそうなら pushState も考える
	if (typeof(history.replaceState) == 'function')
	 history.replaceState(null, null, PT2.URL + '#!/' + stat);
	// IE はほっとく
	// else location.hash = stat;

	return(PT2);

};

// 設定ファイルの読み込み
PT2.X.loadConf = function() {

	$.ajax({
		url: PT2.confFile,
		dataType: 'xml',
		cache: false,
		success: PT2.X.setConf,
		error: function(req, stat, err){

			PT2.A.alert('設定ファイルの読み込みに失敗しました ('
			 + stat + ')。ページを開き直して下さい。');

		}
	});

	return(PT2);

};

// 設定内容を変数に保存
PT2.X.setConf = function(data) {

	var conf = $(data);
	conf.find('text>*').each(function() {

		var _this = $(this);
		var tag = _this.get(0).tagName;

		if (_this.is('[type]')) {

			if (typeof(PT2.text[tag]) != 'object') PT2.text[tag] = {};
			PT2.text[tag][_this.attr('type')] = _this.text();

		} else {

			PT2.text[tag] = _this.text();

		}

	});

	PT2.xslFile = conf.find('path>dir>templates').text()
	 + conf.find('path>xsl>ajax').text();

	PT2.copySwfFile = conf.find('path>dir>resources').text()
	 + conf.find('path>swf>copy').text();

	PT2.pasteSwfFile = conf.find('path>dir>resources').text()
	 + conf.find('path>swf>paste').text();

	PT2.logDir = conf.find('path>dir>logs').text();

	PT2.reloadCountOption = conf.find('option>reloadcount').text();

	// 設定値を元にいじる
	PT2.S.init();
	// XSL も今のうちつっこんどく
	PT2.X.loadXSL();

};

// テンプレートファイルの読み込み
PT2.X.loadXSL = function() {

	if (PT2.input.a && PT2.input.a.val() < 2) {

		PT2.F.enableButton();
		return;

	}

	var xhr = {
		url: PT2.xslFile,
		dataType: 'xml',
		xhrFields: {},
		cache: false,
		success: PT2.X.setXSL,
		error: PT2.A.xslError
	};

	if (PT2.responseType) xhr.xhrFields.responseType = PT2.responseType;

	$.ajax(xhr);

};

// テンプレートを変数に保存
PT2.X.setXSL = function(data) {

	PT2.logXSL = data;
	PT2.X.applyState().X.changeState().F.enableButton();

}

// 発言
PT2.X.post = function() {

	// 削除した時だけログリセット
	var reset = /^_del_/.test(PT2.input.m.val());
	PT2.X.loadLog('POST', reset).F.clearSaying();
	PT2.F.setReloadTimer();

	// form のイベント用だから
	return(false);

};

// リロード
PT2.X.reload = function() {

	PT2.X.loadLog('GET', false);
	PT2.F.setReloadTimer();

};

// スーパーリロード
PT2.X.superReload = function() {

	PT2.X.loadLog('GET', true);
	PT2.F.setReloadTimer();

};

// 自動リロード
PT2.X.autoReload = function() {

	if (!PT2.input.reload.is(':disabled')) PT2.X.reload();

};

// ログ送受信
PT2.X.loadLog = function(method, reset) {

	PT2.F.disableButton().F.setLastId(reset);
	var param = PT2.fSay.serialize();

	// リロードの場合発言は送らない
	if (method == 'GET') param = param.replace(/(^|&)m=[^&]*(&|$)/, '$1m=$2');
	var xslt = (PT2.input.a.val() == '2');
	var callback = xslt ? PT2.X.xslt : PT2.X.receiveHTML;
	var contentType = xslt ? 'xml' : 'html';
	var xhr = {
		url: PT2.URL,
		type: method,
		cache: false,
		data: param,
		dataType: contentType,
		xhrFields: {},
		success: callback,
		error: PT2.A.phpError
	};

	if (PT2.responseType) xhr.xhrFields.responseType = PT2.responseType;

	$.ajax(xhr);

	return(PT2);

};

// 過去ログ取得
PT2.X.loadPastLog = function(logName) {

	var today = new Date();
	var year = '' + today.getFullYear();
	var mon = '0' + (today.getMonth() + 1);
	var day = '0' + today.getDate();
	mon = mon.substr(mon.length - 2);
	day = day.substr(day.length - 2);
	today = '' + year + mon + day;

	// 引数優先
	if (typeof(logName) == 'string') {

		// 指定変だったら知らんぷりして帰る
		if (!logName.match(/^((\d{4})(\d{2})(\d{2})|today)$/)) return(false);

		if (RegExp.$1 == 'today') {

			logName = today;

		} else {

			year = RegExp.$2;
			mon = RegExp.$3;
			day = RegExp.$4;

		}

		PT2.input.year.val(parseInt(year, 10));
		PT2.input.month.val(parseInt(mon, 10));
		PT2.input.day.val(parseInt(day, 10));


	} else {

		year = '' + ((parseInt(PT2.input.year.val(), 10) % 2000) + 2000);
		mon = '0' + parseInt(PT2.input.month.val(), 10);
		day = '0' + parseInt(PT2.input.day.val(), 10);
		logName = year + mon.substr(mon.length - 2) + day.substr(day.length - 2);

		PT2.X.setState('log-' + logName);

	}

	if (!logName.match(/^\d{8}$/)) {

		PT2.A.alert(PT2.text.error.invalid_xml_filename).F.enableButton();

	} else {

		var xslt = (PT2.input.a.val() == '2');
		// 日付が今日ならリアルタイム生成のログを読みに行く
		var url = (logName == today) ? PT2.URL + '?a=' + PT2.input.a.val() + '&f=log-today'
		 : (xslt ? PT2.logDir + logName + '.xml' : '?a=1&f=log-' + logName);
		var callback = xslt ? function (data) { PT2.X.xslt(data, 'past'); } : function (data) { PT2.X.receiveHTML(data, 'past'); };
		var contentType = xslt ? 'xml' : 'html';
		var xhr = {
			url: url,
			dataType: contentType,
			xhrFields: {},
			cache: (logName == today) ? false : true,
			success: callback,
			error: PT2.A.xmlError
		};

		if (PT2.responseType) xhr.xhrFields.responseType = PT2.responseType;

		$.ajax(xhr);

	}

	// form のイベント用だから
	return(false);

};

// cid 指定ログ取得
// 今のとこ隠し機能扱いでフォームとか用意してない
PT2.X.loadCidLog = function(cid) {

	if (!cid.match(/^[0-9a-f]{6}$/)) return;

	var url = PT2.URL + '?a=2&f=cid-' + cid;
	var xhr = {
			url: url,
			dataType: 'xml',
			xhrFields: { responseType: PT2.responseType },
			cache: false,
			success: function (data) { PT2.X.xslt(data, 'past'); },
			error: PT2.A.phpError
	};

	if (PT2.responseType) xhr.xhrFields.responseType = PT2.responseType;

	$.ajax(xhr);

};

// 整形済みデータを受け取る
PT2.X.receiveHTML = function(data, type) {

	data = $(data);
	if (data.get(0) && data.get(0).tagName == 'html') data = data.children();

	if (type == 'past') PT2.C.showPastLog(data);
	 else PT2.C.addLog(data);

}

// XSLT
PT2.X.xslt = function(data, type) {

	if (!PT2.logXSL) {

		PT2.A.xslError();
		return;

	}

	if (PT2.toXSLDoc) data = PT2.X.addConf(PT2.X.copyNode(data), PT2.logXSL);
	 else data = PT2.X.addConf(data);

	var proc = new PT2.XSLTProcessor();
	proc.importStylesheet(PT2.logXSL);
	// Fx 2.0 だとここら辺で style 要素とかが欠落する
	var logs = proc.transformToFragment(data, document);

	// chrome でうまく XSLT できないときがあった
	if (!logs) {

		PT2.A.phpError();
		return;

	}

	// IE は返り値が文字列になる
	if (typeof(logs) == 'string') {

		// xmlns とかがついてると html() が使えない
		// 文字列のまま渡すことにした
		logs = logs.replace(/<html[^>]+>(.*)<\/html>/m, "$1");

	} else {

		if (logs.lastChild.tagName == 'html') logs = $(logs.lastChild).children();
		 else logs = $(logs).children();

	}

	if (type == 'past') PT2.C.showPastLog(logs);
	 else PT2.C.addLog(logs);

};

// ownerDocument を XSL に合わせて作り直す
PT2.X.copyNode = function(xml) {

	doc = PT2.logXSL;

	var data = $('<logs />', doc);

	// ネスト三段だし再帰かけずに強行
	$(xml).find('logs').children().each(function() {

		var node = this.tagName;
		var log = $('<' + node + ' />', doc);
		if (node == 'chat' || node == 'member') $(this).children().each(function() {

			var node = this.tagName;
			var param = $('<' + node + ' />', doc);
			if (node == 'body') $(this).children().each(function() {

				var node = this.tagName;
				var texts = $('<' + node + ' />', doc).text($(this).text());
				if (node == 'name') texts.attr('color', $(this).attr('color'));

				param.append(texts);

			});
			 else param.text($(this).text());

			log.append(param);

		});
		 else log.text($(this).text());

		data.append(log);

	});

	return(data);

}

// 設定項目をログの XML に追加
PT2.X.addConf = function(xml, doc) {

	if (!doc) {

		doc = xml;
		xml = $(xml).find('logs');

	}

	// 異なる DOM 間だと一個一個コピーしなきゃだめみたい
	var dText = $('<text />', doc);

	for (var tag in PT2.text) {

		if (typeof(PT2.text[tag]) == 'string') 
		 dText.append($('<' + tag + ' />', doc).text(PT2.text[tag]));
		 else for (var type in PT2.text[tag])
		 dText.append($('<' + tag + ' />', doc).attr('type', type).text(PT2.text[tag][type]));

	}

	xml.append(dText);
	xml = xml.get(0);

	return(xml);

};

// コピー用 swf を呼び出す
// PT2.S.clipboardData から移動した
PT2.P.addCopySwf = function() {

	if (PT2.copySwf) {

		// バージョンアップを促すため一時的に入れてるだけなのでべた書き
		// レビュー通ってから出す
		// PT2.A.addNote('AutoPaster のバージョンが古いようです。オートペーストがうまく動作しない場合は最新版にしてみて下さい。');
		return;
	}

	PT2.body.tag('object').attr({ id: 'copy', type: 'application/x-shockwave-flash', data: PT2.copySwfFile })
	  .tag('param').attr({ name: 'wmode', value: 'transparent' }).gat()
	 .gat();

	PT2.copySwf = $('#copy');
	PT2.copySwf.width(1);

};

// swf の準備ができた時に呼ばれる
PT2.P.registerCopyFunc = function() {

	// swf にテキストを送る
	PT2.P.setCopyText = function(txt, id) {

		id = '#' + id;
		PT2.copySwf.get(0).setCopyText(txt, id);

	};

};

// swf にテキストを送る (準備段階)
PT2.P.setCopyText = function(txt, id) {

	if (PT2.copySwf && PT2.copySwf.width() > 1) PT2.P.moveCopySwf(-100);
	PT2.A.alert(PT2.text.error.copy_not_ready);

};

// swf の移動
PT2.P.moveCopySwf = function(x, y, w, h) {

	if (!x) x = '0';
	if (!y) y = '0';
	if (!w) w = 1;
	if (!h) h = 1;
	PT2.copySwf.css({ top: y + "px", left: x + "px" }).width(w).height(h);

	return(PT2);

};

// コピーが済んだ時の動作
PT2.P.addCopiedMark = function(target) {

	if (target && target != '#') $(target).addClass('copied');

	return(PT2);

};

// クリップボードへコピー
PT2.P.copy = function(data) {

	// どうも情報の同期が取れてない
	//PT2.clipboardData.setData('Text', data);
	window.clipboardData.setData('Text', data);

	return(PT2);

};

// クリップボードからペースト
PT2.P.paste = function() {

	if (PT2.input.m.val() != '' || !PT2.input.autopaste.is(':checked')) return(PT2);

	if (typeof(PT2.clipboardData.getData) != 'boolean') PT2.P.pasteIE();
	 else if (PT2.exSelf) PT2.exSelf.postMessage({ type: 'getClip' });

	return(PT2);

};

// ペースト (IE)
PT2.P.pasteIE = function() {

	//var data = '' + PT2.clipboardData.getData('Text');
	var data = '' + window.clipboardData.getData('Text');

	if (data != '' && !data.match(/[\r\n]/) && data.match(PT2.BNRegExp)) {

		PT2.input.m.val(data);
		PT2.input.backup.val(data);
		//PT2.clipboardData.setData('Text', '');
		window.clipboardData.setData('Text', '');

	}

};

// ペースト (Firefox 拡張)
PT2.P.pasteFx = function(data) {

	if (data != '' && !data.match(/[\r\n]/) && data.match(PT2.BNRegExp)) {

		PT2.input.m.val(data);
		PT2.input.backup.val(data);

		PT2.exSelf.postMessage({ type: 'clearClip' });

	}

};

// ペースト (Chrome 拡張)
PT2.P.pasteCr = function() {

// 拡張のとこからここにアクセスできなかったから使ってない

};

// アラートの雛形
PT2.A.alertTemplate = function(txt, className) {

	var alertElm = $('<p />').addClass(className)
	 .tag('strong').text(PT2.text.announce + txt).gat()
	 .tag('button type="button"').addClass('close').text(PT2.text.close).gat();

	return(alertElm);

}

// 普通のメッセージを出す
PT2.A.addNote = function(txt) {

	PT2.dChat.append(PT2.A.alertTemplate(txt, 'note'));

	return(PT2);

};

// エラーメッセージを出す
PT2.A.addAlert = function(txt) {

	PT2.dChat.append(PT2.A.alertTemplate(txt, 'alert'));

	return(PT2);

};

// 可能ならチャット画面にアラートを出す
PT2.A.alert = function(message) {

	if (PT2.dChat) {

		PT2.A.addAlert(message).C.scrollToLatest();

	} else {

		alert(message);

	}

	return(PT2);

};

// PHP ロードエラー
PT2.A.phpError = function(req, stat, err) {

	PT2.A.alert(PT2.text.error.failed_php_load).F.enableButton();

};

// XML ロードエラー
PT2.A.xmlError = function(req, stat, err) {

	PT2.A.alert(PT2.text.error.failed_xml_load).F.enableButton();

};

// XSL ロードエラー
PT2.A.xslError = function(req, stat, err) {

	PT2.A.alert(PT2.text.error.failed_xsl_load);

};

$(PT2.X.loadConf);

} // if (typeof(window.PT2) == 'undefined')
})();
