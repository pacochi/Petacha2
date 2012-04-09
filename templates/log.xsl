<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
<xsl:output omit-xml-declaration="yes" method="xml" encoding="UTF-8" />
<xsl:template match="/logs">
<html xml:lang="ja">

    <!--参加者-->
    <xsl:for-each select="member">
     <li class="member" style="color:{color};">
      <xsl:if test="self='yes'">
       <xsl:attribute name="class">member self</xsl:attribute>
      </xsl:if>
      <xsl:value-of select="name" />
     </li>
    </xsl:for-each>

     <!--発言-->
     <xsl:for-each select="chat">
      <p class="chat" style="color:{color};" id="r{id}">
       <span class="cid" title="{cid}" style="color:#{cid};background-color:#{cid};"><xsl:value-of select="cid" />
       <xsl:text> </xsl:text></span>
       <span class="name">
       <xsl:if test="ext='system'">
        <xsl:attribute name="class">name system</xsl:attribute>
       </xsl:if>
       <xsl:value-of select="name" />
       </span><xsl:value-of select="/logs/text/said" />
       <xsl:for-each select="body/*">
        <xsl:choose>
         <xsl:when test="name()='bn'">
          <label id="bnr{../../id}-{position()}" class="bn"><input type="radio" class="bn" value="{.}" /><xsl:value-of select="." /></label>
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
     <xsl:for-each select="error">
      <p class="alert" title="{.}">
       <strong>
        <xsl:value-of select="/logs/text/announce" />
        <xsl:variable name="type" select="."/>
        <xsl:value-of select="/logs/text/error[@type=$type]" />
       </strong>
       <button class="close" type="button"><xsl:value-of select="/logs/text/close" /></button>
      </p>
     </xsl:for-each>

     <!--メッセージ-->
     <xsl:for-each select="message">
      <p class="note" title="{.}">
       <strong>
        <xsl:value-of select="/logs/text/announce" />
        <xsl:variable name="type" select="."/>
        <xsl:value-of select="/logs/text/message[@type=$type]" />
       </strong>
       <button class="close" type="button"><xsl:value-of select="/logs/text/close" /></button>
      </p>
     </xsl:for-each>

</html>
</xsl:template>
</xsl:stylesheet>
