<?php

/*

 ぺたちゃ2
 書いた人 : kaska
 表示テキストなどの設定は conf.xml で行って下さい。
 背景色などの設定は resources/peta2_color.css で行って下さい。
 スクリプト内の設定値は分かる方のみいじって下さい。

*/

# 内部エンコードとか
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
date_default_timezone_set('Asia/Tokyo');

# デバッグモード
define('PT2_DEBUG_MODE', true);
# 設定ファイル呼び出し前に必要なパス
$libdir = './lib/';
$conf = './conf.xml';

require_once($libdir . 'PtConf.php');
PtConf::load($conf);

require_once($libdir . 'PtUtil.php');
require_once($libdir . 'PtSQL.php');
PtUtil::silentError();
PtUtil::checkDBSetup();
PtConf::addEnv();
$libdir = PtConf::S('path/dir/library');

require_once($libdir . 'PtUser.php');
$user = new PtUser();

# アクセス制限受けてる場合はそのまま終了
if ($user->isDenied()) PtUtil::exit403($user->cid);

require_once($libdir . 'PtLog.php');
require_once($libdir . 'PtText.php');
$logs = new PtLog($user);

if (!$user->isRSSMode()) $user->closeSession();

# クライアント側で XSLT できる時は XML そのまま出力して終了
if ($user->isClientXSLTMode()) $logs->outputXML();

require_once($libdir . 'PtPage.php');
$page = new PtPage($logs);

# ページを出力して終了
$page->output();

?>