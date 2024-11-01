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

// Crear un array para almacenar los datos de las películas
$peliculas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pelicula = [
        'Codigo' => $row['Codigo'],
        'Nombre' => $row['Nombre'],
        'Descripcion' => $row['Descripcion'],
        'Categorias' => $row['Categorias'],
        'Año' => $row['Año'],
        'Actores' => []
    ];

    // Obtener la lista de actores para la película actual
    $actor_sql = "SELECT CONCAT(a.first_name, ' ', a.last_name) AS Actor
                  FROM film_actor fa
                  JOIN actor a ON fa.actor_id = a.actor_id
                  WHERE fa.film_id = " . intval($row['Codigo']) . "
                  ORDER BY Actor";
    $actor_result = mysqli_query($conex, $actor_sql);
    
    while ($actor_row = mysqli_fetch_assoc($actor_result)) {
        $pelicula['Actores'][] = $actor_row['Actor'];
    }

    $peliculas[] = $pelicula;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Películas - Sakila</title>
    <link rel="stylesheet" href="estilos/impresion5.css"
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
                <th>Actores</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($peliculas as $pelicula): ?>
                <tr>
                    <td><?php echo $pelicula['Codigo']; ?></td>
                    <td><?php echo htmlspecialchars($pelicula['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($pelicula['Descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($pelicula['Categorias']); ?></td>
                    <td><?php echo $pelicula['Año']; ?></td>
                    <td><?php echo implode(', ', $pelicula['Actores']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
