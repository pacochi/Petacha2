<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php xsl" version="1.0" >
<xsl:output method="xml" indent="yes" encoding="UTF-8" />
<xsl:template match="/logs">
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns="http://purl.org/rss/1.0/">
  <channel rdf:about="{text/rssurl}">
    <title><xsl:value-of select="text/title" /></title>
    <link><xsl:value-of select="text/scripturl" /></link>
    <description><xsl:value-of select="text/rssdesc" /></description>
    <dc:creator><xsl:value-of select="text/adminname" /></dc:creator>
    <items>
      <rdf:Seq>

<xsl:for-each select="chat">
<rdf:li rdf:resource="{/logs/text/scripturl}#r{id}"/>
</xsl:for-each>

      </rdf:Seq>
    </items>
  </channel>

<xsl:for-each select="chat">

  <item rdf:about="{/logs/text/scripturl}#r{id}">
    <title><xsl:value-of select="body" /></title>
    <link><xsl:value-of select="/logs/text/scripturl" /><xsl:text>#r</xsl:text><xsl:value-of select="id" /></link>
    <dc:date><xsl:value-of select="rssdate" /></dc:date>
    <dc:creator><xsl:value-of select="name" /></dc:creator>
  </item>

</xsl:for-each>

</rdf:RDF>
</xsl:template>
</xsl:stylesheet>
