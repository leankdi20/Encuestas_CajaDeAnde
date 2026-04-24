<?php

class EncuestaDetMeta {
  
  public static function getEncuestaDetMeta($conn, $encuesta_det_id)
  {
    $ret = array("mensaje" => "", 
                 "encuesta_det_meta" => null);
    $encuesta_det_meta = array();
    
    try {
      $cmd = " select ltrim(rtrim(llave)) as llave, valor 
               from encuesta_det_meta 
               where encuesta_det_id = ? ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_det_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $encuesta_det_meta[$row["llave"]] = $row["valor"];
      }
      $ret["encuesta_det_meta"] = $encuesta_det_meta;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta_det_meta"] = null;
    }
    
    return $ret;
  }
  
}

?>