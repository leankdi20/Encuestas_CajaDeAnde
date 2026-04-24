<?php
include_once "includes_dashboard.php";

$ret = array("mensaje" => "", 
						 "datos" => null);

try {
	$p_cedula = isset($_GET['cedula']) ? trim($_GET['cedula']) : "";
	
	$cor = WebServices::correoPorCedula($p_cedula);
	
	$ret["datos"]["correo"] = $cor;
} catch (Exception $e) {
	$ret["mensaje"] = $e->getMessage();
}

echo json_encode($ret);

sqlsrv_close($conn);
?>