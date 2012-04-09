ぺたちゃ2 ver. 120409_0 (beta)

■概要

ぺたちゃ2 は GGD や SISTER を遊びやすくするためのチャットです。


■動作環境

PHP5 が使えるサーバで動作します。
.htaccess が使えると若干心強いです。


■構成

petacha2 (実際はバージョン名などが付きます)
├lib
│├index.php
│├PtCommand.php
│├PtConf.php
│├PtLog.php
│├PtPage.php
│├PtSQL.php
│├PtText.php
│├PtUser.php
│├PtUtil.php
│└JP106Key.php
├templates
│├base.xsl
│├log.xsl
│└rss.xsl
├resources
│├jquery-1.6.1.min.js
│├jquery.tag.js
│├peta2.js
│├activity.js
│├activities.js
│├peta2.css
│├peta2_color.css
│└Peta2Copy.swf
├tools
│├confeditor.htm
│├coloreditor.htm
│├activity_test.htm
│└tool.js
├logs
│├error_log.txt (設置後に作成されます)
│├pt2.sqlite (設置後に作成されます)
│├pt2host.sqlite (設置後に作成されます)
│├*.xml (設置後に作成されます)
│├access_*.log (設置後に作成されます)
│└htaccess.txt
├sessions
│├.gitkeep (削除しても構いません)
│└(設置後にセッションファイルが作成されます)
├index.php
├db_setup.php (設置後はサーバから削除して下さい)
├conf.xml
├htaccess.txt
└readme.txt


■サーバへの設置と設定

□前準備
ファイルの文字コードはおおむね UTF-8 なので (このテキストだけ Shift_JIS)、
設定を確認・編集する際には UTF-8 でも読み書きできるエディタをご用意下さい。

□設置の手順
1.
PHP5 の使えるサーバに全てのファイルをアップロードして下さい。
petacha2 のフォルダごとアップロードすると楽です。
2.
logs ディレクトリと sessions ディレクトリのパーミッションを、
PHP がそのディレクトリにファイルを作成できるようにいじって下さい。
(707 とか 777 とか、設置サーバによって違います。)
3.
db_setup.php にアクセスして下さい。
データベースがうまく作られたら、db_setup.php はサーバ上から削除して下さい。
4.
index.php もしくは / で終わる URL にアクセスしてみて、
特にエラーが出なかったら設置完了です。

エラーログとアクセスログは logs ディレクトリに作成されます。
ちょくちょく確認してみて下さい。

途中でつまずいた場合は、ご連絡下さい。
スローペースながら問題解決のお手伝いをさせていただきます。

□ログフォルダのアクセス制限
.htaccess が使えるサーバでしたら、logs ディレクトリに入っている htaccess.txt を
.htaccess にリネームすると、外部からアクセスログを覗かれないようになります。
過去ログは閲覧できます。

□チャットの設定
conf.xml をテキストエディタや XML エディタで開き、設定を確認・変更して下さい。
各設定値の説明はコメントとして書かれています。
tools/confeditor.htm を使うと楽かもしれません。
(ローカルでは多分動かないので、サーバにアップロードして使って下さい。)

□色の設定
resources/peta2_color.css をテキストエディタや CSS エディタで開き、変更して下さい。
tools/coloreditor.htm を使うと楽かもしれません。
(ローカルでは多分動かないので、サーバにアップロードして使って下さい。)
発言色セットは conf.xml から変更して下さい。

□その他の設定
分かる範囲で各自いじって下さい。
設定項目以外のカスタマイズに関しても特に制限はありません。

□チャットの使い方
以下をご覧下さい。
http://chat.am.cute.bz/help/

□別ページでの入室状況表示
1.
resources/activity.js をテキストエディタ等で開き、設定を変更して下さい。
2.
ページの表示させたい箇所にさっき設定した ID と同じ ID を割り振って、
resources/jquery-1.6.1.min.js と resources/activity.js を読み込むようにして下さい。
(jQuery は他のバージョンでもおおむね大丈夫だと思います。)
サンプルとして tools/activity_test.htm を用意しました。参考にして下さい。
3.
細かい調整をしたい場合、各自でスクリプトを改変したり、改変してもらったりして下さい。
尚、Ajax で XML を読み込んでいる関係から、別ドメインのページでは利用できません。

複数のチャットの入室状況をひとつのページで表示する場合、
resources/activity.js の containerId をかぶらないよう変更してチャットの数だけ読み込むか、
resources/activities.js を利用して下さい。 (使い方はスクリプトのコメントにあります。)

□IE でだけチャットがうまく動かない報告があった場合
XML ファイルと XSL ファイルの MIME type が、サーバ側で設定されていないのかもしれません。
その場合、petacha2 ディレクトリの htaccess.txt を .htaccess にリネームしてみて下さい。
(ファイル名の先頭に "." を付けられない場合、アップロードしてから変更して下さい。)

□Twitter への通知
http://チャットの設置場所/?f=cid-000000&rss
上記の URL で、システム発言だけを含んだ RSS が出力されますので、
RSS を読んで POST するような twitter の bot さんに読んでもらう事で通知が可能になります。
認証が必要なものは取り入れないポリシーなので、直接通知を POST する機能は付けません。
どうしても直接 POST したい場合は各自で改造して下さい。

■ライセンス

同梱されている jQuery (resources/jquery-1.6.1.min.js) は、
MIT ライセンスと GPL のデュアル・ライセンスです。
http://jquery.org/license/
著作権表示などは resources/jquery-1.6.1.min.js の冒頭に書かれています。

同梱されている jQuery Tag プラグイン (resources/jquery.tag.js) も、
jQuery と同じ MIT ライセンスと GPL のデュアル・ライセンスです。
http://developmentor.lrlab.to/postal/jquery/jquery.tag.html
著作権表示などは resources/jquery.tag.js の冒頭に書かれています。

その他は kaska が書いたもので、びしっとしたライセンス条項はありません。
みんなで使ってね。なかよく使ってね。
(ライセンス名が必要な場合、MTNT ライセンスと記しといて下さい。)


■連絡先

kaskat+am@gmail.com
http://twitter.com/petacha2


■バージョン履歴

□ver. 120409_0 (beta)
PHP5.4 でも動くように修正
スクリプトが無効でも過去ログを読める機能を追加
ログから特定の ID を抽出する機能を追加
過去ログ表示部分をドラッグで移動できるように変更

□ver. 111214_0 (beta)
IE8 でオートペーストが無効になる不具合を修正

□ver. 111212_0 (beta)
拡張機能に合わせて peta2.js をちょっと修正
ver. 111203_0 で発言の下に引いてた線を撤去

□ver. 111203_0 (beta)
htaccess.txt で文字コードの書き方をミスっていたのを修正
行数変更時に前のログが残っていたのを修正
スマートホン (というか 005SH) での可読性がさらに向上するよう修正
ウィンドウリサイズ時にチャットの枠もリサイズされるよう修正
過去ログ閲覧で今日のログも閲覧できるように変更
チャットの文字を詰め気味表示に変更

□ver. 111202_0 (beta)
XML の MIME type 修正用 htaccess.txt を追加

□ver. 111119_0 (beta)
activity.js をちょっと修正
複数の入室状況表示する activities.js を追加

□ver. 111117_0 (beta)
conf.xml と peta2_color.css の簡易エディタを追加
入室状況を別ページで表示する activity.js を追加

□ver. 111103_0 (beta)
逆順表示時に閲覧ツールでちゃんと閲覧できるように調整

□ver. 111029_0 (beta)
過去ログを逆順で表示する機能を追加

□ver. 110614_1 (beta)
IE9 で RSS がフィード扱いされていなかったのを修正

□ver. 110614_0 (beta)
IE9 で出るエラーをいくつか修正
IE7 でも使えるようになったかも
リロードカウンターの表示をオプション化
RSS の Content-Type を application/xml に変更

□ver. 110518_0 (beta)
パーミッション変更時のエラーが出にくいように修正

□ver. 110507_0 (beta)
書きかけのヘルプおよび Readme に加筆
リロードカウンターをリロードボタンに追加
発言削除時にログを読み直すよう変更
行数変更時にログを読み直すよう変更
リモートホストのキャッシュのロックが半端だったのを修正
携帯およびスマートホンでの可読性が若干向上するよう修正
XSS の対策漏れがあったので修正

□ver. 110216_0 (alpha)
取り急ぎリリース
実際の公開日は 2011.03.29
