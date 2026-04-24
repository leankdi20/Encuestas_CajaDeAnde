<?php

class Puesto {
  
  public static function getPuestos($conn, $solo_activos = true)
  {
    $ret = array("mensaje" => "", 
                 "puestos" => null);
    $puestos = array();
    
    try {
      $cond = $solo_activos ? " where activo = 1 " : "";
      
      $cmd = " select puesto_id, nombre, otro 
               from puestos " . $cond . "
               order by otro, nombre ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($puestos, $row);
      }
      $ret["puestos"] = $puestos;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["puestos"] = null;
    }
    
    return $ret;
  }
  
  public static function getPuestosCaja($conn, $solo_activos = true)
  {
    $ret = array("mensaje" => "", 
                 "puestos" => null);
    $puestos = array();
    
    try {
      $cond = $solo_activos ? " where activo = 1 " : "";
      
      $cmd = " select puesto_id, nombre 
               from puestos_caja " . $cond . "
               order by nombre ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($puestos, $row);
      }
      $ret["puestos"] = $puestos;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["puestos"] = null;
    }
    
    return $ret;
  }
  
}

?>