<?php
require('codigos/conexion2.inc'); 

// Consulta SQL
$sql = "SELECT 
            f.film_id AS Codigo,
            f.title AS Nombre,
            f.description AS Descripcion,
            GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') AS Categorias,
            f.release_year AS Año
        FROM 
            film f
        JOIN 
            film_category fc ON f.film_id = fc.film_id
        JOIN 
            category c ON fc.category_id = c.category_id
        JOIN 
            inventory i ON f.film_id = i.film_id
        JOIN 
            rental r ON i.inventory_id = r.inventory_id
        JOIN 
            payment p ON r.rental_id = p.rental_id
        JOIN 
            film_actor fa ON f.film_id = fa.film_id
        JOIN 
            actor a ON fa.actor_id = a.actor_id
        GROUP BY 
            f.film_id, f.title, f.description, f.release_year
        ORDER BY 
            f.film_id";

$result = mysqli_query($conex, $sql);

// Crear un string XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<Peliculas>';

while ($row = mysqli_fetch_assoc($result)) {
    $xml .= '<Pelicula>';
    $xml .= '<Codigo>' . htmlspecialchars($row['Codigo']) . '</Codigo>';
    $xml .= '<Nombre>' . htmlspecialchars($row['Nombre']) . '</Nombre>';
    $xml .= '<Descripcion>' . htmlspecialchars($row['Descripcion']) . '</Descripcion>';
    $xml .= '<Categorias>' . htmlspecialchars($row['Categorias']) . '</Categorias>';
    $xml .= '<Año>' . htmlspecialchars($row['Año']) . '</Año>';
    $xml .= '</Pelicula>';
}

$xml .= '</Peliculas>';

// Guardar el XML en un archivo
$ruta = 'peliculas.xml';
file_put_contents($ruta, $xml);

// Se reinicia el puntero del resultado para poder usarlo de nuevo
mysqli_data_seek($result, 0); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Películas - Sakila</title>
    <link rel="stylesheet" href="estilos/impresion5.css">
</head>
<body>
    <h1>Lista de Películas</h1>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Categorías</th>
                <th>Año</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Codigo']); ?></td>
                    <td><?php echo htmlspecialchars($row['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['Descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($row['Categorias']); ?></td>
                    <td><?php echo htmlspecialchars($row['Año']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    
    <a href="peliculas.xml" download>
        <button>Descargar XML</button>
    </a>
</body>
</html>
