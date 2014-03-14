<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php xsl" version="1.0" >
<xsl:output method="xml" indent="yes" encoding="UTF-8" />
<xsl:template match="/logs">
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title><xsl:value-of select="text/title" /></title>
    <link><xsl:value-of select="rssurl" /></link>
    <description><xsl:value-of select="text/rssdesc" /></description>
    <dc:language>ja</dc:language>
    <dc:creator><xsl:value-of select="text/adminname" /></dc:creator>
    <dc:date><xsl:value-of select="chat[1]/rssdate" /></dc:date>
    <atom:link href="{rssurl}" rel="self" type="application/rss+xml" />
    <atom:link rel="hub" href="http://pubsubhubbub.appspot.com" />

<xsl:for-each select="chat">

  <item>
    <title><xsl:value-of select="body" /></title>
    <link><xsl:value-of select="guid" /></link>
    <guid><xsl:value-of select="guid" /></guid>
    <dc:date><xsl:value-of select="rssdate" /></dc:date>
    <dc:creator><xsl:value-of select="name" /></dc:creator>
  </item>

</xsl:for-each>

  </channel>
</rss>
</xsl:template>
</xsl:stylesheet>
