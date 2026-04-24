<?php

class Unidad {
  
  public static function getUnidadesPorSucursal($conn, $sucursal_id, $solo_activos = true)
  {
    $ret = array("mensaje" => "", 
                 "unidades" => null);
    $sucursales = array();
    
    try {
      $cond = $solo_activos ? " and u.activo = 1 " : "";
      
      $cmd = " select u.unidad_id, u.nombre 
               from sucursales s 
               inner join sucursal_unidades su on (su.sucursal_id = s.sucursal_id) 
               inner join unidades u on (u.unidad_id = su.unidad_id " . $cond . ") 
               where s.sucursal_id = ? 
               order by u.nombre ";
      $datos = sqlsrv_query($conn, $cmd, array($sucursal_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($sucursales, $row);
      }
      $ret["unidades"] = $sucursales;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["unidades"] = null;
    }
    
    return $ret;
  }
  
}

?>