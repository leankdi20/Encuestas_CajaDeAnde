<?php
include_once "includes_dashboard.php";

$ret = array("mensaje" => "", 
			 "datos" => null);

try {
	$suc = Sucursal::getSucursalesUnidades($conn);
	
	$mens = $suc["mensaje"];
	if ($mens != "") throw new Exception("Error cargando las sucursales. " . $mens);
	
	$ret["datos"] = $suc["sucursales"];
} catch (Exception $e) {
	$ret["mensaje"] = $e->getMessage();
}

echo json_encode($ret);

sqlsrv_close($conn);
?>