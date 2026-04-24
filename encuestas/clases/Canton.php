<?php

class Canton {
  
  public static function getCantones($conn, $provincia_id)
  {
    $ret = array("mensaje" => "", 
                 "cantones" => null);
    $cantones = array();
    
    try {
      $cmd = " select canton_id, nombre 
               from cantones 
               where provincia_id = ? 
               order by canton_id ";
      $datos = sqlsrv_query($conn, $cmd, array($provincia_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($cantones, $row);
      }
      $ret["cantones"] = $cantones;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["cantones"] = null;
    }
    
    return $ret;
  }
  
}

?>