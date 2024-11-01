<?php
require('codigos/conexion2.inc');
require('codigos/fpdf.php');

class PDF extends FPDF {
    private $fecha_inicial;
    private $fecha_final;

    // Constructor para recibir las fechas
    function __construct($fecha_inicial, $fecha_final) {
        parent::__construct();
        $this->fecha_inicial = $fecha_inicial;
        $this->fecha_final = $fecha_final;
    }

    //  Encabeza ooo 
    function Header() {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10,'Sakila Entretenimientos',0,1,'C');
        $this->Cell(0,10,"Reporte de Ingresos " . $this->fecha_inicial . " a " . $this->fecha_final,0,1,'C');
        $this->Ln(10);
        $this->SetFont('Arial','B',10);
        $this->Cell(20,10,'ID',1,0,'C');
        $this->Cell(70,10,'Nombre',1,0,'C'); //ancho, altura, texto, borde, salto, alineación
        $this->Cell(40,10,'Genero',1,0,'C');
        $this->Cell(40,10,'Monto',1,0,'C');
        $this->Ln();
    }
    
    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Variables de rango de fecha
$fecha_inicial = isset($_GET['fecha_inicial']) ? $_GET['fecha_inicial'] : '2005-01-01'; //
$fecha_final = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : '2006-12-31';    

// Consulta SQL
$sql = "SELECT f.film_id AS ID, f.title AS Nombre, c.name AS Genero, 
        SUM(p.amount) AS Monto
        FROM rental r
        INNER JOIN inventory i ON r.inventory_id = i.inventory_id
        INNER JOIN store s ON i.store_id = s.store_id
        INNER JOIN film f ON i.film_id = f.film_id
        INNER JOIN payment p ON r.rental_id = p.rental_id
        INNER JOIN film_category fc ON f.film_id = fc.film_id
        INNER JOIN category c ON fc.category_id = c.category_id
        WHERE r.rental_date BETWEEN '$fecha_inicial' AND '$fecha_final'
        GROUP BY f.film_id, s.store_id, c.name
        ORDER BY Monto DESC";

$result = mysqli_query($conex, $sql);
$total_monto = 0;

// Crear PDF
$pdf = new PDF($fecha_inicial, $fecha_final); 
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

// Agregar datos al PDF
while($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(20,10,$row['ID'],1,0,'C');
    $pdf->Cell(70,10,$row['Nombre'],1,0,'C');
    $pdf->Cell(40,10,$row['Genero'],1,0,'C');
    $pdf->Cell(40,10,number_format($row['Monto'], 2),1,0,'C');
    $pdf->Ln();
    $total_monto += $row['Monto'];
}

// Total acumulado
$pdf->SetFont('Arial','B',10);
$pdf->Cell(130,10,utf8_decode('Total Alquiler Acumulado por Almacén:'),1); // con el decode utf 8 hace que la ñ ñalga ñien
$pdf->Cell(40,10,number_format($total_monto, 2),1);

$pdf->Output();
?>
