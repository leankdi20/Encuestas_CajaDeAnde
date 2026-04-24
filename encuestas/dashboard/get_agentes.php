<?php
include_once "includes_dashboard.php";

$ret = array("mensaje" => "", 
			 "datos" => null);

try {
	$nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : "";
	$sucursal_id = isset($_GET['sucursal']) ? $_GET['sucursal'] : 0; 
	$unidad_id = isset($_GET['unidad']) ? $_GET['unidad'] : 0;
	
	$age = Agente::getAgentesDash($conn, $nombre, $sucursal_id, $unidad_id);
	
	$mens = $age["mensaje"];
	if ($mens != "") throw new Exception("Error cargando los agentes. " . $mens);
	
	$ret["datos"] = $age["agentes"];
} catch (Exception $e) {
	$ret["mensaje"] = $e->getMessage();
}

echo json_encode($ret);

sqlsrv_close($conn);
?>