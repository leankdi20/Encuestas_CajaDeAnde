<?php

class EstCivil {
  
  public static function getEstados($conn, $solo_activos = true)
  {
    $ret = array("mensaje" => "", 
                 "estados" => null);
    $estados = array();
    
    try {
      $cond = $solo_activos ? " where activo = 1 " : "";
      
      $cmd = " select est_civil_id, nombre 
               from est_civiles " . $cond . "
               order by est_civil_id ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($estados, $row);
      }
      $ret["estados"] = $estados;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["estados"] = null;
    }
    
    return $ret;
  }
  
}

?>