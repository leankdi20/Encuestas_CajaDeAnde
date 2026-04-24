<?php

class Genero {
  
  public static function getGeneros($conn, $solo_activos = true)
  {
    $ret = array("mensaje" => "", 
                 "generos" => null);
    $estados_emp = array();
    
    try {
      $cond = $solo_activos ? " where activo = 1 " : "";
      
      $cmd = " select genero_id, nombre, otro 
               from generos " . $cond . "
               order by otro, nombre ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($estados_emp, $row);
      }
      $ret["generos"] = $estados_emp;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["generos"] = null;
    }
    
    return $ret;
  }
  
}

?>