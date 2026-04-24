<?php

class EncuestaDetOpciones {
  
  public static function getEncuestaDetOpciones($conn, $encuesta_det_id, $dicc = false)
  {
    $ret = array("mensaje" => "", 
                 "encuesta_det_opciones" => null);
    $encuesta_det_opciones = array();
    
    try {
      $cmd = " select encuesta_det_opcion_id, texto, otro, explicacion, bloquea_otras, correos, 
											coalesce(seleccionada, 0) as seleccionada, seleccionada, orden, activo 
               from encuesta_det_opciones 
               where encuesta_det_id = ? 
               order by otro, orden ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_det_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      if ($dicc) {
        while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
          $encuesta_det_opciones[$row["encuesta_det_opcion_id"]] = $row;
        }
      } else {
        while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
          array_push($encuesta_det_opciones, $row);
        }
      }
      
      $ret["encuesta_det_opciones"] = $encuesta_det_opciones;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta_det_opciones"] = null;
    }
    
    return $ret;
  }
  
}

?>