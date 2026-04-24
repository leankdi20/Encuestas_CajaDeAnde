<?php

class Query {
  
  public static function dataEncuesta($conn, $encuesta_id) {
    return Procesos::encuesta($conn, $encuesta_id);
  }
  
  public static function listarSucursales($conn, $sucursal_id = 0) {
    $sucursales = Sucursal::getSucursales($conn)['sucursales'];
    
    foreach ($sucursales as $sucursal) {
      $sel = ($sucursal_id == $sucursal["sucursal_id"]) ? " selected " : "";
      
      echo "<option value='".$sucursal['sucursal_id']."' ".$sel.">".$sucursal['nombre']."</option>";
    }
  }
  
  public static function listarUnidades($conn, $sucursal_id, $unidad_id = 0) {
    $unidades = Unidad::getUnidadesPorSucursal($conn, $sucursal_id)["unidades"];
    
    foreach ($unidades as $unidad) {
      $sel = ($unidad_id == $unidad["unidad_id"]) ? " selected " : "";
      
      echo "<option value='".$unidad["unidad_id"]."' ".$sel.">".$unidad["nombre"]."</option>";
    }
  }
  
  public static function listarAgentes($conn, $agente_id = 0) {
    $agentes = Agente::getAgentes($conn)["agentes"];
    
    foreach ($agentes as $agente) {
      $sel = ($agente_id == $agente["agente_id"]) ? " selected " : "";
      
      echo "<option value='".$agente["agente_id"]."' ".$sel.">".$agente["nombre"]."</option>";
    }
  }
  
  public static function listarAgentesSucUni($conn, $sucursal_id, $unidad_id, $agente_id = 0) {
    $agentes = Agente::getAgentesSucUni($conn, $sucursal_id, $unidad_id)["agentes"];
    
    foreach ($agentes as $agente) {
      $sel = ($agente_id == $agente["agente_id"]) ? " selected " : "";
      
      echo "<option value='".$agente["agente_id"]."' ".$sel.">".$agente["nombre"]."</option>";
    }
  }
  
  public static function listarGestiones($conn, $unidad_id) {
    $gestiones = Gestion::getGestionesUnidad($conn, $unidad_id)["gestiones"];
    
    foreach ($gestiones as $gestion) {
      echo "<option value='".$gestion["gestion_id"]."'>".$gestion["nombre"]."</option>";
    }
  }
  
  public static function listarEstadosEmp($conn) {
    $estados_emp = EstadoEmp::getEstadosEmp($conn)['estados_emp'];
    
    foreach ($estados_emp as $estado_emp) {
      echo "<option value='".$estado_emp['estado_emp_id']."'>".$estado_emp['nombre']."</option>";
    }
  }
  
  public static function listarGeneros($conn) {
    $generos = Genero::getGeneros($conn)['generos'];
    
    foreach ($generos as $genero) {
      echo "<option value='".$genero['genero_id']."'>".$genero['nombre']."</option>";
    }
  }
  
  public static function listarPuestos($conn) {
    $puestos = Puesto::getPuestos($conn)['puestos'];
    
    foreach ($puestos as $puesto) {
      echo "<option value='".$puesto['puesto_id']."'>".$puesto['nombre']."</option>";
    }
  }
  
  public static function listarProvincias($conn, $imprimir = true) {
    $ret = "";
    
    $provincias = Provincia::getProvincias($conn)['provincias'];
    
    foreach ($provincias as $provincia) {
      if ($imprimir)
        echo "<option value='".$provincia['provincia_id']."'>".$provincia['nombre']."</option>";
      else
        $ret .= "<option value='".$provincia['provincia_id']."'>".$provincia['nombre']."</option>";
    }
    
    if (!$imprimir) return $ret;
  }
  
  public static function listarCantones($conn, $provincia_id) {
    $cantones = Canton::getCantones($conn, $provincia_id)['cantones'];
    
    foreach ($cantones as $canton) {
      echo "<option value='".$canton['canton_id']."'>".$canton['nombre']."</option>";
    }
  }
  
  public static function listarDistritos($conn, $canton_id) {
    $distritos = Distrito::getDistritos($conn, $canton_id)['distritos'];
    
    foreach ($distritos as $distrito) {
      echo "<option value='".$distrito['distrito_id']."'>".$distrito['nombre']."</option>";
    }
  }
  
  public static function listarPuestosCaja($conn, $imprimir = true) {
    $ret = "";
    
    $puestos = Puesto::getPuestosCaja($conn)['puestos'];
    
    foreach ($puestos as $puesto) {
      if ($imprimir)
        echo "<option value='".$puesto['puesto_id']."'>".$puesto['nombre']."</option>";
      else
        $ret .= "<option value='".$puesto['puesto_id']."'>".$puesto['nombre']."</option>";
    }
    
    if (!$imprimir) return $ret;
  }
  
  public static function listarEstCiviles($conn) {
    $estados = EstCivil::getEstados($conn)['estados'];
    
    foreach ($estados as $estado) {
      echo "<option value='".$estado['est_civil_id']."'>".$estado['nombre']."</option>";
    }
  }
  
  public static function listarDireccionesEnvio($conn, $incluye_domicilio = 1, $imprimir = true) {
    $ret = "";
    
    $direcciones_envio = DireccionEnvio::getDireccionesEnvio($conn, $incluye_domicilio)['direcciones_envio'];
    
    foreach ($direcciones_envio as $direccion_envio) {
      if ($imprimir)
        echo "<option value='".$direccion_envio['direccion_id']."'>".$direccion_envio['nombre']."</option>";
      else
        $ret .= "<option value='".$direccion_envio['direccion_id']."'>".$direccion_envio['nombre']."</option>";
    }
    
    if (!$imprimir) return $ret;
  }
  
}

?>