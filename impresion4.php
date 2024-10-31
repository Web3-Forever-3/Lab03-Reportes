<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<title>Crear XML con acceso a datos 3</title>
	</head>
	<body style="background-color: #FFFFCC; color: #800000">
		
		<h2>Crear XML con Sakila</h2>

		<?php
				// Ruta base para guardar el XML
			$ruta = $_SERVER["DOCUMENT_ROOT"]."/WebIII/practicas/Lab03/";

			// Habilita conexión con el motor de MySQL.
			include_once("codigos/conexion2.inc");

			// Fechas para filtrar los alquileres
			$startDate = '2000-01-01'; // inicio
			$endDate = '2024-10-27';   // final

			// Definir consulta SQL
			$AuxSql = "SELECT 
                film.film_id AS Codigo, 
                film.title AS Nombre,
                COUNT(rental.rental_id) AS Cant_Veces_Alquilada,
                SUM(payment.amount) AS Total_Generado 
            FROM 
                film 
            JOIN inventory ON film.film_id = inventory.film_id
            JOIN rental ON inventory.inventory_id = rental.inventory_id
            JOIN payment ON rental.rental_id = payment.rental_id
            WHERE
                rental.rental_date BETWEEN '$startDate' AND '$endDate'
            GROUP BY 
                film.film_id, film.title
            ORDER BY 
                Cant_Veces_Alquilada DESC 
            LIMIT 10;";

			// Ejecutar la consulta
			$Regis = mysqli_query($conex, $AuxSql) or die(mysqli_error($conex));

		

			// Crear arrays para almacenar los datos
			$codigo = [];
			$nombre = [];
			$VecesAlqui = [];
			$Total_Generado = [];

			// Rellenar los arrays con los datos de la consulta
			$i = 0;
			while($fila = mysqli_fetch_array($Regis)){
				$codigo[$i] = $fila["Codigo"];
				$nombre[$i] = $fila["Nombre"];
				$VecesAlqui[$i] = $fila["Cant_Veces_Alquilada"];
				$Total_Generado[$i] = $fila["Total_Generado"];
				$i++;
			}

			// Liberar espacio de la consulta
			mysqli_free_result($Regis);

			// Impresión de los datos (solo prueba)
			$canti = sizeof($codigo);
			for($j = 0; $j < $canti; $j++){
				print("------------------------------------------------------------------------<br>");
				printf("Película: %s - %s<br>", $codigo[$j], $nombre[$j]);
				printf("Veces Alquilada: %s<br>", $VecesAlqui[$j]);
				printf("Total Generado: $%s<br>", $Total_Generado[$j]);
				print("------------------------------------------------------------------------<br>");
			}
			print("<br>");
				


			//creacion del documento xml
			$xml = "<?xml version='1.0' encoding='utf-8' ?>";
			$xml .= "<?xml-stylesheet type='text/xsl' href='estilos/formatos.xsl'?>"; 
			$xml .= "<informacion>";
			$xml .= "   <clasificacion>";
		
			for($j = 0; $j < $canti; $j++) {
				$xml .= "<pelicula>";
				$xml .= "<codigo>".$codigo[$j]."</codigo>";
				$xml .= "<nombre>".$nombre[$j]."</nombre>";
				$xml .= "<veces_alquilada>".$VecesAlqui[$j]."</veces_alquilada>";
				$xml .= "<total_generado>".$Total_Generado[$j]."</total_generado>";
				$xml .= "</pelicula>";
			}
			
			$xml .= "   </clasificacion>";
			$xml .= "</informacion>";


			//escribir archivo xml
			$ruta = $ruta."primer.xml";

			try{
				$archivo = fopen($ruta,"w+");
				fwrite($archivo,$xml);
				fclose($archivo);
			}catch(Exception $e){
				echo "Error:..".$e->getMessage();
			}
		?>

		<a href="primer.xml">XML Generado</a><br />
		<a href="descargaxml.php">Descargar archivo xml</a>


	</body>
</html>