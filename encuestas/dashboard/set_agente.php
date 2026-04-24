<?php
include_once "includes_dashboard.php";

$ret = array("mensaje" => "", 
						 "datos" => null);

try {
	// ------------------ BLOQUE DE VALIDACIONES ------------------ //
	if (!isset($_GET['accion'])) throw new Exception("Parámetros incompletos (acción).");
	$accion = $_GET['accion'];
	
	if ($accion == "insertar") {
		if (!isset($_GET['nombre'])) throw new Exception("Parámetros incompletos (nombre).");
		
		$nombre = trim($_GET['nombre']);
		
		if ($nombre == "") throw new Exception("Error leyendo parámetros (nombre).");
	}
	
	if ($accion == "modificar") {
		if (!isset($_GET['agente'])) throw new Exception("Parámetros incompletos (agente id).");
		
		$agente_id = (int)$_GET['agente'];
		
		if ($agente_id <= 0) throw new Exception("Error leyendo parámetros (agente id).");
	}
	
	if ($accion == "insertar" || $accion == "modificar") {
		if (!isset($_GET['sucursal'])) throw new Exception("Parámetros incompletos (sucursal).");
		if (!isset($_GET['unidad'])) throw new Exception("Parámetros incompletos (unidad).");
		
		$sucursal_id = (int)$_GET['sucursal'];
		$unidad_id = (int)$_GET['unidad'];
		
		if ($sucursal_id <= 0) throw new Exception("Error leyendo parámetros (sucursal).");
		if ($unidad_id <= 0) throw new Exception("Error leyendo parámetros (unidad).");
	}
	// ------------------ BLOQUE DE VALIDACIONES ------------------ //
	
	
	if ($accion == "insertar") {
		$age = Agente::insertar($conn, $nombre, $sucursal_id, $unidad_id);
		
		$mens = $age["mensaje"];
		if ($mens != "") throw new Exception("Error insertando agente. " . $mens);
		
		$ret["datos"] = $age["datos"];
	}
	
	if ($accion == "modificar") {
		$age = Agente::modificar($conn, $agente_id, $sucursal_id, $unidad_id);
		
		$mens = $age["mensaje"];
		if ($mens != "") throw new Exception("Error modificando agente. " . $mens);
	}
} catch (Exception $e) {
	$ret["mensaje"] = $e->getMessage();
}

echo json_encode($ret);

sqlsrv_close($conn);
?>