<?php

class Sucursal {
  
  public static function getSucursales($conn, $solo_activos = true)
  {
    $ret = array("mensaje" => "", 
                 "sucursales" => null);
    $sucursales = array();
    
    try {
      $cond = $solo_activos ? " where activo = 1 " : "";
      
      $cmd = " select sucursal_id, nombre, orden 
               from sucursales " . $cond . "
               order by orden ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($sucursales, $row);
      }
      $ret["sucursales"] = $sucursales;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["sucursales"] = null;
    }
    
    return $ret;
  }
  
  public static function getSucursalesUnidades($conn, $solo_activos = true)
  {
    $ret = array("mensaje" => "", 
                 "sucursales" => null);
    $sucursales = array();
    
    try {
      $cond = $solo_activos ? " where activo = 1 " : "";
      
      $cmd = " select sucursal_id, nombre, orden 
               from sucursales " . $cond . "
               order by orden ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
				$sucursal = $row;
				
				$sucursal["unidades"] = Unidad::getUnidadesPorSucursal($conn, $sucursal["sucursal_id"])["unidades"];
				
        array_push($sucursales, $sucursal);
      }
      $ret["sucursales"] = $sucursales;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["sucursales"] = null;
    }
    
    return $ret;
  }
  
}

?>