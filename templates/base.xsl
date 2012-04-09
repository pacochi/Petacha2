<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php xsl" version="1.0" >
<xsl:output omit-xml-declaration="no" method="xml" doctype-public="-//W3C//DTD XHTML 1.1//EN" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" indent="yes" encoding="UTF-8" />
<xsl:template match="/page">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
 <head>
  <meta name="author" content="{conf/text/adminname}" />
  <meta name="viewport" content="width=device-width,user-scalable=yes,initial-scale=1.0" />
  <link type="text/css" rel="stylesheet" href="{conf/path/dir/resources}peta2.css" media="all" />
  <link type="application/rss+xml" rel="alternate" title="RSS" href="{conf/text/rssurl}" />
  <title><xsl:value-of select="conf/text/title" /></title>
  <script type="text/javascript" src="{conf/path/dir/resources}jquery-1.6.1.min.js" charset="UTF-8"><xsl:text> </xsl:text></script>
  <script type="text/javascript" src="{conf/path/dir/resources}jquery.tag.js" charset="UTF-8"><xsl:text> </xsl:text></script>
  <script type="text/javascript" src="{conf/path/dir/resources}peta2.js" charset="UTF-8"><xsl:text> </xsl:text></script>
 </head>
 <body id="peta2">

  <div id="header">
   <h1><xsl:value-of select="conf/text/title" />
   <xsl:if test="user/join=1">
    <xsl:value-of select="conf/text/join" />
   </xsl:if>
   </h1>
  </div><!--div#header-->

  <div id="body">

   <ul id="member">
    <li class="member-text">
     <xsl:value-of select="/page/conf/text/member" /><xsl:value-of select="conf/text/spliter" />
    </li>
    <!--参加者-->
    <xsl:for-each select="logs/member">
     <li class="member" style="color:{color};">
      <xsl:if test="self='yes'">
       <xsl:attribute name="class">member self</xsl:attribute>
      </xsl:if>
      <xsl:value-of select="name" />
     </li>
    </xsl:for-each>
   </ul>

   <form action="#bn" id="bn">
    <div id="chat">

     <!--発言-->
     <xsl:for-each select="logs/chat">
      <p class="chat" style="color:{color};" id="r{id}">
       <span class="cid" title="{cid}" style="color:#{cid};background-color:#{cid};"><xsl:value-of select="cid" />
       <xsl:text> </xsl:text></span>
       <span class="name">
       <xsl:if test="ext='system'">
        <xsl:attribute name="class">name system</xsl:attribute>
       </xsl:if>
       <xsl:value-of select="name" />
       </span><xsl:value-of select="/page/conf/text/said" />
       <xsl:for-each select="body/*">
        <xsl:choose>
         <xsl:when test="name()='bn'">
          <em class="bn"><xsl:value-of select="." /></em>
         </xsl:when>
         <xsl:when test="name()='url'">
          <a href="{.}" class="chatlink"><xsl:value-of select="." /></a>
         </xsl:when>
         <xsl:when test="name()='name'">
          <strong class="name" style="color:{@color};"><xsl:value-of select="." /></strong>
         </xsl:when>
         <xsl:when test="name()='cmd'">
          <em class="command"><xsl:value-of select="." /></em>
         </xsl:when>
         <xsl:otherwise>
          <xsl:value-of select="." />
         </xsl:otherwise>
        </xsl:choose>
       </xsl:for-each>
       <span class="date"><xsl:value-of select="date" /></span>
      </p>
     </xsl:for-each>

     <!--アラート-->
     <xsl:for-each select="logs/error">
      <p class="alert" title="{.}">
       <strong>
        <xsl:value-of select="/page/conf/text/announce" />
        <xsl:variable name="type" select="."/>
        <xsl:value-of select="/page/conf/text/error[@type=$type]" />
       </strong>
      </p>
     </xsl:for-each>

     <!--メッセージ-->
     <xsl:for-each select="logs/message">
      <p class="note" title="{.}">
       <strong>
        <xsl:value-of select="/page/conf/text/announce" />
        <xsl:variable name="type" select="."/>
        <xsl:value-of select="/page/conf/text/message[@type=$type]" />
       </strong>
      </p>
     </xsl:for-each>

    <xsl:text> </xsl:text></div><!--div#chat-->
    <p class="dummybn">
     <input type="radio" name="bn" value="" checked="checked" />
    </p>
   </form>

   <div id="write">
    <form action="{conf/text/scripturl}#r{logs/chat[last()]/id}" method="post" id="say" accept-charset="UTF-8">

     <p>
      <!--発言欄、発言ボタン-->
      <input type="text" autocomplete="off" name="m" id="m" tabindex="1" accesskey="0" value="" />
      <button type="submit" id="s" tabindex="1" accesskey="s"><xsl:value-of select="conf/text/say" /></button>
     </p>

     <p>
      <!--名前、色、行数、リロード-->
      <label><xsl:value-of select="conf/text/name" /><xsl:value-of select="conf/text/spliter" />
      <input type="text" value="{user/name}" name="n" id="n" tabindex="1" accesskey="n" /></label>

      <label><xsl:value-of select="conf/text/color" /><xsl:value-of select="conf/text/spliter" />
      <select name="c" id="c" tabindex="1">
      <xsl:for-each select="user/select/color">
       <option value="{.}" style="color:{.};">
       <xsl:if test="@default='yes'">
        <xsl:attribute name="selected">selected</xsl:attribute>
       </xsl:if>
       <xsl:value-of select="." />
       </option>
      </xsl:for-each>
      </select></label>

      <label><xsl:value-of select="conf/text/line" /><xsl:value-of select="conf/text/spliter" />
      <select name="l" id="l" tabindex="1">
      <xsl:for-each select="user/select/line">
       <option value="{.}">
       <xsl:if test="@default='yes'">
        <xsl:attribute name="selected">selected</xsl:attribute>
       </xsl:if>
       <xsl:value-of select="." />
       </option>
      </xsl:for-each>
      </select></label>

      <label><xsl:value-of select="conf/text/reloadsec" /><xsl:value-of select="conf/text/spliter" />
      <select name="r" id="r" tabindex="1">
      <xsl:for-each select="user/select/reloadsec">
       <option value="{.}">
       <xsl:if test="@default='yes'">
        <xsl:attribute name="selected">selected</xsl:attribute>
       </xsl:if>
       <xsl:choose>
        <xsl:when test=".=0">
         <xsl:value-of select="/page/conf/text/noreload" />
        </xsl:when>
        <xsl:otherwise>
         <xsl:value-of select="." />
        </xsl:otherwise>
       </xsl:choose>
       </option>
      </xsl:for-each>
      </select></label>

      <input type="hidden" name="a" id="a" value="0" />
      <input type="hidden" name="i" id="i" value="0" />
     </p>

    </form>
   </div><!--div#write-->

  </div><!--div#body-->

  <div id="footer">
   <ul class="navi">
    <xsl:for-each select="conf/text/link">
     <li>
      <a href="{@url}"><xsl:value-of select="." /></a>
     </li>
    </xsl:for-each>
   </ul>
  </div><!--div#footer-->

 </body>
</html>
</xsl:template>
</xsl:stylesheet>
