<?php

class EncuestaDetTipoArchivo {
  
  public static function getEncuestaDetTiposArchivos($conn, $encuesta_det_id)
  {
    $ret = array("mensaje" => "", 
                 "encuesta_det_tipos_archivo" => null);
    $encuesta_det_tipos_archivo = array();
    
    try {
      $cmd = " select edta.tipo_archivo_id, ta.extension
               from encuesta_det_tipos_archivos edta 
			   inner join tipos_archivo ta on (ta.tipo_archivo_id = edta.tipo_archivo_id) 
               where edta.encuesta_det_id = ? 
			   order by edta.tipo_archivo_id ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_det_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
		array_push($encuesta_det_tipos_archivo, $row);
      }
      $ret["encuesta_det_tipos_archivo"] = $encuesta_det_tipos_archivo;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta_det_tipos_archivo"] = null;
    }
    
    return $ret;
  }
  
}

?>