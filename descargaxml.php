<?php
	header('Content-disposition: attachment; filename=primer.xml');
	header('Content-type: application/octet-stream .xml; charset=utf-8');

	//obtiene raiz del sitio
    $ruta = $_SERVER["DOCUMENT_ROOT"]."/WebIII/practicas/Lab03/primer.xml";

	readfile($ruta);
?>
