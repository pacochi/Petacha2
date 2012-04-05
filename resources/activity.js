/*

ぺたちゃ2 の入室状況表示スクリプト (peta2.js)
書いた人 : kaska
みんなで使ってね。なかよく使ってね。

*/

$(function() {

var chatURL = 'http://chat.am.cute.bz/'; // チャットの URL
var containerId = 'peta2activity'; // 表示する箇所の ID
var successHTML = '<p>最終更新日時: {date}　参加者: {member}</p>'; // 表示する HTML
var errorHTML = '<p>データの取得に失敗しました。</p>'; // 失敗時の HTML

$.ajax({
	url: chatURL + '?a=2&l=10', // a=2 で XML、l=10 でログ 10行 (デフォルトの最小値)
	type: 'GET',
	cache: false,
	dataType: 'xml',
	success: function(xml) {

		xml = $(xml);
		var member = '';
		var date = xml.find('chat:last').children('date').text(); // 日付
		xml.find('member').each(function() { // 参加者の数だけ回す

			_this = $(this);
			var name = _this.children('name').text(); // 名前
			var color = _this.children('color').text(); // 色
			member += '<span style="color:' + color + ';">' + name + '</span> ';

		});

		if (member.length == 0) member = 'いません';
		successHTML = successHTML.replace(/\{date\}/, date).replace(/\{member\}/, member);
		$('#' + containerId).html(successHTML);

	},
	error: function() {

		$('#' + containerId).html(errorHTML);

	}
});

});