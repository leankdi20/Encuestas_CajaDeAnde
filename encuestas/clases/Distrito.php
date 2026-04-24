<?php

class Distrito {
  
  public static function getDistritos($conn, $canton_id)
  {
    $ret = array("mensaje" => "", 
                 "distritos" => null);
    $distritos = array();
    
    try {
      $cmd = " select distrito_id, nombre 
               from distritos 
               where canton_id = ? 
               order by distrito_id ";
      $datos = sqlsrv_query($conn, $cmd, array($canton_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($distritos, $row);
      }
      $ret["distritos"] = $distritos;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["distritos"] = null;
    }
    
    return $ret;
  }
  
}

?>