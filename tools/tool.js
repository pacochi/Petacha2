/*

ぺたちゃ2 のツール用スクリプト (tool.js)
書いた人 : kaska
みんなで使ってね。なかよく使ってね。

*/

(function() { // タブ省略
if (typeof(window.PTT) == 'undefined') { // タブ省略

window.PTT = {};

// 最初にすること
PTT.init = function() {

	PTT[$('body').attr('id')].init();

};

// ■peta2_color.css 簡易エディタ
PTT.color = { // タブ省略

// 最初にすること
init: function() {

	PTT.color.CSS.load();

},

// 画面
view: {

	editArea: null,
	cssArea: null,
	previewArea: null,

	// 表示
	apply: function() {

		PTT.color.view.editArea = $('#editor').empty().append(PTT.color.item.colorToInput().css({ width: '40%', float: 'left' }));
		PTT.color.view.cssArea = PTT.text.area();
		PTT.color.view.previewArea = $('#peta2');
		PTT.color.view.editArea.append(
		 $('<fieldset />').css({ width: '50%', float: 'right' }).append($('<legend />').text('プレビュー')).append(PTT.color.view.previewArea.show())
		).append(
		 $('<fieldset />').css('clear', 'both').append($('<legend />').text('CSS')).append(PTT.color.view.cssArea)
		);
		var writeOffset = $('#write').offset();
		$('#view').css({ width: '20em', top: (writeOffset.top - 120) + 'px', left: (writeOffset.left - 20) + 'px' }).show();

		PTT.color.view.rewriteCSS();

	},

	// CSS の更新
	rewriteCSS: function() {

		PTT.color.view.cssArea.children().empty().append(PTT.color.CSS.output());

	}


}, // view

// CSS
CSS: {

	rules: [],

	load: function() {

		var stylesheet = document.styleSheets[0];
		var allRules = (stylesheet.cssRules) ? stylesheet.cssRules : stylesheet.rules;
		var rules = [];

		$(allRules).each(function() {

			if (this.type == 4 && (this.media == 'all' || this.media[0] == 'all'))
			 rules = this.cssRules;

		});

		if (!rules.length) {

			alert('CSS を読み込めませんでした。');
			return;

		}

		$(rules).each(function() {

			PTT.color.CSS.rules.push(PTT.color.item.create(this));

		});

		PTT.color.view.apply();

	},

	// 生成
	output: function() {

		var css = $('<span />').append(PTT.text.color('@media all {\n\n', 2));

		$(PTT.color.CSS.rules).each(function() {

			css.append(this.toCSS());

		});

		css.append(PTT.text.color('}\n', 2));

		return(css);

	}

},// CSS

// ルールを扱いやすいよう変換
item: {

	colors: {
	 'body-text': { note: '全体と入力欄の文字', value: '#000', target: [] },
	 'body-back': { note: '全体と入力欄の背景', value: '#FEF', target: [] },
	 'chat-back': { note: 'チャット部分の背景', value: '#FDE', target: [] },
	 'title-text': { note: 'タイトルの文字', value: '#400', target: [] },
	 'date-text': { note: '日付の文字', value: '#868', target: [] },
	 'link-text': { note: '未読リンクの文字', value: '#008', target: [] },
	 'link-text-visited': { note: '既読リンクの文字', value: '#608', target: [] },
	 'link-back-hover': { note: 'リンクポイント時の背景', value: '#FEF', target: [] },
	 'alert-text': { note: 'アラートの文字', value: '#E00', target: [] },
	 'alert-back': { note: 'アラートの背景', value: '#FEE', target: [] },
	 'alert-border': { note: 'アラートの線', value: '#E00', target: [] },
	 'note-text': { note: 'お知らせの文字', value: '#807', target: [] },
	 'note-back': { note: 'お知らせの背景', value: '#FEF', target: [] },
	 'note-border': { note: 'お知らせの線', value: '#F8E', target: [] },
	 'button-text': { note: 'ボタンの文字', value: 'ButtonText', target: [] },
	 'button-text-disabled': { note: '無効ボタンの文字', value: 'ButtonShadow', target: [] },
	 'button-back': { note: 'ボタンの背景', value: 'ButtonFace', target: [] },
	 'button-back-hover': { note: 'ボタンポイント時の背景', value: 'ButtonHighlight', target: [] },
	 'button-border-top': { note: 'ボタンの上線', value: 'ButtonHighlight', target: [] },
	 'button-border-right': { note: 'ボタンの右線', value: 'ButtonShadow', target: [] },
	 'button-border-bottom': { note: 'ボタンの下線', value: 'ButtonShadow', target: [] },
	 'button-border-left': { note: 'ボタンの左線', value: 'ButtonHighlight', target: [] }
	 },
	editableStyle: {
	 'body': { 'color': 'body-text', 'background-color': 'body-back' },
	 'a:link': { 'color': 'link-text' },
	 'a:visited': { 'color': 'link-text-visited' },
	 'a:active, a:hover': { 'background-color': 'link-back-hover' },
	 'h1': { 'color': 'title-text' },
	 'div#body': { 'background-color': 'chat-back' },
	 'span.date': { 'color': 'date-text' },
	 'p.alert': { 'color': 'alert-text', 'background-color': 'alert-back', 'border-color': 'alert-border' },
	 'p.note': { 'color': 'note-text', 'background-color': 'note-back', 'border-color': 'note-border' },
	 'form#view': { 'background-color': 'note-border' },
	 'div#past': { 'background-color': 'chat-back' },
	 'p.logdate': { 'background-color': 'chat-back' },
	 'label.bn:hover': { 'background-color': 'link-back-hover' },
	 'label.colorpick': { 'color': 'link-text' },
	 'label.colorpick:hover': { 'background-color': 'link-back-hover' },
	 'input, textarea, select': { 'color': 'body-text', 'background-color': 'body-back', 'border-color': 'body-text' },
	 'button': { 'color': 'button-text', 'background-color': 'button-back',
	  'border-top-color': 'button-border-top', 'border-right-color': 'button-border-right',
	  'border-bottom-color': 'button-border-bottom', 'border-left-color': 'button-border-left' },
	 'button[disabled]': { 'color': 'button-text-disabled' },
	 'button:hover': { 'background-color': 'button-back-hover' },
	 'button[disabled]:hover': { 'background-color': 'button-back' },
	 'option': { 'background-color': 'body-back' }
	},

	// 作成
	create: function(rule) {

		var item = new PTT.color.item.styleRule(rule);

		return(item);

	},

	// スタイルの雛形
	styleRule: function(rule) {

		this.selector = rule.selectorText;
		this.rule = rule;
		this.editableStyle = PTT.color.item.editableStyle[this.selector];
		var item = this;
		var border = [];

		this.style = $(rule.style).map(function() {

			var property = this.replace(/-value/, '');
			var value = rule.style[$.camelCase(property)];

			if (typeof(value) == 'string')
			 return({ property: property, value: value, defaultValue: value });

		}).map(PTT.color.item.optimizeColor);

		this.style.each(function(i) {

			if (this.property.match(/^border-(top|right|bottom|left)/)
			 && (border.length == 0 || border[0][1] == this.value))
			 border.unshift([i, this.value]);

			if (item.editableStyle && item.editableStyle[this.property]) {

				var name = PTT.color.item.colors[item.editableStyle[this.property]];
				name.target.push({ style: item.style[i], rule: item.rule, property: this.property });
				if (name.value != this.value) name.value = this.value;

			}

		});

		if (border.length == 4) {

			$(border).each(function() {

				item.style.splice(this[0], 1);

			});

			var borderColor = { property: 'border-color', value: border[0][1], defaultValue: border[0][1] };
			this.style.push(borderColor);

			if (item.editableStyle && item.editableStyle[borderColor.property]) {

				var name = PTT.color.item.colors[this.editableStyle[borderColor.property]];
				name.target.push({ style: this.style[this.style.length - 1], rule: this.rule, property: borderColor.property });
				if (name.value != borderColor.value) name.value = borderColor.value;

			}

		}

		this.toCSS = PTT.color.item.styleRuleToCSS;

	},

	// rgb(0, 0, 0) から #000 に変換
	optimizeColor: function() {

		var property = this.property;
		var value = this.value;

		if (value.match(/rgb\((\d+), (\d+), (\d+)\)/)) {

			var rgb = [ RegExp.$1, RegExp.$2, RegExp.$3 ];
			var simple = true;
			rgb = $(rgb).map(function() {

				if (parseInt(this) % 17) simple = false;
				return(parseInt(this));

			});

			value = '#' + rgb.map(function() {

				if (simple) return((this / 17).toString(16));
				 else return(('0' + this.toString(16)).slice(-2));

			}).get().join('').toUpperCase();

		}

		return({ property: property, value: value, defaultValue: value });

	},

	// CSS の生成
	styleRuleToCSS: function() {

		var css = $('<span />');
		var diff;

		css.append(PTT.text.color(this.selector + '{\n', 2));

		$(this.style).each(function() {

			diff = (this.value == this.defaultValue) ? null : 4;
			css.append(PTT.text.color('\t' + this.property + ':', 3));
			css.append(PTT.text.color(this.value, 0, diff));
			css.append(PTT.text.color(';\n', 3));

		});

		css.append(PTT.text.color('\t}\n\n', 2));

		return(css);

	},

	// 入力欄の生成
	colorToInput: function() {

		var element = $('<fieldset />').css({ color: '#000', background: '#FFF' }).append($('<legend />').text('色の変更'));

		for (var name in PTT.color.item.colors) {

			element.append(
			 $('<label />').css({ fontWeight: 'bold', display: 'block' }).text(name).append(
			  $('<input />').attr('id', name).css({ width: '6em', margin: '0px 1em' }).val(PTT.color.item.colors[name].value)
			  .change(function() { PTT.color.item.modify(this); })
			 ).append(
			  $('<span />').css({ font: '80% normal' }).text('(' + PTT.color.item.colors[name].note + ')')
			)
			);

		}

		return(element);

	},

	// 色変更
	modify: function(elm) {

		var value = $(elm).val();
		$(PTT.color.item.colors[elm.id].target).each(function() {

			this.rule.style.setProperty(this.property, value, 'important');
			this.style.value = value;

		});

		PTT.color.view.rewriteCSS();

	}

} // item

}; // PTT.color

// ■conf.xml 簡易エディタ
PTT.conf = { // タブ省略

// 最初にすること
init: function() {

	PTT.conf.XML.load();

},

// 画面
view: {

	editArea: null,
	xmlArea: null,

	// 表示
	apply: function() {

		PTT.conf.view.editArea = $('#editor').empty().append(PTT.conf.XML.data.toInput());
		PTT.conf.view.xmlArea = $('<pre />').css({ background: 'white', border: '1px solid #888', width: '60em', height: '20em', overflow: 'scroll' });
		PTT.conf.view.editArea.append($('<fieldset />').append($('<legend />').text('XML')).append(PTT.conf.view.xmlArea));
		PTT.conf.view.rewriteXML();

		for (var tag in PTT.conf.item.pluralItems)
		 $('div.item-' + tag + ':last').after($('<button />').addClass(tag).text(tag + ' を追加').click(PTT.conf.item.add));

	},

	// XML の更新
	rewriteXML: function() {

		PTT.conf.view.xmlArea.empty().append(PTT.conf.XML.output().mousedown(PTT.conf.view.selectXML));

	},

	// XML を選択
	selectXML: function(e) {

		if (e.button != 0) return(true);

		var sel = window.getSelection();

		if (sel.isCollapsed) {

			var range = document.createRange();
			range.selectNodeContents(this);
			sel.addRange(range);

			return(false);

		} else {

			sel.removeAllRanges();

		}

	},

	// 入力項目の追加
	addInput: function(item) {

		$('div.item-' + item.tag + ':last').after(item.toInput());
		return(true);

	},

	// 入力項目の削除
	removeInput: function(item, elm) {

		if ($('div.item-' + item.tag).size() <= 1) {

			alert('最後のひとつは削除できません');
			return(false);

		}

		$(elm).parent().remove();
		return(true);

	}


}, // view

// XML
XML: {

	file: '../conf.xml',
	data: null,

	// 読み込み
	load: function() {

		$.ajax({
			url: PTT.conf.XML.file,
			dataType: 'xml',
			success: function(data) {

				$(data).find('conf').each(function() {

					PTT.conf.XML.data = PTT.conf.item.create(this);

				});

				PTT.conf.view.apply();

			},
			error: function(req, stat, err) {

				alert('設定ファイルの読み込みに失敗しました。');

			}
		});

	},

	

	// 生成
	output: function() {

		var xml = PTT.conf.XML.data.toXML();
		xml.eq(0).prepend(PTT.text.color('<?xml version="1.0" encoding="UTF-8"?>\n', 2));

		return(xml);

	}

},// XML

// ノードを扱いやすいよう変換
item: {

	pluralItems: {
	 'line': { parentTag: 'select', attr: [{ name: 'default', text: 'no' }] },
	 'reloadsec': { parentTag: 'select', attr: [{ name: 'default', text: 'no' }] },
	 'color': { parentTag: 'select', attr: [{ name: 'default', text: 'no' }] },
	 'link': { parentTag: 'text', attr: [{ name: 'url', text: 'http://' }] },
	 'cid': { parentTag: 'deny', attr: [] },
	 'host': { parentTag: 'deny', attr: [] },
	 'ua': { parentTag: 'deny', attr: [] },
	 'ref': { parentTag: 'deny', attr: [] }
	},

	// 作成
	create: function(node, parent) {

		var item = (node.nodeType == 8)
		 ? new PTT.conf.item.comment(node, parent)
		 : new PTT.conf.item.elmtext(node, parent);

		return(item);

	},

	// コメントの雛形
	comment: function(node, parent) {

		this.tag = 'comment';
		this.parent = parent;
		this.level = parent.level + 1;
		this.text = node.nodeValue;
		this.toXML = PTT.conf.item.commentToXML;
		this.toInput = PTT.conf.item.commentToInput;

	},

	// XML の生成 (コメント)
	commentToXML: function() {

		var xml = PTT.text.color('<!--' + this.text + '-->\n', 1);

		return(xml);

	},

	// 入力欄の生成 (コメント)
	commentToInput: function() {

		var element = $('<pre />').css('font-style', 'italic').text(this.text);

		return(element);

	},

	// 要素とテキストの雛形
	elmtext: function(node, parent) {

		this.tag = node.tagName;
		this.parent = parent;
		this.level = (parent) ? parent.level + 1 : -1;
		this.plural = (PTT.conf.item.pluralItems[this.tag]) ? PTT.conf.item.pluralItems[this.tag] : {};
		this.attr = [];

		var item = this;

		if (!this.plural.parentTag || this.plural.parentTag != this.parent.tag)
		 this.plural = null;
		 else if (!this.plural.parent) this.plural.parent = this.parent;

		if (node.attributes.length) {

			var attr = {};

			for (var i = 0; i < node.attributes.length; i++)
			 this.attr.push({
			  name: [node.attributes[i].name],
			  text: PTT.entity.conv(node.attributes[i].value),
			  defaultText: PTT.entity.conv(node.attributes[i].value)
			  });

		}

		if (node.childNodes.length) {

			var children = [];
			var text = '';

			for (var i = 0; i < node.childNodes.length; i++)
			 if (node.childNodes[i].nodeType == 3) { // TEXT_NODE

				text += PTT.entity.conv(node.childNodes[i].nodeValue);

			} else {

				children.push(PTT.conf.item.create(node.childNodes[i], this));

			}

			if (children.length) this.children = children;
			 else this.text = this.defaultText = text;


		}

		this.toXML = PTT.conf.item.elmtextToXML;
		this.toInput = PTT.conf.item.elmtextToInput;

	},

	// XML の生成
	elmtextToXML: function() {

		var xml = $('<span />');
		var diff;

		for (var i = 0; i < this.level; i++) xml.append('&nbsp;');
		xml.append(PTT.text.color('<' + this.tag, 2));

		$(this.attr).each(function() {

			diff = (this.text == this.defaultText) ? null : 4;
			xml.append(PTT.text.color(' ' + this.name + '="', 3));
			xml.append(PTT.text.color(this.text, 3, diff));
			xml.append(PTT.text.color('"', 3));

		});

		if (this.text) {

			diff = (this.text == this.defaultText) ? null : 4;
			xml.append(
			 PTT.text.color('>', 2)
			).append(
			 PTT.text.color(this.text, 0, diff)
			).append(
			 PTT.text.color('</' + this.tag + '>\n', 2)
			);

		} else if (this.children) {

			xml.append(PTT.text.color('>\n', 2));

			for (var i = 0; i < this.children.length; i++)
			 xml.append(this.children[i].toXML());

			xml.append(PTT.text.color('</' + this.tag + '>\n', 2));

		} else {

			//xml.append(PTT.text.color(' />\n', 2));
			xml.append(
			 PTT.text.color('>', 2)
			).append(
			 PTT.text.color('値が未入力です', 1, 4).css('font-weight', 'bold')
			).append(
			 PTT.text.color('</' + this.tag + '>\n', 2)
			);

		}

		return(xml);

	},

	// 入力欄の生成
	elmtextToInput: function() {

		var element = $('<div />');
		var item = this;

		if (typeof(this.text) == 'string') {

			element.append(
			 $('<label />').css('font-weight', 'bold').text(this.tag).append(
			  $('<input />').css({width: '10em', margin: '0px 1em' }).val(this.text)
			  .change(function() { PTT.conf.item.modify(item, this); })
			 )
			);

			$(this.attr).each(function () {

				var attr = this;
				element.append('(').append(
				 $('<label />').text('' + this.name).append(
				  $('<input />').css({width: '10em', margin: '0px 1em' }).val(this.text)
				  .change(function() { PTT.conf.item.modify(attr, this); })
				 )
				).append(')');

			});

			if (this.plural)
			 element.addClass('item-' + this.tag).append(
			  $('<button />').text('×').click(function() { PTT.conf.item.remove(item, this); })
			 );

		} else if (this.children) {

			var fieldset = $('<fieldset />').append($('<legend />').text(this.tag));
			element.addClass('category').append(fieldset);

			for (var i = 0; i < this.children.length; i++)
			 fieldset.append(this.children[i].toInput());

		} else {

			element.addClass('none');

		}

		return(element);

	},

	// 要素の追加
	add: function() {

		var tag = this.className;
		var pluralItem = PTT.conf.item.pluralItems[tag];
		var children = pluralItem.parent.children;
		var node = {
		 nodeType: 1,
		 tagName: tag,
		 childNodes: [{ nodeType: 3, nodeValue: ''}],
		 attributes: pluralItem.attr
		};
		var item = new PTT.conf.item.create(node, pluralItem.parent);

		for (var i = children.length - 1; i >= 0; i--)
		 if (children[i].tag == tag) {

			children.splice(i + 1, 0, item);
			break;

		}

		PTT.conf.view.addInput(item);
		PTT.conf.view.rewriteXML();

	},

	// 要素の削除
	remove: function(item, elm) {

		if (PTT.conf.view.removeInput(item, elm)) {

			// delete item; じゃ削除できなかった
			item.toXML = function() { return(''); };

			PTT.conf.view.rewriteXML();

		}

	},

	// テキスト変更
	modify: function(item, elm) {

		$(elm).val(item.text = PTT.entity.conv($(elm).val()));
		PTT.conf.view.rewriteXML();

	}

} // item

}; // PTT.conf

// ■テキストのスタイル付け
PTT.text = { // タブ省略

pallet: [ '#000', '#F00', '#00F', '#008', '#FF0', '#FFF', '#888' ],

color: function(text, col, bak) {

	if (typeof(col) == 'number') col = PTT.text.pallet[col];
	if (typeof(bak) == 'number') bak = PTT.text.pallet[bak];

	var element = $('<span />').css('color', col).text(text);
	if (bak) element.css('background-color', bak);

	return(element);

},

area: function(col, bod) {

	col = (typeof(col) == 'number') ? PTT.text.pallet[col] : PTT.text.pallet[5];
	bod = (typeof(bod) == 'number') ? PTT.text.pallet[bod] : PTT.text.pallet[6];

	var element = $('<pre />').css({ background: col, border: '1px solid ' + bod, width: '60em', height: '20em', overflow: 'scroll' }).append(
	 $('<span />').mousedown(PTT.text.selFunc)
	);

	return(element);

},

selFunc: function(e) {

	if (e.button != 0) return(true);

	var sel = window.getSelection();

	if (sel.isCollapsed) {

		var range = document.createRange();
		range.selectNodeContents(this);
		sel.addRange(range);

		return(false);

	} else {

		sel.removeAllRanges();

	}

}

}; // PTT.text

// ■文字参照の変換
// $('<p />').text(str).html() だと変換できないのがいる
PTT.entity = { // タブ省略

chr: { '<': '&lt;', '>': '&gt;', '&': '&amp;', '"': '&quot;', "'": '&apos;' },

conv: function(str) {

	return(str.replace(/([<>"']|&[^a-zA-z#]|&$)/g, PTT.entity.repFunc));

},

repFunc: function(a, str) {

	return(PTT.entity.chr[str.charAt(0)] + str.substr(1));

}

}; // PTT.entity

$(PTT.init);

} // if (typeof(window.PTT) == 'undefined')
})();
