�؂�����2 ver. 120409_0 (beta)

���T�v

�؂�����2 �� GGD �� SISTER ��V�т₷�����邽�߂̃`���b�g�ł��B


�������

PHP5 ���g����T�[�o�œ��삵�܂��B
.htaccess ���g����Ǝ኱�S�����ł��B


���\��

petacha2 (���ۂ̓o�[�W�������Ȃǂ��t���܂�)
��lib
����index.php
����PtCommand.php
����PtConf.php
����PtLog.php
����PtPage.php
����PtSQL.php
����PtText.php
����PtUser.php
����PtUtil.php
����JP106Key.php
��templates
����base.xsl
����log.xsl
����rss.xsl
��resources
����jquery-1.6.1.min.js
����jquery.tag.js
����peta2.js
����activity.js
����activities.js
����peta2.css
����peta2_color.css
����Peta2Copy.swf
��tools
����confeditor.htm
����coloreditor.htm
����activity_test.htm
����tool.js
��logs
����error_log.txt (�ݒu��ɍ쐬����܂�)
����pt2.sqlite (�ݒu��ɍ쐬����܂�)
����pt2host.sqlite (�ݒu��ɍ쐬����܂�)
����*.xml (�ݒu��ɍ쐬����܂�)
����access_*.log (�ݒu��ɍ쐬����܂�)
����htaccess.txt
��sessions
����.gitkeep (�폜���Ă��\���܂���)
����(�ݒu��ɃZ�b�V�����t�@�C�����쐬����܂�)
��index.php
��db_setup.php (�ݒu��̓T�[�o����폜���ĉ�����)
��conf.xml
��htaccess.txt
��readme.txt


���T�[�o�ւ̐ݒu�Ɛݒ�

���O����
�t�@�C���̕����R�[�h�͂����ނ� UTF-8 �Ȃ̂� (���̃e�L�X�g���� Shift_JIS)�A
�ݒ���m�F�E�ҏW����ۂɂ� UTF-8 �ł��ǂݏ����ł���G�f�B�^�����p�Ӊ������B

���ݒu�̎菇
1.
PHP5 �̎g����T�[�o�ɑS�Ẵt�@�C�����A�b�v���[�h���ĉ������B
petacha2 �̃t�H���_���ƃA�b�v���[�h����Ɗy�ł��B
2.
logs �f�B���N�g���� sessions �f�B���N�g���̃p�[�~�b�V�������A
PHP �����̃f�B���N�g���Ƀt�@�C�����쐬�ł���悤�ɂ������ĉ������B
(707 �Ƃ� 777 �Ƃ��A�ݒu�T�[�o�ɂ���ĈႢ�܂��B)
3.
db_setup.php �ɃA�N�Z�X���ĉ������B
�f�[�^�x�[�X�����܂����ꂽ��Adb_setup.php �̓T�[�o�ォ��폜���ĉ������B
4.
index.php �������� / �ŏI��� URL �ɃA�N�Z�X���Ă݂āA
���ɃG���[���o�Ȃ�������ݒu�����ł��B

�G���[���O�ƃA�N�Z�X���O�� logs �f�B���N�g���ɍ쐬����܂��B
���傭���傭�m�F���Ă݂ĉ������B

�r���ł܂������ꍇ�́A���A���������B
�X���[�y�[�X�Ȃ���������̂���`���������Ă��������܂��B

�����O�t�H���_�̃A�N�Z�X����
.htaccess ���g����T�[�o�ł�����Alogs �f�B���N�g���ɓ����Ă��� htaccess.txt ��
.htaccess �Ƀ��l�[������ƁA�O������A�N�Z�X���O��`����Ȃ��悤�ɂȂ�܂��B
�ߋ����O�͉{���ł��܂��B

���`���b�g�̐ݒ�
conf.xml ���e�L�X�g�G�f�B�^�� XML �G�f�B�^�ŊJ���A�ݒ���m�F�E�ύX���ĉ������B
�e�ݒ�l�̐����̓R�����g�Ƃ��ď�����Ă��܂��B
tools/confeditor.htm ���g���Ɗy��������܂���B
(���[�J���ł͑��������Ȃ��̂ŁA�T�[�o�ɃA�b�v���[�h���Ďg���ĉ������B)

���F�̐ݒ�
resources/peta2_color.css ���e�L�X�g�G�f�B�^�� CSS �G�f�B�^�ŊJ���A�ύX���ĉ������B
tools/coloreditor.htm ���g���Ɗy��������܂���B
(���[�J���ł͑��������Ȃ��̂ŁA�T�[�o�ɃA�b�v���[�h���Ďg���ĉ������B)
�����F�Z�b�g�� conf.xml ����ύX���ĉ������B

�����̑��̐ݒ�
������͈͂Ŋe���������ĉ������B
�ݒ荀�ڈȊO�̃J�X�^�}�C�Y�Ɋւ��Ă����ɐ����͂���܂���B

���`���b�g�̎g����
�ȉ��������������B
http://chat.am.cute.bz/help/

���ʃy�[�W�ł̓����󋵕\��
1.
resources/activity.js ���e�L�X�g�G�f�B�^���ŊJ���A�ݒ��ύX���ĉ������B
2.
�y�[�W�̕\�����������ӏ��ɂ������ݒ肵�� ID �Ɠ��� ID ������U���āA
resources/jquery-1.6.1.min.js �� resources/activity.js ��ǂݍ��ނ悤�ɂ��ĉ������B
(jQuery �͑��̃o�[�W�����ł������ނˑ��v���Ǝv���܂��B)
�T���v���Ƃ��� tools/activity_test.htm ��p�ӂ��܂����B�Q�l�ɂ��ĉ������B
3.
�ׂ����������������ꍇ�A�e���ŃX�N���v�g�����ς�����A���ς��Ă�������肵�ĉ������B
���AAjax �� XML ��ǂݍ���ł���֌W����A�ʃh���C���̃y�[�W�ł͗��p�ł��܂���B

�����̃`���b�g�̓����󋵂��ЂƂ̃y�[�W�ŕ\������ꍇ�A
resources/activity.js �� containerId �����Ԃ�Ȃ��悤�ύX���ă`���b�g�̐������ǂݍ��ނ��A
resources/activities.js �𗘗p���ĉ������B (�g�����̓X�N���v�g�̃R�����g�ɂ���܂��B)

��IE �ł����`���b�g�����܂������Ȃ��񍐂��������ꍇ
XML �t�@�C���� XSL �t�@�C���� MIME type ���A�T�[�o���Őݒ肳��Ă��Ȃ��̂�������܂���B
���̏ꍇ�Apetacha2 �f�B���N�g���� htaccess.txt �� .htaccess �Ƀ��l�[�����Ă݂ĉ������B
(�t�@�C�����̐擪�� "." ��t�����Ȃ��ꍇ�A�A�b�v���[�h���Ă���ύX���ĉ������B)

��Twitter �ւ̒ʒm
http://�`���b�g�̐ݒu�ꏊ/?f=cid-000000&rss
��L�� URL �ŁA�V�X�e�������������܂� RSS ���o�͂���܂��̂ŁA
RSS ��ǂ�� POST ����悤�� twitter �� bot ����ɓǂ�ł��炤���Œʒm���\�ɂȂ�܂��B
�F�؂��K�v�Ȃ��͎̂�����Ȃ��|���V�[�Ȃ̂ŁA���ڒʒm�� POST ����@�\�͕t���܂���B
�ǂ����Ă����� POST �������ꍇ�͊e���ŉ������ĉ������B

�����C�Z���X

��������Ă��� jQuery (resources/jquery-1.6.1.min.js) �́A
MIT ���C�Z���X�� GPL �̃f���A���E���C�Z���X�ł��B
http://jquery.org/license/
���쌠�\���Ȃǂ� resources/jquery-1.6.1.min.js �̖`���ɏ�����Ă��܂��B

��������Ă��� jQuery Tag �v���O�C�� (resources/jquery.tag.js) ���A
jQuery �Ɠ��� MIT ���C�Z���X�� GPL �̃f���A���E���C�Z���X�ł��B
http://developmentor.lrlab.to/postal/jquery/jquery.tag.html
���쌠�\���Ȃǂ� resources/jquery.tag.js �̖`���ɏ�����Ă��܂��B

���̑��� kaska �����������̂ŁA�т����Ƃ������C�Z���X�����͂���܂���B
�݂�ȂŎg���ĂˁB�Ȃ��悭�g���ĂˁB
(���C�Z���X�����K�v�ȏꍇ�AMTNT ���C�Z���X�ƋL���Ƃ��ĉ������B)


���A����

kaskat+am@gmail.com
http://twitter.com/petacha2


���o�[�W��������

��ver. 120409_0 (beta)
PHP5.4 �ł������悤�ɏC��
�X�N���v�g�������ł��ߋ����O��ǂ߂�@�\��ǉ�
���O�������� ID �𒊏o����@�\��ǉ�
�ߋ����O�\���������h���b�O�ňړ��ł���悤�ɕύX

��ver. 111214_0 (beta)
IE8 �ŃI�[�g�y�[�X�g�������ɂȂ�s����C��

��ver. 111212_0 (beta)
�g���@�\�ɍ��킹�� peta2.js ��������ƏC��
ver. 111203_0 �Ŕ����̉��Ɉ����Ă�����P��

��ver. 111203_0 (beta)
htaccess.txt �ŕ����R�[�h�̏��������~�X���Ă����̂��C��
�s���ύX���ɑO�̃��O���c���Ă����̂��C��
�X�}�[�g�z�� (�Ƃ����� 005SH) �ł̉ǐ�������Ɍ��シ��悤�C��
�E�B���h�E���T�C�Y���Ƀ`���b�g�̘g�����T�C�Y�����悤�C��
�ߋ����O�{���ō����̃��O���{���ł���悤�ɕύX
�`���b�g�̕������l�ߋC���\���ɕύX

��ver. 111202_0 (beta)
XML �� MIME type �C���p htaccess.txt ��ǉ�

��ver. 111119_0 (beta)
activity.js ��������ƏC��
�����̓����󋵕\������ activities.js ��ǉ�

��ver. 111117_0 (beta)
conf.xml �� peta2_color.css �̊ȈՃG�f�B�^��ǉ�
�����󋵂�ʃy�[�W�ŕ\������ activity.js ��ǉ�

��ver. 111103_0 (beta)
�t���\�����ɉ{���c�[���ł����Ɖ{���ł���悤�ɒ���

��ver. 111029_0 (beta)
�ߋ����O���t���ŕ\������@�\��ǉ�

��ver. 110614_1 (beta)
IE9 �� RSS ���t�B�[�h��������Ă��Ȃ������̂��C��

��ver. 110614_0 (beta)
IE9 �ŏo��G���[���������C��
IE7 �ł��g����悤�ɂȂ�������
�����[�h�J�E���^�[�̕\�����I�v�V������
RSS �� Content-Type �� application/xml �ɕύX

��ver. 110518_0 (beta)
�p�[�~�b�V�����ύX���̃G���[���o�ɂ����悤�ɏC��

��ver. 110507_0 (beta)
���������̃w���v����� Readme �ɉ��M
�����[�h�J�E���^�[�������[�h�{�^���ɒǉ�
�����폜���Ƀ��O��ǂݒ����悤�ύX
�s���ύX���Ƀ��O��ǂݒ����悤�ύX
�����[�g�z�X�g�̃L���b�V���̃��b�N�����[�������̂��C��
�g�т���уX�}�[�g�z���ł̉ǐ����኱���シ��悤�C��
XSS �̑΍��R�ꂪ�������̂ŏC��

��ver. 110216_0 (alpha)
���}�������[�X
���ۂ̌��J���� 2011.03.29
