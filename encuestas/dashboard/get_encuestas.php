<?php
include_once "includes_dashboard.php";

$ret = array("mensaje" => "", 
			 "datos" => null);

try {
	$sucursal_id = isset($_GET["sucursal_id"]) ? $_GET["sucursal_id"] : null;
	$unidad_id = isset($_GET["unidad_id"]) ? $_GET["unidad_id"] : null;
	
	$enc = Dashboard::getEncuestasRes($conn, $sucursal_id, $unidad_id);
	
	$mens = $enc["mensaje"];
	if ($mens != "") throw new Exception("Error cargando las encuestas. " . $mens);
	
	$ret["datos"] = $enc["encuestas"];
} catch (Exception $e) {
	$ret["mensaje"] = $e->getMessage();
}

echo json_encode($ret);

sqlsrv_close($conn);
?>