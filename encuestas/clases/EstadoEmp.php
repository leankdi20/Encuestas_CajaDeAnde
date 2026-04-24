<?php

class EstadoEmp {
  
  public static function getEstadosEmp($conn, $solo_activos = true)
  {
    $ret = array("mensaje" => "", 
                 "estados_emp" => null);
    $estados_emp = array();
    
    try {
      $cond = $solo_activos ? " where activo = 1 " : "";
      
      $cmd = " select estado_emp_id, nombre  
               from estados_emp " . $cond . "
               order by nombre ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($estados_emp, $row);
      }
      $ret["estados_emp"] = $estados_emp;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["estados_emp"] = null;
    }
    
    return $ret;
  }
  
}

?>