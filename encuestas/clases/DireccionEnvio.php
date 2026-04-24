<?php

class DireccionEnvio {
  
  public static function getDireccionesEnvio($conn, $incluye_domicilio = 1)
  {
    $ret = array("mensaje" => "", 
                 "direcciones_envio" => null);
    $direcciones_envio = array();
    $cond = "";
    
    try {
      if ($incluye_domicilio == 0) $cond = " where es_sucursal = 1 ";
      
      $cmd = " select direccion_id, nombre, es_sucursal 
               from direcciones_envio " . $cond. "
               order by es_sucursal desc, nombre asc ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($direcciones_envio, $row);
      }
      $ret["direcciones_envio"] = $direcciones_envio;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["direcciones_envio"] = null;
    }
    
    return $ret;
  }
  
}

?>