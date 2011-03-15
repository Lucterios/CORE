<?xml version="1.0" encoding="ISO-8859-1"?>

<!--
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
-->


<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format" version="1.0">

<xsl:template match="/">
	<xsl:apply-templates select="model"/>
</xsl:template>

<xsl:template match="model">
	<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
		<fo:layout-master-set>
			<fo:simple-page-master master-name="page">
				<xsl:attribute name="margin-right"><xsl:value-of select="@margin_right"/>mm</xsl:attribute>
				<xsl:attribute name="margin-left"><xsl:value-of select="@margin_left"/>mm</xsl:attribute>
				<xsl:attribute name="margin-bottom"><xsl:value-of select="@margin_bottom"/>mm</xsl:attribute>
				<xsl:attribute name="margin-top"><xsl:value-of select="@margin_top"/>mm</xsl:attribute>
				<xsl:attribute name="page-width"><xsl:value-of select="@page_width"/>mm</xsl:attribute>
				<xsl:attribute name="page-height"><xsl:value-of select="@page_height"/>mm</xsl:attribute>
				<fo:region-body>
					<xsl:if test="number(page/header/@extent) != 0">
						<xsl:attribute name="margin-top"><xsl:value-of select="page/header/@extent"/>mm</xsl:attribute>
					</xsl:if>
					<xsl:if test="number(page/bottom/@extent) != 0">
						<xsl:attribute name="margin-bottom"><xsl:value-of select="page/bottom/@extent"/>mm</xsl:attribute>
					</xsl:if>
					<xsl:if test="number(page/left/@extent) != 0">
						<xsl:attribute name="margin-left"><xsl:value-of select="page/left/@extent"/>mm</xsl:attribute>
					</xsl:if>
					<xsl:if test="number(page/rigth/@extent) != 0">
						<xsl:attribute name="margin-right"><xsl:value-of select="page/rigth/@extent"/>mm</xsl:attribute>
					</xsl:if>
				</fo:region-body>
				<xsl:if test="number(page/header/@extent) != 0">
					<fo:region-before>
						<xsl:attribute name="extent"><xsl:value-of select="page/header/@extent"/>mm</xsl:attribute>
					</fo:region-before>
				</xsl:if>
				<xsl:if test="number(page/bottom/@extent) != 0">
					<fo:region-after>
						<xsl:attribute name="extent"><xsl:value-of select="page/bottom/@extent"/>mm</xsl:attribute>
					</fo:region-after>
				</xsl:if>
				<xsl:if test="number(page/left/@extent) != 0">
					<fo:region-start>
						<xsl:attribute name="extent"><xsl:value-of select="page/left/@extent"/>mm</xsl:attribute>
					</fo:region-start>
				</xsl:if>
				<xsl:if test="number(page/rigth/@extent) != 0">
					<fo:region-end>
						<xsl:attribute name="extent"><xsl:value-of select="page/rigth/@extent"/>mm</xsl:attribute>
					</fo:region-end>
				</xsl:if>
 			</fo:simple-page-master>
		</fo:layout-master-set>
		<xsl:apply-templates select="page"/>
	</fo:root>
</xsl:template>

<xsl:template name="left-trim">
  <xsl:param name="s" />
  <xsl:choose>
    <xsl:when test="substring($s, 1, 1) = ''">
      <xsl:value-of select="$s"/>
    </xsl:when>
    <xsl:when test="normalize-space(substring($s, 1, 1)) = ''">
      <xsl:call-template name="left-trim">
        <xsl:with-param name="s" select="substring($s, 2)" />
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="$s" />
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template name="right-trim">
  <xsl:param name="s" />
  <xsl:choose>
    <xsl:when test="substring($s, 1, 1) = ''">
      <xsl:value-of select="$s"/>
    </xsl:when>
    <xsl:when test="normalize-space(substring($s, string-length($s))) = ''">
      <xsl:call-template name="right-trim">
        <xsl:with-param name="s" select="substring($s, 1, string-length($s) - 1)" />
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="$s" />
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template name="trim">
  <xsl:param name="s" />
  <xsl:call-template name="right-trim">
    <xsl:with-param name="s">
      <xsl:call-template name="left-trim">
        <xsl:with-param name="s" select="$s" />
      </xsl:call-template>
    </xsl:with-param>
  </xsl:call-template>
</xsl:template>

<xsl:template match='page'>
	<fo:page-sequence master-reference='page'>
		<xsl:if test="number(header/@extent) != 0">
			<xsl:apply-templates select="header"/>
		</xsl:if>
		<xsl:if test="number(bottom/@extent) != 0">
			<xsl:apply-templates select="bottom"/>
		</xsl:if>
		<xsl:if test="number(left/@extent) != 0">
			<xsl:apply-templates select="left"/>
		</xsl:if>
		<xsl:if test="number(rigth/@extent) != 0">
			<xsl:apply-templates select="rigth"/>
		</xsl:if>
		<xsl:apply-templates select="body"/>
	</fo:page-sequence>
</xsl:template>

<xsl:template match='body'>
	<fo:flow flow-name='xsl-region-body'>
		<xsl:apply-templates/>
	</fo:flow>
</xsl:template>

<xsl:template match="header">
 	<fo:static-content flow-name="xsl-region-before">
		<xsl:apply-templates/>
	</fo:static-content>
</xsl:template>

<xsl:template match="bottom">
 	<fo:static-content flow-name="xsl-region-after">
		<xsl:apply-templates/>
	</fo:static-content>
</xsl:template>

<xsl:template match="left">
 	<fo:static-content flow-name="xsl-region-start">
		<xsl:apply-templates/>
	</fo:static-content>
</xsl:template>

<xsl:template match="rigth">
 	<fo:static-content flow-name="xsl-region-end">
		<xsl:apply-templates/>
	</fo:static-content>
</xsl:template>

<xsl:template name="pos_bottom">
	<xsl:param name="position"/>
	<xsl:for-each select="../*[position()=$position]">
		<xsl:value-of select="number(@top)+number(@heigth)"/>
	</xsl:for-each>
</xsl:template>

<xsl:template name="component_space_before">
	<xsl:variable name="top_heigth">
		<xsl:call-template name="pos_bottom">
			<xsl:with-param name="position">
				<xsl:value-of select="position()-1"/>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:variable>
	<xsl:if test="(number(@top) &gt;= 0) and (number($top_heigth) &lt; number(@top))">
		<fo:block>
			<xsl:attribute name="space-before"><xsl:value-of select="number(@top)-number($top_heigth)"/>mm</xsl:attribute>
		</fo:block>
	</xsl:if>
</xsl:template>

<xsl:template name="component_size">
	<xsl:attribute name="height"><xsl:value-of select="@height"/>mm</xsl:attribute>
	<xsl:attribute name="width"><xsl:value-of select="@width"/>mm</xsl:attribute>
	<xsl:if test="number(@top) &gt;= 0">
		<xsl:attribute name="top"><xsl:value-of select="@top"/>mm</xsl:attribute>
	</xsl:if>
	<xsl:if test="number(@left) &gt;= 0">
		<xsl:attribute name="left"><xsl:value-of select="@left"/>mm</xsl:attribute>
	</xsl:if>
</xsl:template>

<xsl:template name="component_border">
	<xsl:if test="@border_style!=''">
		<xsl:attribute name="border-color"><xsl:value-of select="@border_color"/></xsl:attribute>
		<xsl:attribute name="border-style"><xsl:value-of select="@border_style"/></xsl:attribute>
		<xsl:attribute name="border-width"><xsl:value-of select="@border_width"/>mm</xsl:attribute>
	</xsl:if>
	<xsl:if test="@display_align!=''">
		<xsl:attribute name="display-align"><xsl:value-of select="@display_align"/></xsl:attribute>
	</xsl:if>
</xsl:template>

<xsl:template name="component_font">
	<xsl:if test="@line_height!=''">
		<xsl:attribute name="line-height"><xsl:value-of select="@line_height"/>pt</xsl:attribute>
	</xsl:if>			
	<xsl:if test="@font_family!=''">
		<xsl:attribute name="font-family"><xsl:value-of select="@font_family"/></xsl:attribute>
	</xsl:if>
	<xsl:if test="@font_size!=''">
		<xsl:attribute name="font-size"><xsl:value-of select="@font_size"/>pt</xsl:attribute>
	</xsl:if>
	<xsl:if test="@font_weight!=''">
		<xsl:attribute name="font-weight"><xsl:value-of select="@font_weight"/></xsl:attribute>
	</xsl:if>
	<xsl:if test="@text_align!=''">
		<xsl:attribute name="text-align"><xsl:value-of select="@text_align"/></xsl:attribute>
	</xsl:if>
</xsl:template>

<xsl:template match="text">
	<xsl:choose>
		<xsl:when test="not(@spacing) or (number(@spacing)='0')">
			<xsl:call-template name="component_space_before"/>
			<fo:block-container position="absolute"> 
				<xsl:call-template name="component_size"/>
				<xsl:if test="@padding!=''">
					<xsl:attribute name="padding"><xsl:value-of select="@padding"/>mm</xsl:attribute>
				</xsl:if>
				<fo:block>
					<xsl:call-template name="component_border"/>
					<xsl:call-template name="component_font"/>
					<xsl:apply-templates/>
				</fo:block>
			</fo:block-container>
		</xsl:when>
		<xsl:otherwise>
			<fo:block>
				<xsl:call-template name="component_border"/>
				<xsl:if test="@padding!=''">
					<xsl:attribute name="padding"><xsl:value-of select="@padding"/>mm</xsl:attribute>
				</xsl:if>
				<xsl:if test="number(@left) &gt;= 0">
					<xsl:attribute name="margin-left"><xsl:value-of select="@left"/>mm</xsl:attribute>
				</xsl:if>
				<xsl:call-template name="component_font"/>
				<xsl:attribute name="space-before"><xsl:value-of select="@spacing"/>mm</xsl:attribute>
				<xsl:apply-templates/>
			</fo:block>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="table_body">
	<fo:table table-layout="fixed">
		<xsl:if test="(number(@spacing) &gt; 0) and (number(@left) &gt; 0)">
			<fo:table-column>
				<xsl:attribute name="column-width"><xsl:value-of select="@left"/>mm</xsl:attribute>
			</fo:table-column>
		</xsl:if>
		<xsl:for-each select="columns">
			<fo:table-column>
				<xsl:attribute name="column-width"><xsl:value-of select="@width"/>mm</xsl:attribute>
			</fo:table-column>
		</xsl:for-each>
		<fo:table-header>
			<fo:table-row>
			<xsl:if test="(number(@spacing) &gt; 0) and (number(@left) &gt; 0)">
				<fo:table-cell/>
			</xsl:if>
			<xsl:for-each select="columns">
				<xsl:for-each select="cell">
					<fo:table-cell padding="2pt">
						<xsl:call-template name="component_border"/>
						<fo:block>
							<xsl:call-template name="component_font"/>
							<xsl:apply-templates/>
						</fo:block>
					</fo:table-cell>				
				</xsl:for-each>
			</xsl:for-each>
			</fo:table-row>
		</fo:table-header>
		<fo:table-body>
			<xsl:for-each select="rows">
				<fo:table-row>
					<xsl:for-each select="cell">
						<fo:table-cell padding="2pt">
							<xsl:call-template name="component_border"/>
							<fo:block>
								<xsl:call-template name="component_font"/>
								<xsl:choose>
									<xsl:when test="@image=1">
										<xsl:call-template name="addimage"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:apply-templates/>
									</xsl:otherwise>
								</xsl:choose>
								<!-- xsl:if test="@image=1">
									<xsl:call-template name="addimage"/>
								</xsl:if>
								<xsl:if test="@image!=1">
									<xsl:apply-templates/>
								</xsl:if -->
							</fo:block>
						</fo:table-cell>				
					</xsl:for-each>
				</fo:table-row>
			</xsl:for-each>
		</fo:table-body>
	</fo:table>
</xsl:template>

<xsl:template match="table">
	<xsl:if test="number(@spacing)='0'">
		<xsl:call-template name="component_space_before"/>
		<fo:block-container position="absolute"> 
			<xsl:call-template name="component_size"/>
			<xsl:call-template name="component_border"/>
			<xsl:call-template name="component_font"/>
			<fo:block>
				<xsl:call-template name="table_body"/>
                        </fo:block>
		</fo:block-container>
	</xsl:if>
	<xsl:if test="number(@spacing)!=0">
		<fo:block>
			<xsl:call-template name="component_border"/>
			<xsl:call-template name="component_font"/>
			<xsl:if test="number(@left) &gt;= 0">
				<xsl:attribute name="margin-left"><xsl:value-of select="@left"/>mm</xsl:attribute>
			</xsl:if>
			<xsl:if test="@space-before">
				<xsl:attribute name="space-before"><xsl:value-of select="@space_before"/>mm</xsl:attribute>
			</xsl:if>
			<fo:block>
				<xsl:call-template name="table_body"/>
			</fo:block>
		</fo:block>
	</xsl:if>
</xsl:template>

<xsl:template name="addimage">
    <fo:external-graphic>
		<xsl:attribute name="src">url('<xsl:call-template name="trim"><xsl:with-param name="s" select="text()"/></xsl:call-template>')</xsl:attribute>
	</fo:external-graphic>
</xsl:template>

<xsl:template match="image">
	<xsl:if test="number(@spacing)='0'">
		<xsl:call-template name="component_space_before"/>
		<fo:block-container position="absolute"> 
			<xsl:call-template name="component_size"/>
			<xsl:attribute name="padding"><xsl:value-of select="@padding"/>mm</xsl:attribute>
			<xsl:call-template name="component_border"/>
			<xsl:call-template name="component_font"/>
			<fo:block>
				<xsl:call-template name="addimage"/>
            </fo:block>
		</fo:block-container>
	</xsl:if>
	<xsl:if test="number(@spacing)!=0">
		<fo:block>
			<xsl:call-template name="component_border"/>
			<xsl:attribute name="padding"><xsl:value-of select="@padding"/>mm</xsl:attribute>
			<xsl:call-template name="component_font"/>
			<xsl:if test="number(@left) &gt;= 0">
				<xsl:attribute name="margin-left"><xsl:value-of select="@left"/>mm</xsl:attribute>
			</xsl:if>
			<xsl:if test="@space-before">
				<xsl:attribute name="space-before"><xsl:value-of select="@space_before"/>mm</xsl:attribute>
			</xsl:if>
			<fo:block>
				<xsl:call-template name="addimage"/>
			</fo:block>
		</fo:block>
	</xsl:if>
</xsl:template>

<xsl:template match="br">
	<fo:block>
	</fo:block>
</xsl:template>

<xsl:template match="font">
	<fo:inline>
		<xsl:if test="@Font-weight">
			<xsl:attribute name="font-weight"><xsl:value-of select="@Font-weight"/></xsl:attribute>
		</xsl:if>
		<xsl:if test="@Font-style">
			<xsl:attribute name="font-style"><xsl:value-of select="@Font-style"/></xsl:attribute>
		</xsl:if>
		<xsl:if test="@text-decoration">
			<xsl:attribute name="text-decoration"><xsl:value-of select="@text-decoration"/></xsl:attribute>
		</xsl:if>
		<xsl:if test="@color">
			<xsl:attribute name="color"><xsl:value-of select="@color"/></xsl:attribute>
		</xsl:if>
		<xsl:apply-templates/>
	</fo:inline>
</xsl:template>


</xsl:stylesheet>