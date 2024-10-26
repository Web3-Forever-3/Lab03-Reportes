<?php
   //Libreria en php para crear documentos .pdf
   require('codigos/fpdf.php');

   
   class PDF extends FPDF{
   
        
    function Header() {
    
     //espacio
    $this->Ln(10);

    }

    //en realidad este es el header xd
    function clienteInfo($nombreCliente, $contacto, $ubicacion) {
    

        //Cliente y Fecha Consulta
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(25, 5, utf8_decode("Cliente:"), 0, 0);
    $this->SetFont('Arial', '', 12); 
    $this->Cell(90, 5, utf8_decode($nombreCliente), 0, 0);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(40, 5, utf8_decode("Fecha de Consultas"), 0, 1);
   
        //  Contacto y  Fecha de inicio 
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(25, 5, utf8_decode("Contacto:"), 0, 0);
    $this->SetFont('Arial', '', 12); 
    $this->Cell(90, 5, utf8_decode($contacto), 0, 0);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(30, 5, utf8_decode("Fecha Inicio:"), 0, 0);
    $this->SetFont('Arial', '', 12); 
    $this->Cell(30, 5, utf8_decode( "Alguna fecha xd " ), 0, 1);

        // Ubicación y Fecha Final
    $this->SetFont('Arial', 'B', 12); 
    $this->Cell(25, 5, utf8_decode("Ubicación:"), 0, 0);
    $this->SetFont('Arial', '', 12); 
    $this->Cell(90, 5, utf8_decode($ubicacion),0 , 0);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(30, 5, utf8_decode("Fecha Final:"), 0, 0);
    $this->SetFont('Arial', '', 12); 
    $this->Cell(30, 5, utf8_decode( "Alguna fecha xd " ), 0, 1);

     //espacio
    $this->Ln(15);

    }

    // Datos factura
    function FacturaInfo($FacturaID, $FechaFacturacion, $Empleado, $FechaRequerida, $FechaDespachada) {
        
        $this->SetFillColor(210, 240, 210); 

       

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20,7,"Factura #:",'TL',0, '', true);
        $this->SetFont('Arial','',11);
        $this->Cell(75,7, $FacturaID,'T',0,'', true);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(50,7, utf8_decode("Fecha de facturación: "),'T',0,'', true);
        $this->SetFont('Arial','',11);
        $this->Cell(40,7,utf8_decode("$FechaFacturacion"),'TR',1,'', true);

        
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(24,7,"Empleado:",'L',0,'', true);
        $this->SetFont('Arial','',11);
        $this->Cell(80,7," $Empleado",0,0,'', true);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30,7,"Requerida:",0,0,'', true);
        $this->SetFont('Arial','',11);
        $this->Cell(51,7,"$FechaRequerida",'R',1,'', true);
        
        $this->Cell(105,10," ",'LB',0,'', true);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30,10,"Despachada: ",'B',0,'', true);
        $this->SetFont('Arial','',11);
        $this->Cell(50,10,"$FechaDespachada",'BR',1,'', true);
        $this->Ln(13);
    }

    function subtitulos(){
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30,10,utf8_decode("Código "),1,0);
        $this->Cell(60,10,"Nombre ",1,0);
        $this->Cell(20,10,"Cantidad ",1,0);
        $this->Cell(25,10,"Precio Uni ",1,0);
        $this->Cell(25,10,"Descuento ",1,0);
        $this->Cell(30,10,"Total",1,1);

    }

    // Imprime productos de la factura
    function ProductoInfo($Codigo, $Nombre, $Cantidad, $Precio, $Descuento, $Total) {
        
                
        $this->SetFont('Arial','',10);
        $this->Cell(30,10,$Codigo,1);
        $this->Cell(60,10,utf8_decode($Nombre),1);
        $this->Cell(20,10,$Cantidad,1,0,'R');
        $this->Cell(25,10,number_format($Precio, 2),1,0,'R');
        $this->Cell(25,10,($Descuento * 100)."%",1,0,'R');
        $this->Cell(30,10,number_format($Total, 2),1,1,'R');
    }

   }

    //crear el nuevo pdf
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times','',12);

    // Conexión con MySQL
    include_once("codigos/conexion.inc");

    // Consulta SQL
    $AuxSql = "
        SELECT 
            c.CompanyName AS NombreCliente,
            CONCAT(c.ContactTitle, ' ', c.ContactName) AS Contacto,
            CONCAT(c.Country, ', ', c.City, ', ', c.PostalCode) AS Ubicacion,
            o.OrderID AS NumeroFactura,
            CONCAT(e.FirstName, ' ', e.LastName) AS NombreEmpleado,
            o.OrderDate AS FechaFacturacion,
            o.RequiredDate AS FechaRequerida,
            o.ShippedDate AS FechaDespachada,
            p.ProductID AS CodigoProducto,
            p.ProductName AS NombreProducto,
            od.Quantity AS Cantidad,
            od.UnitPrice AS PrecioUnitario,
            od.Discount AS Descuento,
            (od.Quantity * od.UnitPrice * (1 - od.Discount)) AS TotalProducto
        FROM 
            customers c
        JOIN 
            orders o ON c.CustomerID = o.CustomerID
        JOIN 
            employees e ON o.EmployeeID = e.EmployeeID
        JOIN 
            order_details od ON o.OrderID = od.OrderID
        JOIN 
            products p ON od.ProductID = p.ProductID
        WHERE 
            o.OrderDate BETWEEN '1990-01-01' AND '2000-12-31'
            AND c.CompanyName = 'Alfreds Futterkiste'
        ORDER BY 
            c.CompanyName, o.OrderDate, o.OrderID;
    ";
    //para cambiar a otro cliente pueden buscarse en la bd con el seleect * from

   // Ejecutar consulta
$Regis = mysqli_query($conex, $AuxSql) or die(mysqli_error($conex));

// Variables para seguimiento
$FacturaActual = '';

// Recorrer resultados de la consulta
while ($row = mysqli_fetch_assoc($Regis)) {
    // Si el ID de la factura cambia, pasa a una nueva página
    if ($FacturaActual != $row['NumeroFactura']) {
        if ($FacturaActual != '') {
            $pdf->AddPage(); // Agrega nueva página si no es la primera factura
        }
        $FacturaActual = $row['NumeroFactura'];

        // Imprimir encabezado y datos del cliente
        $pdf-> Header();
        $pdf->clienteInfo($row['NombreCliente'], $row['Contacto'], $row['Ubicacion']);
        $pdf->FacturaInfo(
            $row['NumeroFactura'],
            $row['FechaFacturacion'], 
            $row['NombreEmpleado'], 
            $row['FechaRequerida'], 
            $row['FechaDespachada']
        );
        $pdf-> subtitulos();
    }
    
    
    $pdf->ProductoInfo(
        $row['CodigoProducto'], 
        $row['NombreProducto'], 
        $row['Cantidad'], 
        $row['PrecioUnitario'], 
        $row['Descuento'], 
        $row['TotalProducto']
    );

}
// $pdf->Footer();
// Generar y enviar el PDF al navegador
$pdf->Output();

// Liberar resultados
if (isset($Regis)) {
    mysqli_free_result($Regis);
}
?>