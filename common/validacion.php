<?php
/**
* validacion.php
*
* Archivo que valida el estado de la sesion actual
*/
if ( ! isset($_SESSION['validacion']) ) {
	$_SESSION['validacion'] = 0;
	$_SESSION['mensaje'] = "Acceso negado.";
	$_SESSION['accion'] = "index.php";
	header("Location: " . _PATH_RELATIVO_ . "/error.php");
	exit;
}
?>
