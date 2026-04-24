<?php

class Provincia {
  
  public static function getProvincias($conn)
  {
    $ret = array("mensaje" => "", 
                 "provincias" => null);
    $provincias = array();
    
    try {
      $cmd = " select provincia_id, nombre 
               from provincias 
               order by provincia_id ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($provincias, $row);
      }
      $ret["provincias"] = $provincias;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["provincias"] = null;
    }
    
    return $ret;
  }
  
}

?>