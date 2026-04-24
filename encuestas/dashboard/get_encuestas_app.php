<?php
include_once "includes_dashboard.php";

$ret = array("mensaje" => "", 
			 "datos" => null);

try {
	$enc = Dashboard::getEncuestasApp($conn);
	
	$mens = $enc["mensaje"];
	if ($mens != "") throw new Exception("Error cargando las encuestas. " . $mens);
	
	$ret["datos"] = $enc["encuestas"];
} catch (Exception $e) {
	$ret["mensaje"] = $e->getMessage();
}

echo json_encode($ret);

sqlsrv_close($conn);
?>