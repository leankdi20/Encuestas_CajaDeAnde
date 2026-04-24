<?php
include_once "clases/includes_clases.php";
include_once "config.php";
include_once "Funciones.php";
include_once "Variables.php";

switch ($_GET['accion']) {
  case "leerSocio":
		$ret = [];
		$cedula = $_GET['cedula'];
		$ret["nombre"] = WebServices::nombrePorCedula($cedula);
		$ret["correo"] = WebServices::correoPorCedula($cedula);
    echo json_encode($ret);
    break;
  case "cambiarSucursal":
    cargarUnidadesPorSucursal($conn, $_GET['sucursal']);
    break;
  case "cambiarUnidadAgente":
    cargarAgentesPorUnidad($conn, $_GET['sucursal'], $_GET['unidad']);
    break;
  case "cambiarUnidadGestion":
    cargarGestionesPorUnidad($conn, $_GET['unidad']);
    break;
  case "cambiarProvincia":
    Query::listarCantones($conn, $_GET['provincia']);
    break;
  case "cambiarCanton":
    Query::listarDistritos($conn, $_GET['canton']);
    break;
}

function cargarUnidadesPorSucursal($conn, $sucursal_id) {
  try {
    $unidades = Unidad::getUnidadesPorSucursal($conn, $sucursal_id)["unidades"];
    
    foreach ($unidades as $unidad) {
      echo "<option value='".$unidad["unidad_id"]."'>".$unidad["nombre"]."</option>";
    }
  } catch (Exception $e) {
    return  "";
  }
}

function cargarAgentesPorUnidad($conn, $sucursal_id, $unidad_id) {
  try {
    $agentes = Agente::getAgentesSucUni($conn, $sucursal_id, $unidad_id)["agentes"];
    
    foreach ($agentes as $agente) {
      echo "<option value='".$agente["agente_id"]."'>".$agente["nombre"]."</option>";
    }
  } catch (Exception $e) {
    return  "";
  }
}

function cargarGestionesPorUnidad($conn, $unidad_id) {
  try {
    $gestiones = Gestion::getGestionesUnidad($conn, $unidad_id)["gestiones"];
    
    foreach ($gestiones as $gestion) {
      echo "<option value='".$gestion["gestion_id"]."'>".$gestion["nombre"]."</option>";
    }
  } catch (Exception $e) {
    return  "";
  }
}

function cargarCantonesPorProvincia($conn, $provincia_id) {
  try {
    $cantones = Canton::getCantones($conn, $provincia_id)["cantones"];
    
    foreach ($cantones as $canton) {
      echo "<option value='".$canton["canton_id"]."'>".$canton["nombre"]."</option>";
    }
  } catch (Exception $e) {
    return  "";
  }
}

function cargarDistritosPorCanton($conn, $canton_id) {
  try {
    $distritos = Distrito::getDistritos($conn, $canton_id)["distritos"];
    
    foreach ($distritos as $distrito) {
      echo "<option value='".$distrito["distrito_id"]."'>".$distrito["nombre"]."</option>";
    }
  } catch (Exception $e) {
    return  "";
  }
}

//sqlsrv_close($conn);
?>