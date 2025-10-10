<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
<html>
<head>
    <title>Pins Feed</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            background: #fafafa;
            color: #333;
            margin: 2rem;
        }
        h1 {
            font-size: 1.8rem;
            color: #222;
            margin-bottom: 0.5rem;
        }
        p.meta {
            color: #666;
            margin-bottom: 1.5rem;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            border-bottom: 1px solid #ddd;
            text-align: left;
            padding: 0.75rem;
            vertical-align: top;
        }
        th {
            background: #f5f5f5;
        }
        td img {
            max-width: 120px;
            border-radius: 8px;
        }
        .categories {
            color: #0066cc;
            font-style: italic;
        }
        .stats {
            color: #888;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <h1><xsl:value-of select="/pins/metadata/site"/> Pins</h1>
    <p class="meta">
        Source: <a href="{/pins/metadata/loc}">
            <xsl:value-of select="/pins/metadata/loc"/>
        </a><br/>
        Generated: <xsl:value-of select="/pins/metadata/generated"/>
        — Total: <xsl:value-of select="/pins/metadata/count"/>
    </p>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Description</th>
                <th>Author</th>
                <th>Categories</th>
                <th>Stats</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/pins/pin">
                <tr>
                    <td>
                        <a href="{loc}">
                            <img src="{image/url}" alt="{title}" />
                        </a>
                    </td>
                    <td>
                        <strong>
                            <a href="{loc}" target="_blank" style="color:#0077cc; text-decoration:none;">
                                <xsl:value-of select="title"/>
                            </a>
                        </strong><br/>
                        <small><xsl:value-of select="created"/></small>
                    </td>
                    <td><xsl:value-of select="description"/></td>
                    <td>
                        <a href="{author/profile}">
                            <xsl:value-of select="author/name"/>
                        </a>
                    </td>
                    <td class="categories">
                        <xsl:for-each select="categories/category">
                            <xsl:value-of select="."/>
                            <xsl:if test="position()!=last()">, </xsl:if>
                        </xsl:for-each>
                    </td>
                    <td class="stats">
                        ❤️ <xsl:value-of select="stats/likes"/> <br/>
                        💬 <xsl:value-of select="stats/comments"/>
                    </td>
                </tr>
            </xsl:for-each>
        </tbody>
    </table>

    <div style="margin-top:1rem; color:#666;">
        Page <xsl:value-of select="/pins/pagination/current-page"/> /
        <xsl:value-of select="/pins/pagination/total-pages"/>
    </div>
</body>
</html>
</xsl:template>

</xsl:stylesheet>