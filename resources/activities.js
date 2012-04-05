/*

ぺたちゃ2 の入室状況いっぺんに複数表示スクリプト (activities.js)
書いた人 : kaska
みんなで使ってね。なかよく使ってね。

■使い方

チャットルームを
http://exsample.jp/petacha2_1/
http://exsample.jp/petacha2_2/
http://exsample.jp/petacha2_3/
という URL で 3 つ設置したという仮定です。

1.
このスクリプトの chatCount の値を変更します。
var chatCount = 3; // 部屋数
　この部分です。↑

2.
このスクリプトの chatURL の値を変更します。
var chatURL = 'http://exsample.jp/petacha2_{num}/'; // チャットの URL
　この部分です。↑
変化する数字の部分は「{num}」と記述して下さい。(1 から始まります。)

3.
スクリプトをアップロードして、HTML ファイルに以下のように記述します。

<script type="text/javascript" src="./jquery-1.6.1.min.js" charset="utf-8"></script>
<script type="text/javascript" src="./activities.js" charset="utf-8"></script>
<div id="peta2activity1">部屋 1 の状況 (ロード中…)</div>
<div id="peta2activity2">部屋 2 の状況 (ロード中…)</div>
<div id="peta2activity3">部屋 3 の状況 (ロード中…)</div>

スクリプトのパスは適宜変更して下さい。
activities.js は同じドメイン内ならどこに置いても平気だと思います。
jQuery は既に読み込んでたら記述しなくても平気です。
HTML の文字コードは UTF-8 が無難だと思います。

4.
アップロードした HTML ファイルにアクセスすると、各部屋の状況が出てくると思います。
出てきたらあとは適当にレイアウトなんかを調整して下さい。
出てこなかったらその時はその時です。

*/

$(function() {

var chatCount = 3; // 部屋数
var chatURL = 'http://exsample.jp/petacha2_{num}/'; // チャットの URL
var containerId = 'peta2activity{num}'; // 表示する箇所の ID
var successHTML = '<p>最終更新日時: {date}　参加者: {member}</p>'; // 表示する HTML
var errorHTML = '<p>データの取得に失敗しました。</p>'; // 失敗時の HTML


var chat = [];
for (var i = 1; i <= chatCount; i++)
 chat.push({ URL: chatURL.replace(/\{num\}/, i), id: containerId.replace(/\{num\}/, i) });

$(chat).each(function() {
	var c = this;
	$.ajax({
		url: c.URL + '?a=2&l=10', // a=2 で XML、l=10 でログ 10行 (デフォルトの最小値)
		type: 'GET',
		cache: false,
		dataType: 'xml',
		success: function(xml) {

			xml = $(xml);
			var member = '';
			var date = xml.find('chat:last').children('date').text(); // 日付
			xml.find('member').each(function() { // 参加者の数だけ回す

				var _this = $(this);
				var name = _this.children('name').text(); // 名前
				var color = _this.children('color').text(); // 色
				member += '<span style="color:' + color + ';">' + name + '</span> ';

			});

			if (member.length == 0) member = 'いません';
			var resultHTML = successHTML.replace(/\{date\}/, date).replace(/\{member\}/, member);
			$('#' + c.id).html(resultHTML);

		},
		error: function() {

			$('#' + c.id).html(errorHTML);

		}
	});
});

});
