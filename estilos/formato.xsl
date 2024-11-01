<?xml version="1.0" encoding="utf-8"?>
<!-- DWXMLSource="http://localhost/xml_01/generales_6.xml" -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8"/>

	<!-- Plantilla General del xml -->
	<xsl:template match="/">
    	<html>
            <head>
                <title>Inventario Sakila</title>
            </head>
            <body style="background:#FFF;font-family:Verdana, Geneva, sans-serif; font-size:12px">
                
                <h2>Inventario Sakila</h2>
				<h4>Lab03 --- Forever 3</h4>
                
                
                <br /> 
                
                <!-- Bucle para cada película dentro de la clasificación -->
               <xsl:for-each select="informacion/clasificacion/pelicula">
                    <table align="center" width="500px" border="4" style="margin-bottom: 20px;">
                        <tr bgcolor="#cccccc">
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Veces Alquilada</th>
                            <th>Total Generado</th>
                        </tr>
                        <tr>
                            <td><xsl:value-of select="codigo"/></td>
                            <td><xsl:value-of select="nombre"/></td>
                            <td><xsl:value-of select="veces_alquilada"/></td>
                            <td><xsl:value-of select="total_generado"/></td>
                        </tr>
                    </table>
                    <br /> 
                </xsl:for-each>
                
            </body>
        </html>
	</xsl:template>
	
</xsl:stylesheet>