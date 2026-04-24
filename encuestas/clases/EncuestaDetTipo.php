<?php

class EncuestaDetTipo {
  
  public static function getEncuestaDetTipo($conn, $tipo_id)
  {
    $ret = array("mensaje" => "", 
                 "encuesta_det_tipo" => null);
    $encuesta_det_tipo = null;
    
    try {
      $cmd = " select tipo_id, nombre 
               from encuesta_det_tipo 
               where tipo_id = ? ";
      $datos = sqlsrv_query($conn, $cmd, array($tipo_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $ret["encuesta_det_tipo"] = $row;
      }
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta_det_tipo"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuestaDetTipos($conn)
  {
    $ret = array("mensaje" => "", 
                 "encuesta_det_tipos" => null);
    $encuesta_det_tipos = array();
    
    try {
      $cmd = " select tipo_id, nombre 
               from encuesta_det_tipo 
               order by tipo_id ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($encuesta_det_tipos, $row);
      }
      $ret["encuesta_det_tipos"] = $encuesta_det_tipos;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta_det_tipos"] = null;
    }
    
    return $ret;
  }
  
}

?>