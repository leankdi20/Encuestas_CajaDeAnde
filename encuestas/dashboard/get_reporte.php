<?php
include_once "includes_dashboard.php";

$ret = array("mensaje" => "", "datos" => null);

try {
	$sucursal_id = isset($_GET["sucursal_id"]) ? $_GET["sucursal_id"] : 0;
	$unidad_id = isset($_GET["unidad_id"]) ? $_GET["unidad_id"] : 0;
	$respuesta_id = isset($_GET["respuesta_id"]) ? $_GET["respuesta_id"] : 0;
	
	$ret = Dashboard::consultarReporte($conn, $_GET["id"], $sucursal_id, $unidad_id, $respuesta_id);
} catch (Exception $e) {
	$ret["mensaje"] = $e->getMessage();
}

echo json_encode($ret);

sqlsrv_close($conn);
?>