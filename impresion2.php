<?php
require('codigos/fpdf.php');

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode("Forever3 Entretenimientos"), 0, 1, 'R');
        $this->Cell(0, 10, utf8_decode("Listado de Películas desde Sakila"), 0, 1, 'R');
        $this->Ln(10); 

                // Línea divisora
                $this->Ln(2);
                $this->Cell(0, 0, '', 'T');
                $this->Ln(5);
    }

    // Información de Almacén y Categoría
    function AlmacenCategoriaInfo($almacen, $categoria) {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(20);
        $this->Cell(0, 10, utf8_decode("Almacén: " . $almacen), 0, 1, 'L');
        
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20);
        $this->Cell(0, 10, utf8_decode("Categoría: " . $categoria), 0, 1, 'L');
        $this->Ln(5);
    }

    // Columnas de Películas
    function subtitulosPeliculas() {
        $this->SetFillColor(200, 220, 255); //azul clarito
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(20); //pa centrar 
        $this->Cell(20, 10, "ID", 1, 0, 'C', true); //la c es un parametro para indicarle que tome el color en esa celda junto con el true
        $this->Cell(70, 10, "Nombre", 1, 0, 'C', true);
        $this->Cell(30, 10, "Existencias", 1, 0, 'C', true);
        $this->Cell(30, 10, utf8_decode("Año"), 1, 1, 'C', true);
    }

    // Datos de Película
    function peliculaInfo($id, $nombre, $existencias, $anio) {
        $this->SetFont('Arial', '', 10);
        $this->Cell(20);
        $this->Cell(20, 10, $id, 1, 0, 'C');
        $this->Cell(70, 10, utf8_decode($nombre), 1, 0);
        $this->Cell(30, 10, $existencias, 1, 0, 'C');
        $this->Cell(30, 10, $anio, 1, 1, 'C');
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        
        $this->Cell(0, 10, utf8_decode("----===(" . $this->PageNo() . " / {nb})===----"), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);


include_once("codigos/conexion2.inc");

// películas por almacén y categoría
$AuxSql = "
    SELECT 
        s.store_id AS Almacen,
        c.name AS Categoria,
        f.film_id AS ID,
        f.title AS Nombre,
        COUNT(i.inventory_id) AS Existencias,
        f.release_year AS Anio
    FROM 
        store s
    JOIN 
        inventory i ON s.store_id = i.store_id
    JOIN 
        film f ON i.film_id = f.film_id
    JOIN 
        film_category fc ON f.film_id = fc.film_id
    JOIN 
        category c ON fc.category_id = c.category_id
    GROUP BY 
        s.store_id, c.category_id, f.film_id
    ORDER BY 
        s.store_id, c.name, f.title;
";

$Regis = mysqli_query($conex, $AuxSql) or die(mysqli_error($conex));

$almacenActual = '';
$categoriaActual = '';

while ($row = mysqli_fetch_assoc($Regis)) {
    // Si el Almacén cambia, imprime el nuevo encabezado
    if ($almacenActual != $row['Almacen'] || $categoriaActual != $row['Categoria']) {
        if ($almacenActual != '') {
            $pdf->AddPage();
        }
        $almacenActual = $row['Almacen'];
        $categoriaActual = $row['Categoria'];

        // Imprimir encabezado de Almacén y Categoría
    
        $pdf->AlmacenCategoriaInfo($almacenActual, $categoriaActual);
        $pdf->subtitulosPeliculas();
    }

    // Imprimir datos de película
    $pdf->peliculaInfo(
        $row['ID'], 
        $row['Nombre'], 
        $row['Existencias'], 
        $row['Anio']
    );
}

$pdf->Output();

if (isset($Regis)) {
    mysqli_free_result($Regis);
}
?>
