<?php
    //parametros de conexion (opcionales)
	$servidor  = "localhost"; //dejar asi para que puedan probarlo todos xd
    $basedatos = "sakila";
    $usuario   = "root";
<<<<<<< HEAD
    $contra    = "Laila_17";
=======
    $contra    = "Kevin12!!";
>>>>>>> 97c588c2c66cfe635d3da5609f864e5f7fb1f75a
	
    //suprime advertencias
    //error_reporting(0);

	//proceso para conectar con el servidor 
    if(!$conex = mysqli_connect($servidor, $usuario, $contra, $basedatos)){
        echo "<h3><font color='red'>Error: No se puede conectar al servidor de MySQL.</font></h3>" . "<hr>";
        echo "<strong>Número........:</strong> " . mysqli_connect_errno() . "<br>";
        echo "<strong>Descripción...:</strong> " . mysqli_connect_error() . "<br>";
        exit;          
    }    
	mysqli_set_charset($conex,"utf8");
?>


